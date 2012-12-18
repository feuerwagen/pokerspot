<?php
/**
* Manage a poker table including active players and a reference to the current game.
* 
* @author: Elias Müller
* @version: 0.1
* @since: poker 0.1
* @package: poker
*/

require_once('modules/poker/poker_spot/poker_spot.class.php');
require_once('modules/message/message.class.php');

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

    public function __construct($title = '', $seats = 2, $sb = '', $spot = FALSE) {
        $this->info = array(
            'title' => $title,
            'seats' => (is_numeric($seats)) ? $seats : 2,
            'blinds' => array(
                'small' => $sb,
                'big' => $sb*2
            ),
            'spot' => $spot
        );
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

    static public function getAll($active = false) {
        $db = new DB();
        $sql = "SELECT idtable
                  FROM poker_tables";
        if ($active == true) {
            $sql .= " WHERE idgame IS NOT NULL";
        }
        $result = $db->query($sql);

        $tables = array();
        if ($result->length() > 0) {
            do {
                $tables[$result->idtable] = PokerTable::getInstance($result->idtable);
            } while ($result->next());
        }
        return $tables;
    }

    static public function getAllForUser($iduser = false) {
        if (iduser != false) {
            $db = new DB();
            $sql = "SELECT pt.idtable
                      FROM poker_tables AS pt
                INNER JOIN poker_players AS pp ON pp.idtable = pt.idtable AND pp.iduser = ".$iduser;
            $result = $db->query($sql);

            $tables = array();
            if ($result->length() > 0) {
                do {
                    $tables[$result->idtable] = PokerTable::getInstance($result->idtable);
                } while ($result->next());
            }
            return $tables;
        }
        return false;
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
        $sql = "SELECT pt.idgame, pt.title, pp.idplayer, pt.d, pt.sb, pt.bb, pt.seats, pt.blind, pt.iduser, pt.idspot
                  FROM poker_tables AS pt
             LEFT JOIN poker_players AS pp ON (pp.idtable = pt.idtable AND pp.pactive = 1)
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
                $first = reset($players);
                $first->prev = $prev;
                $prev->next = $first;

            }

            // general information
            $this->info = array(
                "game" => ($result->idgame != 0 && $result->idgame != '') ? Poker::getInstance($result->idgame) : FALSE,
                "spot" => ($result->idspot != 0 && $result->idspot != '') ? PokerSpot::getInstance($result->idspot) : FALSE,
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
                'user' => $result->iduser
            );

            $this->info['free'] = $this->info['seats'] - count($this->info['players']);

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
     * set lock flag for table in db to prevent early polling responses
     */
    public function lock($set) {
        $set = ($set === true) ? 1 : 0;
        $db = DB::getInstance();
        $sql = "UPDATE poker_tables
                   SET tlock = '".$set."'
                 WHERE idtable = ".$this->id;
        $db->query($sql);
    }
    
    /**
    * save the object information
    */
    public function save() {
        $db = DB::getInstance();
        $s = cBootstrap::getInstance();
        $game = ($this->info['game'] != FALSE) ? $this->info['game']->id : 0;
        $spot = ($this->info['spot'] != FALSE) ? $this->info['spot']->id : 0;

        if ($this->id === false) {
            $sql = "INSERT INTO poker_tables
                            SET idgame = '".$game."',
                                idspot = '".$spot."',
                                title = '".$this->info['title']."',
                                iduser = '".$s->user->id."',
                                d = '".$this->info['positions']['dealer']."',
                                sb = '".$this->info['positions']['smallblind']."',
                                bb = '".$this->info['positions']['bigblind']."',
                                seats = '".$this->info['seats']."',
                                blind = '".$this->info['blinds']['small']."'";
        } else {
            $sql = "UPDATE poker_tables
                       SET idgame = '".$game."',
                           idspot = '".$spot."',
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
     * Get all new actions on this table.
     *
     * @param int $timestamp The timestamp after which to look for new actions.
     */
    static public function getNewActions($timestamp, $id) {
        $db = new DB();
        $sql = "SELECT pa.idaction
                  FROM poker_actions AS pa
            INNER JOIN poker_tables AS pt ON pt.idtable = pa.idtable
                 WHERE UNIX_TIMESTAMP(pa.timestamp) > '".$timestamp."'
                   AND pa.idtable = '".$id."'
                   AND pt.tlock = 0
              ORDER BY pa.idaction ASC";
        $result = $db->query($sql);

        if ($result->length() > 0) {
            $actions = array();
            do {
                $actions[] = PokerAction::getInstance($result->idaction);
            } while ($result->next());
            return $actions;
        }
        return false;
    }

    /**
     * Get all new chat messages for this table.
     *
     * @param int $timestamp The timestamp after which to look for new messages.
     */
    static public function getNewMessages($timestamp, $id) {
        $db = new DB();
        $sql = "SELECT m.idmessage
                  FROM messages AS m
            INNER JOIN poker_tables AS pt ON pt.idtable = m.idrecvr AND pt.idtable = '".$id."'
                 WHERE UNIX_TIMESTAMP(m.created) > '".$timestamp."'
                   AND m.recvr = 'poker'
              ORDER BY m.idmessage ASC";
        $result = $db->query($sql);

        if ($result->length() > 0) {
            $msgs = array();
            do {
                $msgs[] = Message::getInstance($result->idmessage);
            } while ($result->next());
            return $msgs;
        }
        return false;
    }

    /**
     * Get all actions on this table since given $idaction.
     *
     * @param int $idaction The idaction from which to look for new actions.
     * @param int $id The id of the table.
     */
    static public function getTableActions($idaction, $id) {
        $db = new DB();
        $sql = "SELECT pa.idaction
                  FROM poker_actions AS pa
            INNER JOIN poker_tables AS pt ON pt.idtable = pa.idtable
                 WHERE pa.idaction >= '".$idaction."'
                   AND pa.idtable = '".$id."'
                   AND pt.tlock = 0
              ORDER BY pa.idaction ASC";
        $result = $db->query($sql);

        if ($result->length() > 0) {
            $actions = array();
            do {
                $actions[] = PokerAction::getInstance($result->idaction);
            } while ($result->next());
            return $actions;
        }
        return false;
    }

    /**
     * Get the next player in line.
     *
     * @param object $player The current player.
     * @param bool $fold True: Only select player, if he has not folded yet.
     */
    public function getNextPlayer($player = NULL, $fold = TRUE) {
        // get player with last action
        if ($player == NULL) {
            if (is_array($this->game->actions)) {
                $actions = array_reverse($this->game->actions);
                $deals = array('flop', 'turn', 'river');
                $invalid = array('join', 'leave', 'blind', 'deal');
                foreach ($actions as $key => $action) {
                    if (in_array($action->action, $deals)) { // new round -> no player yet, start over
                        $player = 'round';
                        break;
                    } elseif ($action->player != false && !in_array($action->action, $invalid)) { // last player action found
                        $player = $action->player;
                        break;
                    }
                }
            }
        /*    if ($player == NULL) {
                $player = reset($this->info['players']);
            }//*/
        }

        if (is_object($player)) {
            $p = $player;
            while ($player = $player->next) {
                if ($player === $p) {
                    return false;
                }

                if ($player->join != TRUE && $player->stack > 0 && ($fold == FALSE || $player->last_action == NULL || $player->last_action->action != 'fold')) {
                    return $player;
                }
            }
        } else {
            // new round = no player action after deal -> start with player left of D
            if ($player == 'round') {
                $player = $this->getNextPlayer($this->info['players'][$this->info['positions']['dealer']]);
            }
            // new game = no player / game action -> start with player left of BB (normal) / SB (headsup)
            elseif ($this->info['positions']['dealer'] == $this->info['positions']['smallblind']) {
                $player = $this->info['players'][$this->info['positions']['dealer']];
            } else {
                $player = $this->getNextPlayer($this->info['players'][$this->info['positions']['bigblind']]);
            }
            return $player;
        }
        
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
            $p = $player;
            while ($player = $player->prev) {
                if ($player === $p) {
                    return false;
                }
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
     * @param bool $fold If TRUE, only return players, which have not folded yet.
     * @param bool $seated If TRUE, only return players, which are seated at this table.
     * @return array The active players.
     */
    public function getActivePlayers($fold = TRUE, $seated = TRUE) {
        $active = array();
        if (is_array($this->info['players'])) {
            foreach ($this->info['players'] as $player) {
                if (($seated == FALSE || $player->join != TRUE) && ($fold == FALSE || $player->last_action == NULL || $player->last_action->action != 'fold')) {
                    $active[] = $player;
                }
            }
        }
        
        return $active;
    }

    /**
     * Move D / SB / BB clockwise (if new, but not first game)
     *
     * @param bool $new_game TRUE if no game running on this table yet.
     */
    public function movePositions($new_game = false) {
        if ($new_game === FALSE) {
            foreach ($this->info['positions'] as $type => $position) {
                if ($position != 0 && array_key_exists($position, $this->info['players'])) {
                    $this->info['positions'][$type] = $this->info['players'][$position]->next->position;
                }
            }
        } else {
            // first active player
            $player = reset($this->info['players']);
            
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
     * Add player to table.
     *
     * @param int $seat
     */
    public function addPlayer($seat) {
        if (array_key_exists($seat, $this->info['players']) && $this->info['game'] !== FALSE) {
            // move blinds, if nessecary (player is added before starting a new game)
            // rearrange positions while keeping the dealer position
            $dealer = $this->info['players'][$this->info['positions']['dealer']];
            $sb = $this->getNextPlayer($dealer, FALSE);
            $bb = $this->getNextPlayer($sb, FALSE);
            $this->info['positions']['smallblind'] = $sb->position;
            $this->info['positions']['bigblind'] = $bb->position;
            return true;
        }
        return false;
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
                $active_players = count($this->getActivePlayers(FALSE, FALSE));
                $position = array_search($seat, $this->info['positions']);
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