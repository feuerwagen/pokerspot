<?php
/**
* Backend controller for the message module.
* 
* @uses: BackendController
* @author: Elias Müller
* @version: 0.1
* @since: chispa 0.5a
* @package: core
* @subpackage: backend
*/

require_once("classes/backend_controller.class.php");

class Messages extends BackendController {	
	/**
    * Returns the section of the main menu, which is currently active
    * @return string The name of the section
    */
    protected function getSection() {
		return 'overview';
    }

    /**
    * Build site content depending on requested action.
    */  
    protected function buildSite() {
        switch ($this->s->action) {
			case 'update':
			case 'send':
			case 'reply':
				$content = $this->getForm();
				break;
            case 'list':
				$tpl = new Template('message');
				$m = $this->listMessages();
				
				if ($m !== false) {
					$tpl->assign('messages', $m['messages']);
					$tpl->assign('current', $m['current']);
					$tpl->assign('read', $m['read']);
					$tpl->assign('user', $this->s->user);
					$content = $tpl->fetch('messages.html');
				} else
					$content = '<h2>Keine Nachrichten vorhanden!</h2>';
                break;
			case 'show':
				$tpl = new Template('message');
				$tpl->assign('m', Message::getInstance($this->s->element));
				$tpl->assign('user', $this->s->user);
				$content = $tpl->fetch('message.html');
				break;
			case 'delete':
				$m = Message::getInstance($this->s->element);
				$content = 'Soll die Nachricht <strong>„'.$m->subject.'“</strong> wirklich gelöscht werden?<input type="hidden" value="form/message/delete/'.$this->s->element.'.html" />';
				break;
        }
        parent::buildSite($content);
    }

    /**
    * Return the buttons for the right toolbar used by this module.
	*
    * @return array The buttons
    */
    public function getToolbarButtons($section) {	
        switch($section) {
			default:
				$buttons = array(
					array(
						'title' => 'Neue Nachricht',
						'class' => 'new_message',
						'action' => 'send',
						'params' => '?width=480&height=520',
						'dialog' => 'send'
					),
				);
				break;
		}
		return $buttons;
    }

	/**
    * Handle form action
    */
    protected function formAction() {
		$this->escapeFormVars();
		
        switch ($this->s->action) {
			case 'send':
				$message = new Message();
				$message->text = $this->vars['text'];
				$message->subject = $this->vars['subject'];
				$rec = array();
				foreach ($this->vars['user'] AS $r)
					$rec[] = User::getInstance($r);
				$message->receiver = $rec;
				$message->sender = $this->s->user;
				if ($message->save()) {
					Error::addMessage('Die Nachricht wurde verschickt!');
					$this->form['reload'] = array('message' => array('sidebar'));
					return true;
				}
				break;
			case 'update':
				$message = Message::getInstance($this->s->element);
				$message->text = $this->vars['text'];
				$message->subject = $this->vars['subject'];
				if ($message->save()) {
					Error::addMessage('Die Nachricht wurde geändert!');
					$this->form['reload'] = array('message' => array('u_message'));
					return true;
				}
				break;
			case 'reply':
				$r = Message::getInstance($this->s->element);
				$message = new Message();
				$message->text = $this->vars['text'];
				$message->subject = $this->vars['subject'];
				$message->receiver = $r->sender;
				$message->sender = $this->s->user;
				$message->replyto = $this->s->element;
				if ($message->save()) {
					Error::addMessage('Die Nachricht wurde verschickt!');
					$this->form['reload'] = array('message' => array('u_message'));
					return true;
				}
				break;
			case 'mark':
				if ($this->s->element != '') {
					$m = Message::getInstance($this->s->element);
					// mark all messages in thread as (un-)read
					if ($this->vars['messages'] == 'all') {
						if (is_array($m->replies)) {
							foreach ($m->replies AS $r) {
								if ($r->receiver == $this->s->user) {
									$r->read = ($this->vars['option'] == 'read') ? true : false;
									if (!$r->save()) {
										Error::addError('Interner Fehler: Nachricht konnte nicht gespeichert werden!');
										return false;
									}
								}
							}
						}
						$this->form['reload'] = array('message' => array('u_message'));
					} else {
						$this->form['reload'] = array('message' => array('sidebar', 'bb_messages'));
					}
					
					// mark current message as (un-)read
					$m->read = ($this->vars['option'] == 'read') ? true : false;
					if ($m->save()) {
						return true;
					} else {
						Error::addError('Interner Fehler: Nachricht konnte nicht gespeichert werden!');
						return false;
					}		
				}
				Error::addError('Es konnte keine Nachricht gefunden werden!');
				break;
			case 'delete':
				$m = Message::getInstance($this->s->element);
				if($m->delete()) {
					Error::addMessage('Die Nachricht wurde gelöscht!');
					$this->form['reload'] = array('message' => array('sidebar', 'u_message'));
					return true;
				}
				break;
			case 'reload':
				switch ($this->s->element) {
					case 'sidebar':
						$tpl = new Template('message');
						$this->s->resetParams();
						$m = $this->listMessages($this->s->element);

						$tpl->assign('messages', $m['messages']);
						$tpl->assign('current', $m['current']);
						$tpl->assign('read', $m['read']);
						$tpl->assign('user', $this->s->user);
						echo $tpl->fetch('message_list.html');
						break;
					case 'u_message':
						$tpl = new Template('message');
						$this->s->resetParams();
						$tpl->assign('m', Message::getInstance($this->s->element));
						$tpl->assign('user', $this->s->user);
						echo $tpl->fetch('message.html');
						break;
					case 'bb_messages':
						echo $this->blackboardTable();
					default:
						echo '';
				}
				return true;
        }
		return false;
    }
    
