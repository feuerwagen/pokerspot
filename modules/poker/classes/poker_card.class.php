<?php
/**
* Represents a card in a poker card deck.
* 
* @author: Elias Müller
* @version: 0.1
* @since: poker 0.1
* @package: poker
*/

class PokerCard {
	public static $__tostring = null;
	public static $tostring = 'image';
	public static $image_path = '/images/__SUIT_____SHORT__.gif';
	public $id = -1;
	public $name = '';
	public $suit = '';
	public $suit_short = '';
	public $value = -1;
	public $short = '';
	public $pth = -1;

	public function __construct($f_iCard) {
		$iCard = (int)$f_iCard%52;
		$iSuit = floor($iCard/13);
		$iName = $iCard%13;

		$arrSuits = array('clubs', 'diamonds', 'hearts', 'spades');
		$arrSuitsShort = array('c', 'd', 'h', 's');
		$arrNames = array('ace', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'jack', 'queen', 'king');
		$arrShorts = array('A', '2', '3', '4', '5', '6', '7', '8', '9', 'T', 'J', 'Q', 'K');
		$arrValues = array(11, 2, 3, 4, 5, 6, 7, 8, 9, 10, 10, 10, 10);
		$arrPTHValues = array(14, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13);

		$this->id = $iCard;
		$this->suit = $arrSuits[$iSuit];
		$this->suit_short = $arrSuitsShort[$iSuit];
		$this->name = $arrNames[$iName];
		$this->value = $arrValues[$iName];
		$this->short = $arrShorts[$iName];
		$this->pth = $arrPTHValues[$iName];
	}

	public function __tostring() {
		if ( null === self::$__tostring || !is_callable(self::$__tostring) ) {
			self::$__tostring = create_function('$c', 'return \'<img src="/images/\'.$c->suit.\'_\'.$c->short.\'.gif" />\';');
		}
		return call_user_func(self::$__tostring, $this);
	}

	public function fullname() {
		return $this->name.' of '.$this->suit;
	}

	public function shortname() {
		return $this->short.$this->suit_short;
	}

	public static function random() {
		return new Card(rand(0,51));
	}
}

?>