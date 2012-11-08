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

class Blackboard {
    /**
    * objects of class user (only one per username!)
    */
    static public $objects = array();
    
    /**
    * user id (from db) 
    */
    private $id;
    
    /**
    * array of user information (realname, email, status, rights(array))
    *
    * @var array
    */
    private $info = array();
    
    /**
    * private because of singleton 
    */
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
            self::$objects[$id] = new Blackboard();
            self::$objects[$id]->load($id);
            $b = self::$objects[$id];
        } else {
            $b = self::$objects[$id];
        }
        return $b;
    }
    
    /**
    * 
    */
    public function __get($field) {
        return $this->info[$field];
    }

    /**
    * 
    */
    private function load($id) {
    }
    
    /**
    * 
    */
    private function delete() {                
    }
    
    /**
    * 
    */
    public function save() {
    }
}
?>