<?php
/**
* Manage poker game data.
* 
* @author: Elias Müller
* @version: 0.1
* @since: poker 0.1
* @package: poker
*/

require_once('classes/poker_card.class.php');

class Poker {
    /**
    * objects of this class (only one per id!)
    */
    static public $objects = array();
    
    /**
    * object id (from db) 
    */
    private $id = false;
    
    /**
    * object information
    *
    * @var array
    */
    private $info = array();
    
    public function __construct($table = false, $pot = 0, $actions = array()) {
        $this->info['table'] = $table;
        $this->info['pot'][] = $pot;
        $this->info['actions'] = $actions;
    }

	/**
    * get object of this class or create new
    * 
    * @param string $id the object id
    * @return object reference to the object
    */
    static public function getInstance($id) {
        if(!isset(self::$objects[$id])) {
            self::$objects[$id] = new Poker();
            self::$objects[$id]->load($id);
            $id = self::$objects[$id];
        } else {
            $id = self::$objects[$id];
        }
        return $id;
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
        $sql = "SELECT pg.idgame, pg.pot0, pg.pot1, pg.pot2, pg.pot3, pg.pot4, pg.pot5, pg.idtable, pg.f1, pg.f2, pg.f3, pg.t, pg.r, pa.idaction
                  FROM poker_games AS pg
             LEFT JOIN poker_actions AS pa ON pa.idgame = pg.idgame
                 WHERE pg.idgame = '$id'
              ORDER BY pa.idaction";
        $result = $db->query($sql);
        
        if ($result->length() > 0) {
            // general information
            $this->info = array(
                "table" => PokerTable::getInstance($result->idtable),
                'flop' => false,
                'turn' => false,
                'river' => false
            );

            $this->info['pot'][] = $result->pot0;

            for ($x = 1; $x <= 5; $x++) {
                $pot = 'pot'.$x;
                if (is_numeric($result->$pot) && $result->$pot > 0) {
                    $this->info['pot'][] = $result->$pot;
                }
            }

            // community cards
            if ($result->f1 != '' && $result->f2 != '' && $result->f3 != '') {
                $this->info['flop'] = array(
                    new PokerCard($result->f1),
                    new PokerCard($result->f2),
                    new PokerCard($result->f3)
                );
            }
            if ($result->t != '') {
                $this->info['turn'] = new PokerCard($result->t);
            }
            if ($result->r != '') {
                $this->info['river'] = new PokerCard($result->r);
            }

            // action log
            $actions = array();
            if ($result->idaction != '') {
                do {
                    $actions[] = PokerAction::getInstance($result->idaction);
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
            $sql = "DELETE FROM poker_games
                          WHERE idgame = '".$this->id."'";
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
        if ($this->info['flop'] !== false) {
            $f1 = $this->info['flop'][0]->id;
            $f2= $this->info['flop'][1]->id;
            $f3 = $this->info['flop'][2]->id;
        }
        if ($this->info['turn'] !== false) {
            $t = $this->info['turn']->id;
        }
        if ($this->info['river'] !== false) {
            $r = $this->info['river']->id;
        }

		if ($this->id === false) {
			$sql = "INSERT INTO poker_games
							SET idtable = '".$this->info['table']->id."',
                                pot0 = '".$this->info['pot'][0]."',
                                pot1 = '".$this->info['pot'][1]."',
                                pot2 = '".$this->info['pot'][2]."',
                                pot3 = '".$this->info['pot'][3]."',
                                pot4 = '".$this->info['pot'][4]."',
                                pot5 = '".$this->info['pot'][5]."',
								f1 = '".$f1."',
                                f2 = '".$f2."',
                                f3 = '".$f3."',
                                t = '".$t."',
								r = '".$r."'";
		} else {
			$sql = "UPDATE poker_games
					   SET pot0 = '".$this->info['pot'][0]."',
                           pot1 = '".$this->info['pot'][1]."',
                           pot2 = '".$this->info['pot'][2]."',
                           pot3 = '".$this->info['pot'][3]."',
                           pot4 = '".$this->info['pot'][4]."',
                           pot5 = '".$this->info['pot'][5]."',
                           f1 = '".$f1."',
                           f2 = '".$f2."',
                           f3 = '".$f3."',
                           t = '".$t."',
                           r = '".$r."'
					 WHERE idgame = ".$this->id;
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

    /**
     * Get all player actions in the last turn (after dealing cards).
     *
     * @param bool $bet Record only last betting round.
     */
    public function getTurnActions($bet = false) {
        $actions = array_reverse($this->info['actions']);
        $deals = array('deal', 'flop', 'turn', 'river');
        $bets = array('bet', 'raise');
        $current = array();
        foreach ($actions AS $action) {
            // record only player actions
            /*if ($action->player != false) {
                $current['done_players'] = $action->player;
            }//*/
            // last bet / raise
            if (!array_key_exists('bet', $current) && (in_array($action->action, $bets) || ($action->action == 'blind' && $action->params['blind'] == 'big'))) {
                $current['bet'] = $action;
                if ($action->action == 'blind')
                    $current['bet']->params['rel_value'] = $current['bet']->params['value'];
            }
            // stop search when last card deal ist reached
            if (in_array($action->action, $deals)) {
                $current['deal'] = $action;
                if ($action->action != 'deal')
                    return $current;
            }
        }
        return $current;
    }

    public function getGamePhase() {
        $actions = $this->getTurnActions();
        $map = array(
            'deal' => 0,
            'flop' => 1,
            'turn' => 2,
            'river' => 3
        );
        
        return (array_key_exists($actions['deal']->action, $map)) ? $map[$actions['deal']->action] : 0;
    }

    /**
     * Add pots after a betting turn. First element of $pots gets added to the last existing pot.
     * The others are appended to the internal list of pots.
     *
     * @param array $pots The pots to add to the game. The first element may marge with the last previous pot.
     * @param array $current_allin Players, which are gone all-in during the current turn.
     *
     * @return int Offset from old pot numbers for each player to the new ones.
     */
    public function addPots($pots, $current_allin) {
        if (is_array($pots) && count($pots) > 0) {
            end($this->info['pot']);
            $end = key($this->info['pot']);
            $add = 1;
            $active_players = $this->info['table']->getActivePlayers();
            $allin = false;

            // check, if last pot is all-in pot -> do not change, but fill new pot
            foreach ($active_players as $player) {
                if ($player->stack == 0 && $player->pot == $end && current($this->info['pot']) > 0 && ($current_allin == FALSE || !in_array($player, $current_allin))) {
                    $allin = true;
                    break;
                }
            }
            
            if ($allin == false) {
                $this->info['pot'][key($this->info['pot'])] += $pots[0];
                array_shift($pots);
                $add = 0;
            }
            if (count($pots) > 0) {
                $this->info['pot'] = array_merge($this->info['pot'], $pots);
            }

            return $add + $end;
        }
        return false;
    }
}

/**
* Manage actions during a poker game.
* 
* @author: Elias Müller
* @version: 0.1
* @since: poker 0.1
* @package: poker
*/

class PokerAction {
    /**
    * objects of this class (only one per id!)
    */
    static public $objects = array();
    
    /**
    * object id (from db) 
    */
    protected $id = false;
    
    /**
    * array of object information
    *
    * @var array
    */
    protected $info = array();
    
    public function __construct($game = FALSE, $action = '', $params = array(), $player = false) {
        if ($game != FALSE || $player != FALSE) {
            $this->info['game'] = $game;
            $this->info['table'] = ($game !== FALSE) ? $game->table : $player->table;
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
            self::$objects[$id] = new PokerAction();
            self::$objects[$id]->load($id);
            $id = self::$objects[$id];
        } else {
            $id = self::$objects[$id];
        }
        return $id;
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
        $sql = "SELECT pa.idgame, pa.action, pa.params, pa.idtable, pa.idplayer, UNIX_TIMESTAMP(pa.timestamp) AS timestamp
                  FROM poker_actions AS pa 
                 WHERE pa.idaction = '$id'";
        $result = $db->query($sql);
        
        if ($result->length() > 0) {
            // general information
            $this->info = array(
                "action" => $result->action,
                'params' => unserialize($result->params),
                'player' => ($result->idplayer != '' && $result->idplayer != 0) ? PokerPlayer::getInstance($result->idplayer) : false,
                'game' => Poker::getInstance($result->idgame),
                'table' => PokerTable::getInstance($result->idtable),
                'time' => $result->timestamp
            );

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
            $sql = "DELETE FROM poker_actions
                          WHERE idaction = '".$this->id."'";
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
            $sql = "INSERT INTO poker_actions
                            SET idplayer = '".$this->info['player']->id."',
                                idgame = '".((is_object($this->info['game'])) ? $this->info['game']->id : 0)."',
                                idtable = '".$this->info['table']->id."',
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

/**
* Manage a player in a poker game.
* 
* @author: Elias Müller
* @version: 0.1
* @since: poker 0.1
* @package: poker
*/

class PokerPlayer {
    /**
    * objects of this class (only one per id!)
    */
    static private $objects = array();
    
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
    
    /**
     * Create new user object for a certain table.
     *
     * @param $idtable The table id.
     * @param $position The player position at tthe table.
     * @param $stack The player's chip stack.
     */
    public function __construct($idtable = 0, $position = 0, $stack = 0) {
        if ($idtable != 0) {
            $s = cBootstrap::getInstance();
            $this->info = array(
                "user" => $s->user,
                'position' => $position,
                'stack' => $stack,
                'bet' => 0,
                'cards' => false,
                'last_action' => NULL,
                'join' => TRUE,
                'table' => PokerTable::getInstance($idtable)
            );
            //$this->save();
        }
    }

    /**
    * get object of this class or create new
    * 
    * @param string $id the object id
    * @return object reference to the object
    */
    static public function getInstance($id) {
        if(!is_object(self::$objects[$id]) || self::$objects[$id]->id == false) {
            self::$objects[$id] = new PokerPlayer();
            self::$objects[$id]->load($id);
        }
        return self::$objects[$id];
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
        $sql = "SELECT pp.idplayer, pp.idtable, pp.stack, pp.c1, pp.c2, pp.position, u.username, pa.idaction, pp.bet, pp.pjoin, pp.pleave, pp.pot, pa.idgame AS pagame, pt.idgame AS ptgame
                  FROM poker_players AS pp
            INNER JOIN users AS u ON u.iduser = pp.iduser
            INNER JOIN poker_tables AS pt ON pt.idtable = pp.idtable 
             LEFT JOIN poker_actions AS pa ON (pa.idplayer = pp.idplayer AND pa.action NOT LIKE 'deal' AND pa.action NOT LIKE 'join' AND pa.action NOT LIKE 'leave')
                 WHERE pp.idplayer = '$id'
              ORDER BY pa.idaction DESC
                 LIMIT 1";
        $result = $db->query($sql);

        if ($result->length() > 0) {
            // hand
            $cards = false;
            if ($result->c1 != '') {
                $cards = array(
                    new PokerCard($result->c1),
                    new PokerCard($result->c2),
                );
            }
            // general information
            $table = PokerTable::getInstance($result->idtable);
            $action = PokerAction::getInstance($result->idaction);

            $this->info = array(
                "user" => User::getInstance($result->username),
                'table' => $table,
                'position' => $result->position,
                'stack' => $result->stack,
                'bet' => $result->bet,
                'cards' => $cards,
                'pot' => $result->pot,
                'last_action' => ($result->pagame === $result->ptgame) ? $action : NULL,
                'join' => ($result->pjoin == 1) ? TRUE : FALSE,
                'leave' => ($result->pleave == 1) ? TRUE : FALSE,
            );

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
            $sql = "UPDATE poker_players
                       SET pactive = 0
                     WHERE idplayer = '".$this->id."'";
            $result = $db->query($sql); 
        } else {
            return false;
        }
        return $result;                     
    }
    
    /**
    * save the object information
    */
    public function save() {
        $db = DB::getInstance();
        $s = cBootstrap::getInstance();

        if ($this->info['cards'] !== false) {
            $c1 = $this->info['cards'][0]->id;
            $c2 = $this->info['cards'][1]->id;
        }

        $join = ($this->info['join'] == true) ? 1 : 0;
        $leave = ($this->info['leave'] == true) ? 1 : 0;

        if ($this->id === false) {
            $sql = "INSERT INTO poker_players
                            SET iduser = '".$this->info['user']->id."',
                                idtable = '".$this->info['table']->id."',
                                stack = '".$this->info['stack']."',
                                bet = '".$this->info['bet']."',
                                position = '".$this->info['position']."',
                                pjoin = '".$join."',
                                pleave = '".$leave."',
                                pot = '".$this->info['pot']."',
                                pactive = 1,
                                c1 = '".$c1."',
                                c2 = '".$c2."'";
        } else {
            $sql = "UPDATE poker_players
                       SET stack = '".$this->info['stack']."',
                           bet = '".$this->info['bet']."',
                           position = '".$this->info['position']."',
                           pjoin = '".$join."',
                           pleave = '".$leave."',
                           pot = '".$this->info['pot']."',
                           c1 = '".$c1."',
                           c2 = '".$c2."'
                     WHERE idplayer = ".$this->id;
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

    /**
     * Get the player object for the current user and table
     */
    static public function getActivePlayer($idtable) {
        $db = DB::getInstance();
        $s = cBootstrap::getInstance();
        $sql = "SELECT idplayer
                  FROM poker_players
                 WHERE idtable = '".$idtable."'
                   AND iduser = '".$s->user->id."'
                   AND pactive = 1";
        $result = $db->query($sql);

        if ($result->length() > 0) {
            return self::getInstance($result->idplayer);
        } else {
            return false;
        }
    } 
}

?>