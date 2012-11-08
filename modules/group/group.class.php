<?php
/**
* Manage user groups.
* 
* @author: Elias Müller
* @version: 0.3
* @since: chispa 0.5a
* @package: core
* @subpackage: backend
*/

class Group {
    /**
    * objects of this class (only one per id!)
    */
    static public $objects = array();
    
    /**
    * object id (from db) 
    */
    private $id;
    
    /**
    * array of object information
    *
    * @var array
    */
    public $info = array();
    
    /**
    * private because of singleton 
    */
    public function __construct() {
    }
    
    /**
    * get object of this class or create new
    * 
    * @param string $id the object id
    * @return object reference to the object
    */
    static public function getInstance($id = NULL) {
        if(!isset($id)) {
            // new group without name allowed, but only for form handling
            $group = new Group();
        } elseif(!is_object(self::$objects[$id])) {
            self::$objects[$id] = new Group();
            self::$objects[$id]->load($id);
            $group = self::$objects[$id];
        } else {
            $group = self::$objects[$id];
        }
        return $group;
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
    public function __set($field, $val) {
        $this->info[$field] = $val;
    }

    /**
    * read the object information from the db
    */
    private function load($id) {
		$this->id = $id;
		$db = DB::getInstance();
        $sql = "SELECT *
                  FROM groups
                 WHERE idstatus = '$id'";
        $result = $db->query($sql);
        
        if ($result->length() > 0) {
            // rights array
            $rights = explode(",", $result->rights);
            // general information
            $this->info = array(
                "name" => $result->name,
				"rights" => $rights
            );
            return true;
        } else
            return false;
    }
    
    /**
    * delete the object information from the db
    */
    public function delete() {    
		$db = DB::getInstance();
		$sql = "SELECT idstatus
				  FROM users
				 WHERE idstatus = ".$this->id;
		$result = $db->query($sql);
		
		if ($result->length() == 0) {
			$sql = "DELETE FROM groups
						  WHERE idstatus = '".$this->id."'";
			if($db->query($sql) != 0)
				return true;
		}
		Error::addError('Die Gruppe konnte nicht gelöscht werden, da ihr noch Benutzer zugeordnet sind. Diese müssen zuvor anderen Gruppen zugewiesen werden!');
		return false;
    }
    
    /**
    * save the object information
    */
    public function save() {
		$db = DB::getInstance();

		if (is_array($this->info['rights']))
			$rights = implode(',', $this->info['rights']);
		
		if (empty($this->id)) {
			$sql = "INSERT INTO groups
						 	SET name = '".$this->info['name']."',
								rights = '".$rights."'";
		} else {
			$sql = "UPDATE groups
				 	   SET name = '".$this->info['name']."',
						   rights = '".$rights."'
					 WHERE idstatus = ".$this->id;
		}
								
		if ($db->query($sql) !== 0) {
			if (!is_object(self::$objects[$id]))
				self::$objects[$id] = $this;
			return true;
		}
		return false;
    }

	/**
	 * get all groups
	 *
	 * @return array
	 * @author Elias Müller
	 **/
	static public function getAll() {
		$db = new DB();
		$sql = 'SELECT idstatus
				  FROM groups';
		$result = $db->query($sql);
		
		if ($result->length() > 0) {
			do {
				$g[$result->idstatus] = Group::getInstance($result->idstatus);
			} while ($result->next());
			return $g;
		}
		return false;
	}
}
?>