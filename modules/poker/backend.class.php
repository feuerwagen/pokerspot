<?php
/**
* Backend controller for the user module.
* 
* @uses: BackendController
* @author: Elias Müller
* @version: 0.5
* @since: chispa 0.5a
* @package: core
* @subpackage: backend
*/

require_once("classes/backend_controller.class.php");
require_once("poker.class.php");
require_once("classes/poker_eval.class.php");

// How often to poll, in microseconds (1,000,000 μs equals 1 s)
define('MESSAGE_POLL_MICROSECONDS', 500000);
 
// How long to keep the Long Poll open, in seconds
define('MESSAGE_TIMEOUT_SECONDS', 30);
 
// Timeout padding in seconds, to avoid a premature timeout in case the last call in the loop is taking a while
define('MESSAGE_TIMEOUT_SECONDS_BUFFER', 5);

class Pokers extends BackendController {
	/**
    * Returns the section of the main menu, which is currently active
    * @return string The name of the section
    */
    protected function getSection() {
        return 'poker';
    }

	/**
	 * generate the title for the page
	 *
	 * @param string title Existing title (i.e. generated by modules)
 	 * @return string Title for the page
	 * @author Elias Müller
	 **/
	protected function generateTitle($title = '') {
		switch ($this->s->action) {
			case 'show':
				$title = 'Spiel';
				break;
		}
		return parent::generateTitle($title);
	}

	/**
    * Build site content depending on requested action.
    */  
    protected function buildSite() {
        switch ($this->s->action) {
            case 'show':
				$tpl = new Template('poker');
				$tables = PokerTable::getAll();

				foreach ($tables as $key => $table) {
					$table->active = (PokerPlayer::getActivePlayer($key) !== FALSE) ? TRUE : FALSE;
				}
				
				$tpl->assign('tables', $tables);
				$tpl->assign('current', current($tables));
				$content = $tpl->fetch('tables.html');
				break;
			default:
                break; 
        }
		parent::buildSite($content);
    }
    
