<?php
/**
* Manage user operations and permission checks.
* 
* @author: Elias Müller
* @version: 0.2
* @since: chispa 0.5a
* @package: core
* @subpackage: backend
*/
require_once('classes/c_bootstrap.class.php');
require_once('modules/poker/poker_table/poker_table.class.php');

class Message {
    /**
    * objects of this class (only one per id!)
    */
    static public $objects = array();
    
    /**
    * object id (from db) 
    */
    private $id = false;
    
    /**
    * array of object information
    *
    * @var array
    */
    private $info = array();
	
    public function __construct() {
    }
    
    /**
    * get object of this class or create new
    * 
    * @param string $username the username (sic!)
    * @return User reference to the user object
    */
    static public function getInstance($id) {
        if(!isset(self::$objects[$id])) {
            self::$objects[$id] = new Message();
            self::$objects[$id]->load($id);
            $b = self::$objects[$id];
        } else {
            $b = self::$objects[$id];
        }
        return $b;
    }
    
    /**
    * magic: return object information
    */
    public function __get($field) {
		if ($field == 'id')
			return $this->id;
        return $this->info[$field];
    }

	/**
    * magic: change information
    */
    public function __set($field, $value) {
        $this->info[$field] = $value;
    }

    /**
     * read the object information from the db
     */
    private function load($id) {
		$db = new DB();
		
		$sql = "SELECT m.text, m.subject, m.created, m.read, u1.username AS sender, m.idreply, m.idmessage, m.idrecvr, m.recvr
				  FROM messages AS m
			 LEFT JOIN users AS u1 ON m.iduser = u1.iduser
				 WHERE m.idmessage = ".$id."
					OR m.idreply = ".$id."
			  ORDER BY m.created ASC";
		$result = $db->query($sql);
		
		if ($result->length() > 0) {
			do {
				if ($result->idmessage == $id) { // start message
					$this->info["subject"] = $result->subject;
					$this->info["text"] = $result->text;
					// Systeminfo
					$this->info["sender"] = User::getInstance($result->sender);
					$this->info["type"] = $result->recvr;
					switch($result->recvr) {
						case 'user':
							$this->info["receiver"] = User::getInstanceForId($result->idrecvr);
							break;
						case 'poker':
							$this->info["receiver"] = PokerTable::getInstance($result->idrecvr);
							break;
					}
					$this->info["replyto"] = $result->idreply;
					if ($result->read != '0000-00-00 00:00:00') {
						$r = explode(' ', $result->read);
						$this->info['read']['date'] = new Date($r[0], 'Y-m-d');
						$this->info['read']['time'] = $r[1];
					} else {
						$this->info['read'] = false;
					}
					$created = explode(' ', $result->created);
					$this->info["created"]['date'] = new Date($created[0], 'Y-m-d');
					$this->info["created"]['time'] = $created[1];
					$this->id = $result->idmessage;
				} else { // following messages
					$this->info['replies'][] = Message::getInstance($result->idmessage);
				}
			} while ($result->next());
			return true;
		}
		return false;
    }
    
    /**
     * delete the object
     */
    public function delete() {
		$db = DB::getInstance();
		
		if (!is_array($this->info['replies'])) {
		 	$sql = "DELETE FROM messages
					 WHERE idmessage = ".$this->id;
			if ($db->query($sql) === true)
				return true;
		} else {
			Error::addWarning('Nachricht kann nicht gelöscht werden: Noch Antworten vorhanden!');
		}
		return false;
    }
    
    /**
     * save the object information
     */
    public function save() {
		$db = DB::getInstance();

		if ($this->id === false) {
			if (is_array($this->info["receiver"])) {
				foreach ($this->info["receiver"] AS $recvr) {
					$sql = "INSERT INTO messages
									SET subject = '".$this->info['subject']."',
										text = '".$this->info['text']."',
										created = NOW(),
										idrecvr = ".$recvr->id.",
										recvr = '".$this->info['type']."',
										idreply = '".$this->info['replyto']."',
										iduser = ".$this->info["sender"]->id;
					$id = $db->query($sql);
					if ($id == 0)
						return false;
				}
			} else {
				$sql = "INSERT INTO messages
								SET subject = '".$this->info['subject']."',
									text = '".$this->info['text']."',
									created = NOW(),
									idrecvr = '".$this->info["receiver"]->id."',
									recvr = '".$this->info['type']."',
									idreply = '".$this->info['replyto']."',
									iduser = '".$this->info["sender"]->id."'";
				$id = $db->query($sql);
			}	
		} else {
			$read = ($this->info['read'] === true) ? "m.read = NOW()," : (($this->info['read'] === false) ? "m.read = 0," : '');
			$sql = "UPDATE messages AS m
					   SET m.subject = '".$this->info['subject']."',
						   $read
						   m.text = '".$this->info['text']."'							
					 WHERE m.idmessage = ".$this->id;
			$id = $db->query($sql);
		}
    	if ($id != 0) {
			if ($this->id === false) {
				$this->id = $id;
				self::$objects[$id] = $this;
			}
			return true;
		}
		return false;
	}

	/**
	 * get all messages for one user
	 *
	 * @return array
	 * @author Elias Müller
	 **/
	static public function getAllForUser($user) {
		$db = new DB();
		$messages = array();
		
		$sql = "SELECT idmessage
				  FROM messages
				 WHERE (iduser = ".$user->id."
				    OR idsender = ".$user->id.")
				   AND idreply = 0
				   AND m.type = 'user'
			  ORDER BY idmessage DESC";
		$result = $db->query($sql);
		
		if ($result->length() > 0) {
			do {
				$messages[$result->idmessage] = Message::getInstance($result->idmessage);
			} while ($result->next());
			return $messages;
		}
		return false;
	}
	
	/**
	 * get all unread messages for one user
	 *
	 * @return array
	 * @author Elias Müller
	 **/
	static public function getUnread() {
		$s = cBootstrap::getInstance();
		$db = new DB();
		$messages = array();
		
		$sql = "SELECT m.idmessage
				  FROM messages AS m
				 WHERE m.iduser = ".$s->user->id."
				   AND m.read = 0
				   AND m.recvr = 'user'
			  ORDER BY m.idmessage DESC";
		$result = $db->query($sql);
		
		if ($result->length() > 0) {
			do {
				$messages[$result->idmessage] = Message::getInstance($result->idmessage);
			} while ($result->next());
		}
		return $messages;
	}
}
?>