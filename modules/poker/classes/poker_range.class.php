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
	public $range = array();
	public $pairs = array();

	public function __construct($range, $deck) {
		$range = $this->expandRange($range);
		$this->range= $range;
		if (is_array($range)) {
			foreach ($range as $hand) {
            	$this->cards = array_merge($this->cards, $this->expandPair($deck, $hand));
	        }
	        foreach ($this->cards as $key => $value) {
	        	$this->pairs[] = $value[0]->shortname().' / '.$value[1]->shortname();
	        }
		}
	}

	/**
	 * Transform EquiLab Range List into suitable pairs of cards in o/s notation.
	 *
	 * @param array $range The elements of the range list.
	 * @return array The corresponding card pairs
	 */
	private function expandRange($range) {
		if (is_array($range) && current($range) == "100%" || $range == '100%') {
			return $this->expandRange(array("A2+", "22+", "K2+", "Q2+", "J2+", "T2+", "92+", "82+", "72+", "62+", "52+", "42+", "32+"));
 		}

 		$list = array();
 		$cards = array('A', 'K', 'Q', 'J', 'T', '9', '8', '7', '6', '5', '4', '3', '2');

 		if (is_array($range)) {
 			foreach ($range as $element) {
 				$element = trim($element);
 				switch (strlen($element)) {
 					case 2:
 						if (substr($element, 0, 1) == substr($element, 1, 1)) {
	 						$list[] = $element;
	 					} else {
	 						$list[] = $element.'o';
	 						$list[] = $element.'s';
	 					}
 						break;
 					case 3:
 						if (substr($element, 2, 1) == '+') {
	 						if (substr($element, 0, 1) == substr($element, 1, 1)) {
		 						$list[] = substr($element, 0, 2);
		 						$index = array_search(substr($element, 0, 1), $cards);
		 						for ($i = 0; $i < $index; $i++) {
		 							$list[] = $cards[$i].$cards[$i];
		 						}
		 					} else {
		 						$list[] = substr($element, 0, 2).'o';
	 							$list[] = substr($element, 0, 2).'s';
	 							$index_end = array_search(substr($element, 1, 1), $cards);
	 							$index_start = array_search(substr($element, 0, 1), $cards) + 1;
		 						for ($i = $index_start; $i < $index_end; $i++) {
		 							$list[] = substr($element, 0, 1).$cards[$i].'s';
		 							$list[] = substr($element, 0, 1).$cards[$i].'o';
		 						}
		 					}
	 					} elseif (in_array(substr($element, 2, 1), array('s', 'o'))) {
	 						$list[] = $element;
	 					}
 						break;
 					case 4:
 						if (substr($element, 3, 1) == '+') {
 							$list[] = substr($element, 0, 3);
	 						$index_end = array_search(substr($element, 1, 1), $cards);
							$index_start = array_search(substr($element, 0, 1), $cards) + 1;
	 						for ($i = $index_start; $i < $index_end; $i++) {
	 							$list[] = substr($element, 0, 1).$cards[$i].substr($element, 2, 1);
	 						}
 						} else {
 							$list[] = $element;
 						}
 						break;
 					case 5:
 						if (substr($element, 0, 1) == substr($element, 1, 1)) {
 							$list[] = substr($element, 0, 2);
 						} else {
 							$list[] = substr($element, 0, 2).'s';
 							$list[] = substr($element, 0, 2).'o';
 						}

 						$index_end = array_search(substr($element, 4, 1), $cards);
						$index_start = array_search(substr($element, 1, 1), $cards) + 1;
 						for ($i = $index_start; $i <= $index_end; $i++) {
 							if (substr($element, 0, 1) == substr($element, 1, 1)) {
 								$list[] = $cards[$i].$cards[$i];
 							} else {
 								$list[] = substr($element, 0, 1).$cards[$i].'s';
		 						$list[] = substr($element, 0, 1).$cards[$i].'o';
 							}
 						}
 						break;
 					case 7:
 						$list[] = substr($element, 0, 3);
 						$index_end = array_search(substr($element, 5, 1), $cards);
						$index_start = array_search(substr($element, 1, 1), $cards) + 1;

 						for ($i = $index_start; $i <= $index_end; $i++) {
 							$list[] = substr($element, 0, 1).$cards[$i].substr($element, 2, 1);
 						}
 						break;
 				}
 			}

 			$final = array();
 			foreach ($list AS $hand) {
 				if (strlen($hand) < 4 && array_search(substr($hand, 0, 1), $cards) > array_search(substr($hand, 0, 1), $cards)) {
 					$final[] = substr($hand, 1, 1).substr($hand, 0, 1).substr($hand, 2);
 				} else {
 					$final[] = $hand;
 				}
 			}
 			return $final;
 		}

 		return false;
	}

	private function expandPair($deck, $hand) {
		$arrShorts = array('A', '2', '3', '4', '5', '6', '7', '8', '9', 'T', 'J', 'Q', 'K');
		$arrSuits = array(0 => 'c', 13 => 'd', 26 => 'h', 39 => 's');
		$cards = array();
		$val1 = array_search(substr($hand, 0, 1), $arrShorts);
		$val2 = array_search(substr($hand, 1, 1), $arrShorts);

		if (strlen($hand) == 4) {
			$card1 = array_search(substr($hand, 0, 1), $arrShorts) + array_search(substr($hand, 1, 1), $arrSuits);
			$card2 = array_search(substr($hand, 2, 1), $arrShorts) + array_search(substr($hand, 3, 1), $arrSuits);
			if ($deck->cardExists($card1) == true || $deck->cardExists($card2) == true) {
				$cards[] = array(
					new PokerCard($card1),
					new PokerCard($card2)
				);
			}
		} else {
			foreach ($arrSuits as $key => $value) {
				if (strlen($hand) == 2 || substr($hand, 2, 1) == 'o') {
					if ($deck->cardExists($key + $val1) == false) {
						continue;
					}
					foreach ($arrSuits as $k => $v) {
						if (($val1 != $val2 && $k == $key && substr($hand, 2, 1) == 'o') || $deck->cardExists($k + $val2) == false) {
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
		}
		
		return $cards;
	}

	public function getRandomPair() {
		shuffle($this->cards);
		return current($this->cards);
	}
}
?>