    /**
    * Handle form action
    */
    protected function formAction() {
		$this->escapeFormVars();
		$player = PokerPlayer::getActivePlayer($this->s->params[0]);

        switch ($this->s->action) {
        	case 'bet':
        	case 'raise':
        		// check, if valid action
        		if ($this->validAction($player, $this->s->action, array('value' => $this->vars['value']))) {
        			// save new action
        			$actions = $player->table->game->getTurnActions();
        			$rel_value = ($this->s->action == 'bet') ? $this->vars['value'] : $this->vars['value'] - $actions['bet']->params['value'];

        			if ($this->saveAction($player, $this->s->action, array('value' => $this->vars['value'], 'rel_value' => $rel_value))) {
        				return true;
        			}
        		} 		
        		break;
        	case 'call':
        		// check, if valid action
        		if ($this->validAction($player, $this->s->action, array('value' => $this->vars['value']))) {
        			// save new action
        			if ($this->saveAction($player, $this->s->action, array('value' => $this->vars['value']))) {
        				return true;
        			}
        		} 		        	
        		break;
        	case 'fold':
        	case 'check':
        		// check, if valid action
        		if ($this->validAction($player, $this->s->action)) {
        			// save new action
        			if ($this->saveAction($player, $this->s->action)) {
        				return true;
        			}
        		} 		
        		break;
        	case 'join':
        		// player joins table
        		if (isset($this->s->params[0]) && $player == false) { // table id
    				// create new player & mark the player to join the table for the next game
        			$player = new PokerPlayer($this->s->params[0], $this->vars['seat'], $this->vars['stack']);
        			if ($player->save()) {
        				$table = PokerTable::getInstance($player->table->id, TRUE);
        				// start new game if sufficient player count & no game running
        				if (count($table->players) >= 2 && $table->game == FALSE) {
        					$this->gameNew($table);
        				}
        				return true;
        			}
        		}
        		break;
        	case 'leave':
        		// player leaves table
        		if (isset($this->s->params[0])) { // table id
        			// mark the player to leave the table after the current game
        			$player->join = false;
        			$player->leave = true;
        			if ($player->save()) {
        				return true;
        			}
        		}
        		break;
        	case 'poll':
        		// Close the session prematurely to avoid usleep() from locking other requests
				session_write_close();

				// Automatically die after timeout (plus buffer)
				set_time_limit(MESSAGE_TIMEOUT_SECONDS+MESSAGE_TIMEOUT_SECONDS_BUFFER);
				 
				// Counter to manually keep track of time elapsed (PHP's set_time_limit() is unrealiable while sleeping)
				$counter = MESSAGE_TIMEOUT_SECONDS;
				$new_actions = false;

				// initial request: return table situation, but no hand history
				if ($this->s->params[1] == 'short') {
					$new_actions = array();
				} else {
					// Poll for messages and hang if nothing is found, until the timeout is exhausted
					while ($counter > 0) {
					    // Check for new data
					    if ($new_actions = $player->table->getNewActions($this->vars['timestamp'])) {
					        break;
					    } else {
					        // Otherwise, sleep for the specified time, after which the loop runs again
					        usleep(MESSAGE_POLL_MICROSECONDS);
					 
					        // Decrement seconds from counter (the interval was set in μs, see above)
					        $counter -= MESSAGE_POLL_MICROSECONDS / 1000000;
					    }
					}
				}
				 
				// If we've made it this far, we've either timed out or have some data to deliver to the client
				if (is_array($new_actions)) {
					// Get actions since last request
					/*$last_request = $this->vars['timestamp'];
					foreach ($player->table->getNewActions($this->vars['timestamp']) as $key => $value) {
					}//*/

					// Get active (= seated) players
					$active_players = $player->table->getActivePlayers(FALSE);
					$players = array();
					foreach ($active_players as $p) {
						$players[] = array(
							'position' => $p->position,
							'stack' => $p->stack,
							'bet' => $p->bet,
							'fold' => ($player->last_action != NULL) ? $player->last_action->action : ''
						);
					}

					// Send data to client
				    // return json object with: timestamp, past actions, player info, valid actions
					$this->form['autocomplete'] = array(
						'timestamp' => time(),
						'log' => $new_actions,
						'players' => $players,
						'actions' => $this->validActions($player),
					);
				    return true;
				}
        		break;
        }
		return false;
    }
	    
    /**
     * Return currently valid actions
     *
     * @param object $player
     * @param string $action If not false: check, if a requested game action is valid in the current situation
     * @param array $params
     */ 
    private function validAction($player, $action = false, $params = Array()) {
        // get last deal and bet
        $actions = $player->table->game->getTurnActions();
        $valid = array();

    	// rule 1: player folded -> FALSE (no valid actions)
        /*if ($player->last_action->action == 'fold') {
        	return false;
        }
        else//*/
        if (array_key_exists('bet', $actions)) {
        	// rule 2: active bet/raise (unanswered) -> call/raise/fold
        	if ($actions['bet']->time > $player->last_action->time) {
        		$valid = array(
        			'call' => $actions['bet']->params['value'] - $player->bet, // value: realtive!
        			'raise' => $actions['bet']->params['value'] + $actions['bet']->params['rel_value'], // value: absolute!
        			'fold' => '');
        	} 
        	// rule 2a: own bigblind -> raise/check
        	elseif ($actions['bet']->action == 'blind' && $actions['bet']->params['blind'] == 'big' && $player->last_action === $actions['bet']) {
        		$valid = array(
        			'raise' => $actions['bet']->params['value'] + $actions['bet']->params['rel_value'], // value: absolute!
        			'check' => '');
        	}
        	// rule 3: answered bet / own bet/raise -> FALSE 
        	/*else { 
        		 false;
        	}//*/
        }
    	// rule 4: check (unanswered) -> check/bet
    	elseif ($actions['deal']->time > $player->last_action->time) {
    		$valid = array(
    			'bet' => $player->table->blinds['big'], 
    			'check' => '');
    	}
    	// rule 5: check (answered) -> FALSE 
    	/*else {
    		return false;
    	}//*/

    	// return TRUE/FALSE if request for specific action 
    	if ($action !== false) {
    		if (array_key_exists($action, $valid)) {
    			// check for params (bet/raise value > min bet/raise)
    			if (($action == 'bet' || $action == 'raise') && $valid[$action] > $params['value']) {
    				return false;
    			}
    			return true;
    		}
    		return false;
    	}

    	// return 'wait'-Action if its not the player's turn
    	if ($player != $player->table->getNextPlayer()) {
    		return array('wait');
    	}

    	// return valid actions for normal request
        return $valid;
    }

