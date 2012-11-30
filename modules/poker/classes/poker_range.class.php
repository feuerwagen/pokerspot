<?php
/**
* Represents a poker card deck.
* 
* @author: Elias Müller
* @version: 0.1
* @since: poker 0.1
* @package: poker
*/

require_once("poker_card.class.php");

class PokerRange {
	private $cards = array();
	
	public function __construct($range, $deck) {
		$arrShorts = array('A', '2', '3', '4', '5', '6', '7', '8', '9', 'T', 'J', 'Q', 'K');

        foreach ($range as $value) {
            if (strlen($value) == 2) {
                // get all pairs of equal card values
            	$value = array_search(substr($value, 0, 1), $arrShorts);
            	$this->cards = array_merge($this->cards, $this->expandPair($deck, $value, $value));
            } else {
                // get all pairs of different card values, separated by color (s / o)
                // get all pairs of equal card values
            	$val1 = array_search(substr($value, 0, 1), $arrShorts);
            	$val2 = array_search(substr($value, 1, 1), $arrShorts);
            	$suited = (substr($value, 2, 1) == 'o') ? false : true;
            	$this->cards = array_merge($this->cards, $this->expandPair($deck, $val1, $val2, $suited));
            }
        }
	}

	private function expandPair($deck, $val1, $val2, $suited = false) {
		$arrSuits = array(0 => 'c', 13 => 'd', 26 => 'h', 39 => 's');
		$cards = array();
		foreach ($arrSuits as $key => $value) {
			if ($suited === false) {
				if ($deck->cardExists($key + $val1) == false) {
					continue;
				}
				foreach ($arrSuits as $k => $v) {
					if (($val1 != $val2 && $k == $key) || $deck->cardExists($k + $val2) == false) {
						// only offsuit & card exists
						continue;
					} elseif ($key + $val1 != $k + $val2) {
						// pairs / offsuit
						$cards[] = array(
							new PokerCard($key + $val1),
							new PokerCard($k + $val2)
						);
					}	
				}
			} else {
				if ($deck->cardExists($key + $val1) == false || $deck->cardExists($key + $val2) == false) {
					continue;
				}
				// suited
				$cards[] = array(
					new PokerCard($key + $val1),
					new PokerCard($key + $val2)
				);
			}
		}
		return $cards;
	}

	public function getRandomPair() {
		shuffle($this->cards);
		return current($this->cards);
	}
}
?>