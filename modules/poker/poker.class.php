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
    
    public function __construct($table, $pot = 0, $actions = array()) {
        $this->info['table'] = $table;
        $this->info['pot'] = $pot;
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
        $db = DB::getInstance();
        $sql = "SELECT pg.idgame, pg.pot, pg.idtable, pg.f1, pg.f2, pg.f3, pg.t, pg.r, pa.idaction
                  FROM poker_games AS pg
             LEFT JOIN poker_actions AS pa ON pa.idgame = pg.idgame
                 WHERE pg.idgame = '$id'
              ORDER BY pa.idaction";
        $result = $db->query($sql);
        
        if ($result->length() > 0) {
            // action log
            $actions = array();
            if ($result->idaction != '') {
                do {
                    $actions[] = PokerAction::getInstance($result->idaction);
                } while ($result->next());
            }

            // general information
            $this->info = array(
                "table" => $result->idtable,
                'flop' => false,
                'turn' => false,
                'river' => false,
                'pot' => $result->pot,
                'actions' => $actions
            );

            // community cards
            if (!empty($result->f1) && !empty($result->f2) && !empty($result->f3)) {
                $this->info['flop'] = array(
                    new PokerCard($result->f1),
                    new PokerCard($result->f2),
                    new PokerCard($result->f3)
                );
            }
            if (!empty($result->t) ) {
                $this->info['turn'] = new PokerCard($result->t);
            }
            if (!empty($result->r) ) {
                $this->info['river'] = new PokerCard($result->r);
            }
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
							SET idtable = '".$this->info['table']."',
                                pot = '".$this->info['pot']."',
								f1 = '".$f1."',
                                f2 = '".$f2."',
                                f3 = '".$f3."',
                                t = '".$t."',
								r = '".$r."'";
		} else {
			$sql = "UPDATE poker_games
					   SET pot = '".$this->info['pot']."',
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
    private $id = false;
    
    /**
    * array of object information
    *
    * @var array
    */
    private $info = array();
    
    public function __construct($game, $action = '', $params = array(), $player = false) {
        $this->info['game'] = $game;
        $this->info['action'] = $action;
        $this->info['params'] = $params;
        $this->info['player'] = $player;
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
        $db = DB::getInstance();
        $sql = "SELECT *
                  FROM poker_actions AS pa 
                 WHERE pa.idaction = '$id'";
        $result = $db->query($sql);
        
        if ($result->length() > 0) {
            // general information
            $this->info = array(
                "action" => $result->action,
                'params' => unserialize($result->params),
                'player' => ($result->idplayer != '') ? PokerPlayer::getInstance($result->idplayer) : false,
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
                            SET idplayer = '".$this->info['player']."',
                                idgame = '".$this->info['game']->id."',
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
    
    /**
     * Create new user object for a certain table.
     *
     * @param $idtable The table id.
     * @param $position The player position at tthe table.
     * @param $stack The player's chip stack.
     */
    public function __construct($idtable, $position, $stack) {
        $s = cBootstrap::getInstance();
        $this->info = array(
            "user" => $s->user,
            'position' => $position,
            'stack' => $stack,
            'bet' => 0,
            'cards' => false,
            'last_action' => NULL,
            'join' => TRUE,
        );
        $player->save();
        $this->info['table'] = PokerTable::getInstance($idtable);
    }

    /**
    * get object of this class or create new
    * 
    * @param string $id the object id
    * @return object reference to the object
    */
    static public function getInstance($id) {
        if(!isset(self::$objects[$id])) {
            self::$objects[$id] = new PokerPlayer();
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
        $db = DB::getInstance();
        $sql = "SELECT pp.idplayer, pp.idtable, pp.stack, pp.c1, pp.c2, pp.position, u.username, pa.idaction, pp.bet, pp.join, pp.leave
                  FROM poker_players AS pp
            INNER JOIN users AS u on u.iduser = pp.iduser
             LEFT JOIN poker_actions AS pa ON pa.iduser = pp.iduser
                 WHERE pp.idplayer = '$id'
              ORDER BY pa.timestamp DESC
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
                'last_action' => ($action->game == $table->game) ? $action : NULL,
                'join' => ($result->join == 1) ? TRUE : FALSE,
                'leave' => ($result->leave == 1) ? TRUE : FALSE,
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
            $sql = "DELETE FROM poker_players
                          WHERE idplayer = '".$this->id."'";
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
                                idtable = '".$this->info['table']."',
                                stack = '".$this->info['stack']."',
                                bet = '".$this->info['bet']."',
                                position = '".$this->info['position']."',
                                join = '".$join."',
                                leave = '".$leave."',
                                c1 = '".$c1."',
                                c2 = '".$c2."'";
        } else {
            $sql = "UPDATE poker_players
                       SET stack = '".$this->info['stack']."',
                           bet = '".$this->info['bet']."',
                           position = '".$this->info['position']."',
                           join = '".$join."',
                           leave = '".$leave."',
                           c1 = '".$c1."',
                           c2 = '".$c2."',
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
        $sql = "SELECT idplayer
                  FROM poker_players
                 WHERE idtable = '".$idtable."'
                   AND iduser = '".$s->user->id."'";
        $result = $db->query($sql);

        if ($result->length() > 0) {
            return self::getInstance($result->idplayer);
        } else {
            return false;
        }
    } 
}

/**
* Manage a poker table including active players and a reference to the current game.
* 
* @author: Elias Müller
* @version: 0.1
* @since: poker 0.1
* @package: poker
*/

class PokerTable {
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
    * @param string $id the object id
    * @param bool $reload force reloading object info from db
    * @return object reference to the object
    */
    static public function getInstance($id, $reload = FALSE) {
        if(!isset(self::$objects[$id])) {
            self::$objects[$id] = new PokerTable();
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
        $db = DB::getInstance();
        $sql = "SELECT idtable
                  FROM poker_tables";
        $result = $db->query($sql);

        $tables = array();
        if ($result->length() > 0) {
            do {
                $tables[$result->idtable] = PokerTable::getInstance($result->idtable);
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
        $db = DB::getInstance();
        $sql = "SELECT pt.idgame, pt.title, pp.idplayer, pt.d, pt.sb, pt.bb, pt.seats, pt.blind
                  FROM poker_tables AS pt
             LEFT JOIN poker_players AS pp ON pp.idtable = pt.idtable
                 WHERE pt.idtable = '$id'
              ORDER BY pp.position";
        $result = $db->query($sql);
        
        if ($result->length() > 0) {
            $players = array();
            if ($result->idplayer != '') {
                $prev = false;
                do {
                    $player = PokerPlayer::getInstance($result->idplayer);
                    $player->prev = $prev;
                    if (is_object($prev)) {
                        $prev->next = $player;
                    }
                    $players[$player->position] = $player;
                    $prev = $player;
                } while ($result->next());
                $first = first($players);
                $first->prev = $player;
            }

            // general information
            $this->info = array(
                "game" => ($result->idgame != 0) ? Poker::getInstance($result->idgame) : FALSE,
                'title' => $result->title,
                'players' => $players,
                'positions' => array(
                    'smallblind' => $result->sb,
                    'bigblind' => $result->bb,
                    'dealer' => $result->d,
                ),
                'blinds' => array(
                    'big' => 2*$result->blind,
                    'small' => $result->blind
                ),
                'seats' => $result->seats,
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
            $sql = "DELETE FROM poker_tables
                          WHERE idtable = '".$this->id."'";
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
        $game = ($this->info['game'] != FALSE) ? $this->info['game']->id : 0;

        if ($this->id === false) {
            $sql = "INSERT INTO poker_tables
                            SET idgame = '".$game."',
                                title = '".$this->info['title']."',
                                d = '".$this->info['positions']['dealer']."',
                                sb = '".$this->info['positions']['smallblind']."',
                                bb = '".$this->info['positions']['bigblind']."',
                                seats = '".$this->info['seats']."',
                                blind = '".$this->info['blinds']['small']."'";
        } else {
            $sql = "UPDATE poker_players
                       SET idgame = '".$game."',
                           title = '".$this->info['title']."',
                           d = '".$this->info['positions']['dealer']."',
                           sb = '".$this->info['positions']['smallblind']."',
                           bb = '".$this->info['positions']['bigblind']."',
                           seats = '".$this->info['seats']."',
                           blind = '".$this->info['blinds']['small']."'
                     WHERE idtable = ".$this->id;
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
     * Check for new actions since the last request.
     *
     * @param object $player The player, for wich to check.
     * @param int $timestamp The Unix timestamp of the last request.
     */
    public function newActions($timestamp) {
        $db = DB::getInstance();
        $sql = "SELECT idaction
                  FROM poker_actions
                 WHERE UNIX_TIMESTAMP(timestamp) > '".$timestamp."'
                   AND idtable = '".$this->id."'";
        $result = $db->query($sql);

        if ($result->length() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Get all new actions on this table.
     *
     * @param int timestamp The timestamp after which to look for new actions.
     */
    public function getNewActions($timestamp) {
        $db = DB::getInstance();
        $sql = "SELECT idaction, idgame, action, params, UNIX_TIMESTAMP(timestamp)
                  FROM poker_actions
                 WHERE UNIX_TIMESTAMP(timestamp) > '".$timestamp."'
                   AND idtable = '".$this->id."'";
        $result = $db->query($sql);

        if ($result->length() > 0) {
            $actions = array();
            do {
                $actions[] = array(
                    'idgame' => $result->idgame,
                    'action' => $result->action,
                    'params' => unserialize($result->params),
                    'timestamp' => $result->timestamp
                );
            } while ($result->next());
        }
        return false;
    }

    /**
     * Get the next player in line.
     *
     * @param object $player The current player.
     */
    public function getNextPlayer($player = NULL, $fold = TRUE) {
        // get player with last action
        if ($player == NULL) {
            $actions = array_reverse($this->game->actions);
            foreach ($actions as $key => $action) {
                if ($action->player != false) {
                    $player = $action->player;
                }
            }
        }

        if (is_object($player)) {
            while ($player = $player->next) {
                if ($player->join != TRUE && ($fold == FALSE || $player->last_action == NULL || $player->last_action->action != 'fold')) {
                    return $player;
                }
            }
        }
        // TODO: edge cases: new game, new round
        // new game = no player / game action -> start with player left of BB
        // new round = no player action after deal -> start with player left of D
        return false;
    }

    /**
     * Get the previous player in line.
     *
     * @param object $player The current player.
     */
    public function getPrevPlayer($player = NULL, $fold = TRUE) {
        // get player with last action
        if ($player == NULL) {
            $actions = array_reverse($this->game->actions);
            foreach ($actions as $key => $action) {
                if ($action->player != false) {
                    $player = $action->player;
                }
            }
        }

        if (is_object($player)) {
            while ($player = $player->prev) {
                if ($player->join != TRUE && ($fold == FALSE || $player->last_action == NULL || $player->last_action->action != 'fold')) {
                    return $player;
                }
            }
        }
        return false;
    }

    /**
     * Get all active (not folded) players.
     *
     * @param bool $fold if TRUE, only return players, which have not folded yet.
     * @return array The active players.
     */
    public function getActivePlayers($fold = TRUE) {
        $active = array();
        foreach ($this->info['players'] as $player) {
            if ($player->join != TRUE && ($fold == FALSE || $player->last_action == NULL || $player->last_action->action != 'fold')) {
                $active[] = $player;
            }
        }
        return $active;
    }

    /**
     * Move D / SB / BB clockwise (if new, but not first game)
     *
     * @param bool $new_game TRUE if no game running on this table yet.
     */
    public function movePositions($new_game) {
        if ($new_game === FALSE) {
            foreach ($this->info['positions'] as $type => $position) {
                if ($position != 0 && array_key_exists($position, $this->info['players'])) {
                    $this->info['positions'][$type] = $this->info['players'][$position]->next->position;
                }
            }
        } else {
            // first active player
            $player = current($this->info['players']);
            
            // set starting positions
            $this->info['positions']['dealer'] = $player->position;
            $this->info['positions']['smallblind'] = $player->next->position;
            $this->info['positions']['bigblind'] = $player->next->next->position;

            // heads-up: move bb and sb to correct players
            if ($this->info['positions']['dealer'] == $this->info['positions']['bigblind']) {
                $temp = $this->info['positions']['smallblind'];
                $this->info['positions']['smallblind'] = $this->info['positions']['bigblind'];
                $this->info['positions']['bigblind'] = $temp;
            }
        }
    }

    /**
     * Remove player from table.
     *
     * @param int $seat
     */
    public function removePlayer($seat) {
        if (array_key_exists($seat, $this->info['players'])) {
            // move button / blind, if nessecary
            // player is removed before starting a new game, so the blinds 
            // should be moved to the next player when starting the game.
            if (in_array($seat, $this->info['positions'])) {
                $active_players = count($this->getActivePlayers(TRUE));
                $position = array_search($this->info['positions']);
                if ($active_players == 3) {
                    switch($position) {
                        case 'bigblind':
                            // move bb to d and d to sb
                            $this->info['positions']['bigblind'] = $this->info['positions']['dealer'];
                        case 'dealer':
                            // move d to sb
                            $this->info['positions']['dealer'] = $this->info['positions']['smallblind'];
                            break;
                        case 'smallblind':
                            // move sb to d
                            $this->info['positions']['smallblind'] = $this->info['positions']['dealer'];
                            break;
                    }
                } elseif ($active_players > 3) {
                    switch($position) {
                        case 'bigblind':
                            // move bb to next player
                            $next = $this->getNextPlayer($this->info['players'][$seat], FALSE);
                            $this->info['positions']['bigblind'] = $next->position;
                            break;
                        case 'dealer':
                            // move d to prev player
                            $prev = $this->getPrevPlayer($this->info['players'][$seat], FALSE);
                            $this->info['positions']['dealer'] = $prev->position;
                            break;
                        case 'smallblind':
                            // move sb to prev player and d to prev-prev player
                            $prevprev = $this->getPrevPlayer($this->info['players'][$this->info['positions']['dealer']], FALSE);
                            $this->info['positions']['smallblind'] = $this->info['positions']['dealer'];
                            $this->info['positions']['dealer'] = $prevprev->position;
                            break;
                    }
                }
                if ($this->info['positions'][''])
                foreach ($this->info['positions'] as $key => $position) {
                    if ($seat == $position) {
                        $this->info['positions'][$key] = $this->info['players'][$seat]->prev;
                    }
                }
            }
            // remove player from seat
            $this->info['players'][$seat]->next->prev = $this->info['players'][$seat]->prev;
            $this->info['players'][$seat]->prev->next = $this->info['players'][$seat]->next;
            unset($this->info['players'][$seat]);
            return true;
        }
        return false;
    }
}
?>