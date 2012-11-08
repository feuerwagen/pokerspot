<?php
/**
 * receive new mails from pop3 mailbox: get all messages, parse via mime_parser class and delete from mail server 
 *
 * @package default
 * @author Elias Müller
 **/
require_once('classes/mail/pop3.php');
require_once('classes/mail/sasl.php');
require_once('classes/mail/mime_parser.php');
require_once('classes/mail/rfc822_addresses.php');

class MailReceive {
	/**
	 * instance of the pop3 class
	 *
	 * @var object
	 **/
	private $server;
	
	/**
	 * number of new messages
	 *
	 * @var int
	 **/
	private $messages = 0;
	
	/**
	 * current message number
	 *
	 * @var int
	 **/
	private $current = 0;
	
	/**
	 * current connection name
	 *
	 * @var string
	 **/
	private $connection;
	
	/**
	 * instance of mime parser
	 *
	 * @var object
	 **/
	private $mime;
	
	/**
	 * config vars
	 *
	 * @var array
	 **/
	private $config;
	
	/**
	 * set all params from config file
	 */
	public function __construct() {
		stream_wrapper_register('pop3', 'pop3_stream'); 
		
		$s = cBootstrap::getInstance();
		$this->config = $s->getConfig('mail');
		$this->server = new pop3_class;
		$this->server->hostname = $this->config['host'];
	} 
	
	public function __destruct() {
		$this->server->Close();
	}
	
	public function __get($name) {
		if ($name == 'messages' || $name == 'current')
			return $this->$name;
	}
	
	/**
	 * get all new mail from pop3 server
	 *
	 * @return int number of messages
	 * @author Elias Müller
	 **/
	public function query() {
		if(($error=$this->server->Open()) == "") { // establish connection
			if(($error=$this->server->Login($this->config['user'],$this->config['password'])) == "") { // login
				if(($error=$this->server->Statistics($messages,$size)) == "") { // get statistics (message count and total size)
					$this->messages = $messages;
					if($messages > 0) {
						$this->server->GetConnectionName($connection_name);
						$this->connection = $connection_name;
						$this->mime = new mime_parser_class;
						$this->mime->decode_bodies = 1; // Set to 0 for not decoding the message bodies
						
						return true;
					}
				}
			}
		}
		if($error != "")
			Error::addError("Fehler beim Mail-Abruf: ".HtmlSpecialChars($error), true);
		return false;
	}
	
	/**
	 * get current message contents
	 *
	 * @return array
	 * @author Elias Müller
	 **/
	function get() {
		if ($this->messages > 0) {
			$params = array(
				'File' => 'pop3://'.$this->connection.'/'.$this->current
			);

			if ($this->mime->Decode($params, $decoded)) {
				if ($this->mime->Analyze($decoded[0], $results))
					return $results;
				else
					Error::addError('MIME message analyse error: '.$this->mime->error, true);
			} else
				Error::addError('MIME message decoding error: '.HtmlSpecialChars($this->mime->error), true);
		}
		return false;
	}
	
	/**
	 * delete current message from server
	 *
	 * @return bool
	 * @author Elias Müller
	 **/
	public function delete() {
		if(($error=$this->server->DeleteMessage($this->current))=="")
			return true;
		else
			Error::addWarning('Fehler beim Mail-Abruf: Eine Nachricht konnte nicht gelöscht werden', true);
		return false;
	}
	
	/**
	 * proceed to next message
	 *
	 * @return bool false if no more messages
	 * @author Elias Müller
	 **/
	public function next() {
		if ($this->current < $this->messages)
			$this->current++;
		else
			return false;
		return true;
	}
	
	/**
	 * close server connection and delete marked messages
	 *
	 * @return bool
	 * @author Elias Müller
	 **/
	public function close() {
		if (($error=$this->server->Close()) == "")
			return true;
		else {
			Error::addError("Fehler beim Trennen der Verbindung zum Mail-Server: ".HtmlSpecialChars($error), true);
			return false;
		}
	}
} // END class 
?>