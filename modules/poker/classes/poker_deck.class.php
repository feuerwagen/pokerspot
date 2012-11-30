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

class PokerDeck {
	private $iNextCard = 0;
	private $cards = array();
	
	public function __construct($f_bFill = true) {
		if ( $f_bFill ) {
			foreach ( range(0, 51) AS $iCard ) {
				array_push($this->cards, new PokerCard($iCard));
			}
		}
	}

	public function next() {
		if ( !isset($this->cards[$this->iNextCard]) ) {
			return null;
		}
		return $this->cards[$this->iNextCard++];
	}

	public function size() {
		return (count($this->cards)-$this->iNextCard);
	}

	public function addDeck(PokerDeck $objDeck) {
		$this->cards = array_merge($this->cards, $objDeck->cards);
		return $this;
	}

	public function addCard(PokerCard $objCard) {
		array_push($this->cards, $objCard);
		return $this;
	}

	public function removeCard(PokerCard $objCard) {
		foreach ($this->cards as $key => $value) {
			if ($value->id == $objCard->id) {
				unset ($this->cards[$key]);
				$this->replenish();
				break;
			}
		}
		return $this;
	}

	public function cardExists($iCard) {
		foreach ($this->cards as $key => $value) {
			if ($value->id == $iCard) {
				return true;
			}
		}
		return false;
	}

	public function shuffle() {
		return shuffle($this->cards);
	}

	public function replenish() {
		$this->iNextCard = 0;
		$this->shuffle();
	}

	public function __tostring() {
		return implode("\n", $this->cards);
	}
}
?>