    /**
     * Save current action -> execute corresponding actions (deal cards etc.)
     *
     * @param object $player
     * @param string $action
     * @param array $params
     */ 
    private function saveAction($player, $action, $params = Array()) {
        $a = new PokerAction($player->table->game, $action, $params, $player);

		if ($a->save()) {
			// add new action to the game object
			$player->table->game->actions[] = $a;
			// and to the player
			$player->last_action = $a;

			// update player stack
			switch($action) {
				case 'bet':
				case 'raise':
					// reduce player stack size
    				$player->stack -= $params['value'] - $player->bet;

    				// increase player bet size
    				$player->bet = $params['value'];

    				$player->save();
					break;
				case 'call':
					// reduce player stack size
    				$player->stack -= $params['value'];

    				// increase player bet size
    				$player->bet += $params['value'];

    				$player->save();
					break;
			}
			
			// perform corresponding actions (deal cards, showdown)
			if ($action != 'bet' && $action != 'raise') {
				$active_players = $player->table->getActivePlayers();
				// all players except one folded -> game finished
				if (count($active_players) == 1) {
					$this->collectBets($table);
					$this->gameShowdown($player->table, $active_players);
				}
				else {
					// get next player
					$next = $player->table->getNextPlayer();
					// no valid actions for next player -> perform next game step 
					if ($this->validAction($next) == false) {
						$this->collectBets($table);
						// get last deal
	        			$actions = $player->table->game->getTurnActions();

	        			switch ($actions['deal']->action) {
	        				case 'river':
	        					$this->gameShowdown($player->table, $active_players);
	        					break;
	        				case 'turn':
	        					$this->gameDealCards($player->table, 'river');
	        					break;
	        				case 'flop':
	        					$this->gameDealCards($player->table, 'turn');
	        					break;
	        				case 'deal':
	        				default:
	        					$this->gameDealCards($player->table, 'flop');
	        					break;
	        			}
					}
				}
			}

			return true;
		}

		return false;
    }

    /**
     * Collect all bets into pot and erase player's temp. pots.
     */
    private function collectBets($table) {
    	foreach ($table->players as $key => $player) {
    		$table->game->pot += $player->bet;
    		$player->bet = 0;
    		$player->save();
    	}
    	$table->game->save();
    }

    /**
     * Perform game showdown. Compare hand values, determine the winner and start a new game.
     *
     * @param object $table The poker table object.
     * @param array $players The players remaining at the end of the game.
     */
    private function gameShowdown($table, $players) {
    	// TODO: rules for all in / split pots
    	if (count($players) > 1) {
    		// eval remaining player's hands and determine the winner
    		$score = array();
    		$public = array(
    			$table->game->turn, 
    			$table->game->river,
    		);
    		$action_c = array();
    		foreach ($players as $key => $player) {
    			$private = array($player->c1, $player->c2);
    			$score[$key] = PokerEval::score(array_merge($table->game->flop, $public, $private));
    			$action_c[$player->user->id] = array($player->c1->id, $player->c2->id);
    		}
    		$winner = max($score);
    		$winners = array_keys($score, $winner);
    		//$winner_cards = PokerEval::winnerCardsAndSuit($winner);
    		//$winner_cards = PokerEval::readable_hand($winner);
    	} else {
    		$winners = array(key($players));
    	}
    	
    	// transfer pot to the winning player's stack(s).
    	$winner_pot = $table->game->pot/count($winners);
    	$action_w = array();
    	foreach ($winners as $key) {
    		$players[$key]->stack += $winner_pot;
    		$players[$key]->save();
    		$action_w[$players[$key]->user->id] = $winner_pot;
    	}

    	// save corresponding action
    	$action = new PokerAction($table->game, 'showdown', array(
    		'winners' => $action_w,
    		'winning_hand' => PokerEval::readable_hand($winner),
    		'cards' => $action_c
    	));
    	$action->save();

    	// start a new game
    	$this->gameNew($table);
    } 