	// hooks
	
	/**
	 * add unread messages to blackboard
	 *
	 * @return array
	 * @author Elias Müller
	 **/
	public function hookBlackboard() {
		$elem['priority'] = 2;
		$elem['id'] = 'messages';
		$elem['content'] = $this->blackboardTable();
		return $elem;
	}
	
    // module-specific methods start here

	/**
	 * add unread messages to blackboard
	 *
	 * @return string
	 * @author Elias Müller
	 **/
	public function blackboardTable() {
		$tpl = new Template('message');		
		$messages = Message::getUnread();
		
		if (count($messages) > 0) { 
			$tpl->assign('messages', $messages);
			return $tpl->fetch('messages_unread.html');
		}
	}
	
	/**
	 * list all messages for current user
	 *
	 * @return array
	 * @author Elias Müller
	 **/
	private function listMessages($active = false) {
		$m = Message::getAllForUser($this->s->user);
		
		if ($m !== false) {
			$c = current($m);
			$read = array();
			$found = false;
			foreach ($m AS $k => $message) {
				$read[$message->id] = ($message->read === false && $message->sender !== $this->s->user) ? false : true;
			
				// find first message thread with unread messages or current selected message (for reload)
				if ($message->id === $active || ($found === false && $read[$message->id] === false)) {
					$c = $message;
					$found = true;
				}
			
				// determine unread state of message thread
				if (is_array($message->replies)) {
					foreach ($message->replies AS $r) {
						if ($r->read === false && $r->sender !== $this->s->user) {
							if ($found === false) {
								$c = $message;
								$found = true;
							}
							$read[$message->id] = false;
						}
					}
				}
			}
		
			$ret['messages'] = $m;
			$ret['read'] = $read;
			$ret['current'] = $c;	
			return $ret;
		}
		return false;
	}

	/**
	 * generate form: new message
	 *
	 * @return string html code for the form
	 * @author Elias Müller
	 **/
	private function getForm() {
		$tpl = new Template('message');
		$el = ($this->s->element != '') ? $this->s->element : false;
		switch ($this->s->action) {
			case 'update':
				$m = Message::getInstance($this->s->element);
				break;
			case 'reply':
				$r = Message::getInstance($this->s->element);
				$m = new Message();
				$m->subject = 'Re: '.$r->subject;
				$m->receiver = ($r->sender === $this->s->user) ? $r->receiver : $r->sender;
				if ($r->replyto != 0)
					$el = $r->replyto;
				break;
		}				

		$tpl->assign('message', $m);
		$tpl->assign('action', $this->s->action);
		$tpl->assign('id', $el);
		$tpl->assign('user', User::getActiveUsers());
		return $tpl->fetch('form_message.html');
	}
}
?>