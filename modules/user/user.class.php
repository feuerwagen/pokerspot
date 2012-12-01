<?php
/**
* Manage user operations and permission checks.
* 
* @author: Elias Müller
* @version: 0.3
* @since: chispa 0.5a
* @package: core
* @subpackage: backend
*/

class User {
    /**
    * objects of this class (only one per id!)
    */
    static public $objects = array();
    
    /**
    * object id (from db) 
    */
    private $id = false;
	private $user;
    
    /**
    * array of object information
    *
    * @var array
    */
    private $info = array();
    
    /**
    * private because of singleton 
    */
    private function __construct() {
    }

	/**
    * get object of this class or create new
    * 
    * @param string $id the object id
    * @return object reference to the object
    */
    static public function getInstance($user) {
        if(!isset(self::$objects[$user])) {
            self::$objects[$user] = new User();
            self::$objects[$user]->load($user);
            $user = self::$objects[$user];
        } else {
            $user = self::$objects[$user];
        }
        return $user;
    }

    /**
    * get object of this class or create new
    * 
    * @param string $id the object id
    * @return object reference to the object
    */
    static public function getInstanceForId($id) {
        $db = DB::getInstance();
        $sql = "SELECT username FROM users WHERE iduser = ".$id;
        $result = $db->query($sql);

        if ($result->length() > 0) {
            return self::getInstance($result->username);
        }
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
    private function load($user) {
		$this->user = $user;
        $db = DB::getInstance();
        $sql = "SELECT *
                  FROM users
			 LEFT JOIN groups ON users.idstatus = groups.idstatus
                 WHERE users.username = '$user'";
        $result = $db->query($sql);
        
        if ($result->length() > 0) {
            // rights array
            $rights = explode(",", $result->rights);
            // general information
            $this->info = array(
                "username" => $result->username,
                "realname" => $result->realname,
                "email" => $result->email,
                "status" => $result->name,
				"idstatus" => $result->idstatus,
				"current_login" => $result->current_login,
				"last_login" => $result->last_login,
                "rights" => $rights,
				"password" => $result->password
            );
			$this->id = $result->iduser;
            return true;
        }
		return false;
    }
    
    /**
    * delete the object information from the db
    */
    public function delete() {
        if ($this->id !== false) {
            // no real delete, just set user to inactive & change password
            // so that no inconsistency is created
            $db = DB::getInstance();
            $sql = "UPDATE users
                    SET idstatus = 0,
                        password = sha1(curdate())
                    WHERE iduser = '".$this->id."'";
            $result = $db->query($sql); 
        } else
            return false;
        return $result;                     
    }
    
    /**
    * save the object information
    */
    public function save() {
        $db = DB::getInstance();

		if ($this->id === false) {
			$sql = "INSERT INTO users
							SET idstatus = '".$this->info['idstatus']."',
								username = '".$this->user."',
								password = '".$this->info['password']."',
								realname = '".$this->info['realname']."',
								email = '".$this->info['email']."'";
		} else {
			$sql = "UPDATE users
					   SET idstatus = '".$this->info['idstatus']."',
						   username = '".$this->user."',
						   password = '".$this->info['password']."',
						   realname = '".$this->info['realname']."',
						   email = '".$this->info['email']."',
						   current_login = '".$this->info['current_login']."'
					 WHERE iduser = ".$this->id;
		}

		if (0 !== $db->query($sql))
		    return true;
		return false;
    }

	/**
    * get the user rights for an action
    */
    public function hasRights($action) {
        if (is_array($this->info["rights"]) && in_array($action, $this->info["rights"]))
            return true;
        else
            return false;
    }

	/**
	 * get all active users
	 *
	 * @return void
	 * @author Elias Müller
	 **/
	static public function getActiveUsers() {
		$db = new DB();
		$sql = "SELECT username
				  FROM users
				 WHERE idstatus != 0
			  ORDER BY username ASC";
		$result = $db->query($sql);
		
		do {
			$users[] = User::getInstance($result->username); 
		} while ($result->next());
		return $users;
	}
	
	/**
	 * get the number of currently installed modules
	 *
	 * @return int
	 * @author Elias Müller
	 **/
	static public function getNumber($active = false) {
		$db = DB::getInstance();
		if ($active === true)
			$option = 'WHERE idstatus != 0';
		$sql = "SELECT COUNT(username) AS number FROM users ".$option;
		$result = $db->query($sql);
		return $result->number;
	}
}
?>