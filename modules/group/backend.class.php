<?php
/**
* Backend controller for the user module.
* 
* @uses: BackendController
* @author: Elias Müller
* @version: 0.4
* @since: chispa 0.5a
* @package: core
* @subpackage: backend
*/

define('SALT_LENGTH', 15);

require_once("classes/backend_controller.class.php");

class Groups extends BackendController {
	/**
    * Returns the section of the main menu, which is currently active
    * @return string The name of the section
    */
    protected function getSection() {
        return 'config';
    }

	/**
	 * generate the title for the page
	 *
	 * @param string title Existing title (i.e. generated by modules)
 	 * @return string Title for the page
	 * @author Elias Müller
	 **/
	protected function generateTitle($title = '') {
		switch ($this->s->action) {
			case 'list':
				$title = 'Gruppen';
				break;
		}
		return parent::generateTitle($title);
	}
	
	/**
    * Build site content depending on requested action.
    */  
    protected function buildSite() {
        switch ($this->s->action) {
			case 'delete':
				$group = Group::getInstance($this->s->element);
				$content = 'Möchtest du die Gruppe <strong>'.$group->name.'</strong> wirklich löschen?<input type="hidden" value="form/group/delete/'.$this->s->element.'.html" />';
				break;
			case 'list':
                $content = $this->listGroups();
                break;
			case 'create':
				$content = $this->getGroupForm();
                break; 
        }
		parent::buildSite($content);
    }

    /**
    * Handle form action
    */
    protected function formAction() {
        switch ($this->s->action) {
			case "activate":
				if ($this->s->element != '') {
					$group = Group::getInstance($this->s->element);
					$rights = $group->rights;
					if (current($rights) == '')
						$rights = array();
						
					if ($this->vars['option'] == 'set' && !in_array($this->vars['right'], $rights)) {
						$rights[] = $this->vars['right'];
					} elseif ($this->vars['option'] == 'remove') {
						foreach ($rights AS $key => $right) {
							if ($right == $this->vars['right'])
								unset($rights[$key]);				
						}
					}
					$group->rights = $rights;
					if ($group->save())
						return true;
				}
				Error::addWarning('Fehler: Es wurde kein Modul ausgewählt!');
				break;
			case 'create':
				$group = Group::getInstance();
				$group->name = $this->vars['name'];
				if ($group->save()) {
					Error::addMessage('Die Benutzergruppe wurde erfolgreich angelegt!');
					$this->form['reload'] = array('group' => array('groups'));
					return true;
				}
			case 'delete':
				$group = Group::getInstance($this->s->element);
				$name = $group->name;
				if ($group->delete()) {
					Error::addMessage('Die Gruppe '.$name.' wurde erfolgreich gelöscht!');
					$this->form['reload'] = array('group' => array('groups'));
				}
				break;
			case 'reload':
				switch($this->s->element) {
					case 'groups': // groups table
						echo $this->listGroups();
						break;
					default:
						Error::addError('Fehler: Element nicht gefunden!');
						return false;
				}
				return true;
				break;
        }
		return false;
    }
	
	/**
     * Create group/rights list
	 *
	 * @return string HTML-Code for group list
     */
    private function listGroups() {
        $tpl = new Template('group');
        		
		// get all rights of the active modules
		$modules = $this->s->modules;       
        foreach ($modules AS $module => $name) {
            $cur_module = $this->s->loadModule($module);
			$r = $cur_module->registerRights();
			if (is_array($r)) {
				$set = false;
				foreach ($r AS $key => $value) {
					$r_names[$module][$key] = $value; // names of the rights
					$set = true;
				}
				if ($set)
					$m_names[$module] = $name; 
			}
			is_array($r_names[$module]) && ksort($r_names[$module]);
		}

		// get all rights of the user groups
		$groups = Group::getAll();
        
        foreach($groups AS $g) {		
			foreach ($g->rights AS $right) {
				$r = explode(':', $right);
				$r_groups[$r[0]][$r[1]][$g->id] = true; 
			}
        }

        $tpl->assign('groups', $groups);
		$tpl->assign('count', count($groups)+1);
		$tpl->assign('modules', $m_names);
		$tpl->assign('names', $r_names);
		$tpl->assign('rights', $r_groups);
		$tpl->assign('call', $this->s->post['call']);
        $tpl->assign('permissions', array('activate' => $this->s->user->hasRights("group:activate"), 'delete' => $this->s->user->hasRights("group:delete"), 'create' => $this->s->user->hasRights("group:create")));
		$tpl->assign('permission', $this->s->user->hasRights('group:activate'));
        return $tpl->fetch('rights_table.html');
    }
	
	/**
	 * generate form: change group info
	 *
	 * @return string html code for the form
	 * @author Elias Müller
	 **/
	private function getGroupForm() {		
		$path = ($this->s->element != '') ? $this->s->action.'/'.$this->s->element.'.html' : $this->s->action;
		
		$tpl = new Template('group');
		$tpl->assign('path', $path);
		return $tpl->fetch('form_group.html');
	}
}
?>