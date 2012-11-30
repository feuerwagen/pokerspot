<?php
/**
* Manage a poker spot including ranges, stack sizes, player positions and pre-flop action.
* 
* @author: Elias Müller
* @version: 0.1
* @since: poker 0.3
* @package: poker
*/

require_once('modules/poker/poker.class.php');

class PokerSpot {
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

    public function __construct($title = '', $button = 0, $stack_p1 = 1, $stack_p2 = 1, $range_p1 = array(), $range_p2 = array()) {
        if ($title != '') {            
            $this->info = array(
                'title' => $title,
                'button' => $button,
                'stacks' => array(
                    $stack_p1, $stack_p2
                ),
                'ranges' => array(
                    $range_p1, $range_p2
                ),
                'actions' => false
            );
        }
    }

    /**
    * get object of this class or create new
    * 
    * @param string $id the object id
    * @param bool $reload force reloading object info from db
    * @return object reference to the object
    */
    static public function getInstance($id, $reload = FALSE) {
        if(!isset(self::$objects[$id])) {
            self::$objects[$id] = new PokerSpot();
            self::$objects[$id]->load($id);
            $obj = self::$objects[$id];
        } else {
            $obj = self::$objects[$id];
            if ($reload == TRUE) {
                $obj->load($id);
            }
        }
        return $obj;
    }

    static public function getAll() {
        $db = new DB();
        $sql = "SELECT idspot
                  FROM poker_spots";
        $result = $db->query($sql);

        $tables = array();
        if ($result->length() > 0) {
            do {
                $tables[$result->idspot] = PokerSpot::getInstance($result->idspot);
            } while ($result->next());
        }
        return $tables;
    }
    
    /**
    * magic: return object information
    */
    public function &__get($field) {
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
        $sql = "SELECT *
                  FROM poker_spots AS ps
             LEFT JOIN poker_actions AS pa ON pa.idspot = ps.idspot
                 WHERE ps.idspot = '$id'
              ORDER BY pa.idaction ASC";
        $result = $db->query($sql);
        
        if ($result->length() > 0) {
            // general information
            $this->info = array(
                'title' => $result->title,
                'button' => $result->button,
                'stacks' => array(
                    $result->p1_stack,
                    $result->p2_stack
                ),
                'ranges' => array(
                    unserialize($result->p1_range),
                    unserialize($result->p2_range)
                ),
            );

            // action log
            $actions = array();
            if ($result->idaction != '') {
                do {
                    $actions[] = SpotAction::getInstance($result->idaction);
                } while ($result->next());
            }
            $this->info['actions'] = $actions;

            $this->id = $id;
            return true;
        }
        return false;
    }
    
    /**
    * delete the object information from the db
    */
    public function delete() {
        if ($this->id !== false) {
            $db = DB::getInstance();
            $sql = "DELETE FROM poker_spots
                          WHERE idspot = '".$this->id."'";
            $result = $db->query($sql);

            $sql = "UPDATE poker_tables
                       SET idspot = NULL
                     WHERE idspot = ".$this->id;
            $db->query($sql); 
        } else
            return false;
        return $result;                     
    }
    
    /**
    * save the object information
    */
    public function save() {
        $db = DB::getInstance();
        $s = cBootstrap::getInstance();

        if ($this->id === false) {
            $sql = "INSERT INTO poker_spots
                            SET title = '".$this->info['title']."',
                                iduser = '".$s->user->id."',
                                button = '".$this->info['button']."',
                                p1_stack = '".$this->info['stacks'][0]."',
                                p2_stack = '".$this->info['stacks'][1]."',
                                p1_range = '".serialize($this->info['ranges'][0])."',
                                p2_range = '".serialize($this->info['ranges'][1])."'";
        } else {
            $sql = "UPDATE poker_spots
                       SET title = '".$this->info['title']."',
                           button = '".$this->info['button']."',
                           p1_stack = '".$this->info['stacks'][0]."',
                           p2_stack = '".$this->info['stacks'][1]."',
                           p1_range = '".serialize($this->info['ranges'][0])."',
                           p2_range = '".serialize($this->info['ranges'][1])."'
                     WHERE idspot = ".$this->id;
        }

        $id = $db->query($sql);
        if ($id != 0) {
            if ($this->id === false) {
                $this->id = $id;
                self::$objects[$id] = $this;
            }
            return true;
        }
        return false;
    }
}

/**
* Manage predefined actions for a poker spot.
* 
* @author: Elias Müller
* @version: 0.1
* @since: poker 0.3
* @package: poker
*/

class SpotAction extends PokerAction {    
    public function __construct($spot = FALSE, $action = '', $params = array(), $player = false) {
        if ($spot != FALSE) {
            $this->info['spot'] = $spot;
            $this->info['action'] = $action;
            $this->info['params'] = $params;
            $this->info['player'] = $player; 
        }  
    }

    /**
    * get object of this class or create new
    * 
    * @param string $id the object id
    * @return object reference to the object
    */
    static public function getInstance($id) {
        if(!isset(self::$objects[$id])) {
            self::$objects[$id] = new SpotAction();
            self::$objects[$id]->load($id);
            $id = self::$objects[$id];
        } else {
            $id = self::$objects[$id];
        }
        return $id;
    }
            
    /**
    * read the object information from the db
    */
    private function load($id) {
        $db = new DB();
        $sql = "SELECT pa.idspot, pa.action, pa.params, pa.idplayer
                  FROM poker_actions AS pa 
                 WHERE pa.idaction = '$id'";
        $result = $db->query($sql);
        
        if ($result->length() > 0) {
            // general information
            $this->info = array(
                'action' => $result->action,
                'params' => unserialize($result->params),
                'player' => $result->idplayer,
                'spot' => PokerSpot::getInstance($result->idspot)
            );

            $this->id = $id;
            return true;
        }
        return false;
    }
        
    /**
    * save the object information
    */
    public function save() {
        $db = DB::getInstance();

        if ($this->id === false) {
            $sql = "INSERT INTO poker_actions
                            SET idplayer = '".$this->info['player']."',
                                idspot = '".((is_object($this->info['spot'])) ? $this->info['spot']->id : 0)."',
                                action = '".$this->info['action']."',
                                params = '".serialize($this->info['params'])."',
                                timestamp = NOW()";
            $id = $db->query($sql);
            if ($id != 0) {
                $this->id = $id;
                self::$objects[$id] = $this;
                return true;
            }
        }
        
        return false;
    }
}
?>