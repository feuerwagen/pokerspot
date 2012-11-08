<?php
/**
* Manage modules.
* 
* @author: Elias Müller
* @version: 0.2
* @since: chispa 0.5a
* @package: core
* @subpackage: backend
*/

class Module {
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
    private function __construct() {
    }
    
    /**
    * get object of this class or create new
    * 
    * @param string $id the object id
    * @return object reference to the object
    */
    static public function getInstance($id) {
        if(!is_object(self::$objects[$id])) {
            self::$objects[$id] = new Module();
            self::$objects[$id]->load($id);
            $module = self::$objects[$id];
        } else {
            $module = self::$objects[$id];
        }
        return $module;
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
                  FROM modules
                 WHERE name = '$id'";
        $result = $db->query($sql);
        
        if ($result->length() > 0) {
            // rights array
            $requires = explode(",", $result->requires);
            // general information
            $this->info = array(
                "version" => $result->version,
                "active" => (($result->active == 1) ? true : false),
                "name" => $result->title,
                "description" => $result->description,
				"requires" => $requires
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
		$sql = "DELETE FROM modules
					  WHERE name = '".$this->id."'";
		$db->query($sql);
    }
    
    /**
    * save the object information
    */
    public function save() {
		$db = DB::getInstance();
		$active = ($this->info['active'] === true) ? 1 : 0;
		if (is_array($this->info['requires']))
			$requires = implode(',', $this->info['requires']);
		
		if ($this->id != '') {
            $info = array();
            foreach ($this->info AS $key => $value) {
                $info[$key] = $db->escape($value);
            }

			$sql = "INSERT INTO modules
						 	SET name = '".$this->id."',
								version = '".$info['version']."',
								active = '".$active."',
								requires = '".$requires."',
								title = '".$info['name']."',
								description = '".$info['description']."'
		ON DUPLICATE KEY UPDATE version = '".$info['version']."',
								active = '".$active."',
								requires = '".$requires."',
								title = '".$info['name']."',
								description = '".$info['description']."'";
								
			$db->query($sql);
			if (!is_object(self::$objects[$this->id]))
				self::$objects[$this->id] = $this;
			return true;
		}
		return false;
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
			$option = 'WHERE active = 1';
		$sql = "SELECT COUNT(name) AS number FROM modules ".$option;
		$result = $db->query($sql);
		return $result->number;
	}
}
?>