    /**
     * Deal cards.
     *
     * @param string $step May be deal, flop, turn or river.
     */
    private function gameDealCards($table, $step) {
    	// create corresponding actions
    	switch ($step) {
    		case 'deal':
    			foreach ($table->players AS $player) {
    				$params = array($player->cards[0]->id, $player->cards[1]->id);
    				$action = new PokerAction($table->game, $step, $params, $player);
    				$action->save();
    			}
    			break;
    		case 'flop':
				$params = array(
					$table->game->flop[0]->id,
					$table->game->flop[1]->id,
					$table->game->flop[2]->id
				);
				$action = new PokerAction($table->game, $step, $params);
				$action->save();
    			break;
    		case 'turn':
    		case 'river':
				$params = array($table->game->$step->id);
				$action = new PokerAction($table->game, $step, $params);
				$action->save();
    			break;
    	}
    }

    /**
     * Start a new game.
     */
    private function gameNew($table) {
    	$player_change = FALSE;
    	$new_game = ($table->game == FALSE) ? TRUE : FALSE;
    	$players = array();

    	// check for player leaves and joins
    	foreach ($table->players AS $key => $player) {
    		if ($player->leave == TRUE) {
    			$table->removePlayer($key);
    			$player->delete();
    			$player_change = TRUE;

    			// only one (or no) player left, so don't start a new game
    			$player_count = count($table->players);
    			if ($player_count <= 1) {
    				return FALSE;
    			} elseif ($player_count == 2) {

    			}
    			continue;
       		} elseif ($player->join == TRUE) {
    			$player->join = FALSE;
    			$player_change = TRUE;
    		}
		}

    	// create card deck and deal cards
    	$objDeck = new Deck();
		$objDeck->shuffle();

		foreach ($table->players AS $player) {
			// erase last action
			$player->last_action = NULL;
			// deal cards
			$player->c1 = $objDeck->next();
			$player->c2 = $objDeck->next();
			$player->save();

			$players[] = array(
				'position' => $player->position,
				'stack' => $player->stack,
				'name' => $player->user->realname
			);
		}

		// create new game object.
    	$game = new Poker($table);
    	$game->flop = array(
    		$objDeck->next(),
    		$objDeck->next(),
    		$objDeck->next()
		);
    	$game->turn = $objDeck->next();
    	$game->river = $objDeck->next();
    	$game->save();

    	// register game in table object
    	$table->game = $game;

    	// move D / SB / BB
     	$table->movePositions($new_game);
    	$table->save();

    	// create corresponding action
    	$action = new PokerAction($game, 'new', array(
    		'button' => $table->positions['dealer'],
    		'players' => $players
    	));
    	$action->save();

    	// Blinds
    	$this->postBlind($table->players[$table->positions['bigblind']], 'big');
    	$this->postBlind($table->players[$table->positions['smallblind']], 'small');

    	$this->gameDealCards($table, 'deal');
    }

    /**
     * Post Blinds.
     * 
     * @param object $player The player object.
     * @param string $blind Blind type (big, small).
     */
    private function postBlind($player, $blind) {
    	$value = $player->table->blinds[$blind];
    	$player->bet = $value;
    	$player->stack -= $value;

    	// new action
    	$action = new PokerAction($player->table->game, $blind.'blind', array('value' => $value), $player);
    	$action->save();
    }
}
?>