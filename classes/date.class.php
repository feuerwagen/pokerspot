<?php
/**
 * funktionen zur umformung und analyse von daten, wochentagen und monaten
 *
 * @package default
 * @version 0.3
 * @package: core
 * @subpackage: general
 * @author Elias Müller
 **/

class Date {
	//Variablen
	private $Date;
	private $DateArray = array();
	private $DateSeperator;
	
	static public $names = array(
		'month' => array('Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember')
	);

	// split date to year, month, day
	public function __construct($date, $format){
		// benötigte platzhalter (=konvertierbare fromate: -/ .Yymndj, 5 Zeichen) nicht zu finden?
		//if (!ereg('^([-/\ \.Yymndj]{5})$', $format)) 
		//return 1;

		$date = preg_replace('/[\/\ \.]/', '-', $date); // Trennzeichen im eingabedatum durch '-' ersetzen
		$_date = explode('-', $date); // eingabedatum zerlegen
		$date = sprintf('%02u', $_date[0]).sprintf('%02u', $_date[1]).sprintf('%02u', $_date[2]); // jeden teil des datums auf zwei stellen kürzen -> $date = 6 stellen

		$format = preg_replace('/[\/\ \.-]/', '', $format); // Trennzeichen aus in-Fromatangabe entfernen

		for ($i=0; $i<strlen($format); $i++) { // $in zeichenweise durchlaufen
			$v = $format[$i];

			if (preg_match('/[Y]/', $v))  // Y (z.B. 2006) -> vier zeichen
				$len = 4;
			elseif (preg_match('/[ymndj]/', $v)) // andere platzhalter -> zwei zeichen
				$len = 2;

			$dt[$v] = substr($date, 0, $len); // ab anfang von $date $len zeichen (2 oder 4) in $dt[] speichern
			$date = substr($date, $len); // $date um $dt[$v] am anfang kürzen

			if (preg_match('/[Yy]/', $v)) { // jahr?
				if ($len = 4) {
					$this->DateArray['Y'] = $dt[$v];
					$this->DateArray['y'] = substr($dt[$v], 2);
				} else {
					$this->DateArray['Y'] = ($dt[$v] <= date($v) ? '20'.$dt[$v] : '19'.$dt[$v]); // 19 oder 20 zufügen
					$this->DateArray['y'] = $dt[$v]; // 2 stellig
				}
			} elseif (preg_match('/[mn]/', $v)) { // Monat?
				$this->DateArray['m'] = $this->leadingZero($dt[$v]); // mit führender null
				$this->DateArray['n'] = (int) $dt[$v]; // null entfernen
			} elseif (preg_match('/[dj]/', $v)) {
				$this->DateArray['d'] = $this->leadingZero($dt[$v]); // mit führender null
				$this->DateArray['j'] = (int) $dt[$v]; // null entfernen
			}
		}

		$this->Date = $date;
		$this->weekday();
	}

	// return date elements
	public function __get($name){
		return $this->DateArray[$name];
	}
	
	// set date elements
	public function __set($name, $value){
		switch($name) {
			case 'w':
				break;
			case 'Y':
				$this->DateArray[$name] = $value;
				$this->DateArray['y'] = substr($value, 2);
				break;
			case 'y':
				$this->DateArray[$name] = $value;
				$this->DateArray['Y'] = (($value <= date('Y')) ? '20'.$value : '19'.$value);
				break;
			case 'n':
			case 'm':
				$this->DateArray['m'] = $this->leadingZero($value);
				$this->DateArray['n'] = (int) $value;
				break;
			case 'd':
			case 'j':
				$this->DateArray['d'] = $this->leadingZero($value);
				$this->DateArray['j'] = (int) $value;
				break;
		}
		$this->weekday();
	}
	
	// return month name in german
	public function monthName() {
		return self::$names['month'][$this->DateArray['n']-1];
	}
	
	// return short day name in german
	public function dayName() {
		$names = array('Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So');
		return $names[$this->DateArray['w']-1];
	}

	// find first work day of the month
	public function firstDayThisMonth() {
		$first_day_this_month = gmmktime("0","0","0",$this->DateArray['m'], "1", $this->DateArray['Y']);
		$wday = localtime($first_day_this_month, 1);
		$wday = $wday["tm_wday"];
		if ($wday == 0)
			$wday = 7;
		return $wday;
	}

	// calulate german holidays
	public function isHoliday() {
		$dstring = $this->returnDate('d-m');
		// statische Feiertage
		switch ($dstring) {
			//case '24-12': // Heiligabend
			case '25-12': // 1. Weihnachtstag
			case '26-12': // 2. Weihnachtstag
			case '01-01': // Neujahr
			case '01-05': // Tag der Arbeit
			case '03-10': // Tag der deutschen Einheit
			case '01-11': // Allerheiligen
				return true;
		}
		
		// bewegliche Feiertage
		$easter = new date(date ("d-m-Y", easter_date($this->DateArray['Y'])), 'd-m-Y');
		$days = array('2'); // Karfreitag
		foreach ($days AS $value) {
			$easter->Sub($value);
			if ($this->comparedates($easter) == 0)
				return true;
		}
		$days = array('3', '38', '11', '10'); // Ostermontag, Christi Himmelfahrt, Pfingstmontag, Fronleichnam
		foreach ($days AS $value) {
			$easter->Add($value);
			if ($this->comparedates($easter) == 0)
				return true;
		}	
		return false;
	}
	
	/**
	 * compare two dates (reference date given as param)
	 * 
	 * @param object $targetdate the reference date object for the comparison
	 * @return int -10 -> invalid dates / -1 this later than target / 1 this earlier than target / 0 both dates equal
	 */
	public function compareDates($targetdate) {
		if ($this->Validatedate() === false || $targetdate->Validatedate() === false) {
			//echo "<br/>Ungültiges Startdatum";
			return (-10);
		}
		//compare years
		if ($this->DateArray['Y']!=$targetdate->DateArray['Y']) {
			if ($this->DateArray['Y']>$targetdate->DateArray['Y']) {
				//echo "<br/>Startjahr ist größer als Endjahr";
				return -1;
			} else if ($this->DateArray['Y']<$targetdate->DateArray['Y']) {
				//echo "<br/>Endjahr ist größer als Startjahr";
				return 1;
			} else {
				//echo "<br/>Jahre nicht identifizerbar";
				return -10;
			}
		}
		if ($this->DateArray['m']==$targetdate->DateArray['m']) {
			if ($this->DateArray['d'] == $targetdate->DateArray['d']) {
				//echo "<br/>Daten sind gleich";
				return 0;
			} else if ($this->DateArray['d'] > $targetdate->DateArray['d']) {
				//echo "<br/>Starttag ist größer als Endtag";
				return -1;
			} else if ($this->DateArray['d'] < $targetdate->DateArray['d']) {
				//echo "<br/>Endtag ist größer als Starttag";
				return 1;
			} else {
				//echo "<br/>Tage nicht identifizierbar";
				return 0;
			}
		} else {
			if ($this->DateArray['m']>$targetdate->DateArray['m']) {
				//echo "<br/>Startmonat ist größer als Endmonat";
				return -1;
			} elseif ($this->DateArray['m']<$targetdate->DateArray['m']) {
				//echo "<br/>Endmonat ist größer als Startmonat";
				return 1;
			}
		}
		//echo "<br>Gültige Daten";
		return 1;
	}

	// Überprüfung auf korrektes Datum
	public function validateDate() {
		if (($this->DateArray['m']<1)||($this->DateArray['m']>12)) {
			//echo "<br/> Ungültiger Monat";
			return false;
		}
		// doing the math using K Maps for calculating whether it is a leap year or not, I got the following formula
		// A B` C`  +  A B C
		// A - divisible by 4; B - divisible by 100; C - divisible by 400
		$leapday = 0;
		$A = (($this->DateArray['Y']%4)==0)?1:0;
		$B = (($this->DateArray['Y']%100)==0)?1:0;
		$C = (($this->DateArray['Y']%400)==0)?1:0;
		$R = ($A && (!($B)) && (!($C))) || ($A && $B && $C);

		//verifying the day
		//months with 31 days
		$month31 = (($this->DateArray['m']==1)||($this->DateArray['m']==3)||($this->DateArray['m']==5)||($this->DateArray['m']==7)||($this->DateArray['m']==8)||($this->DateArray['m']==10)||($this->DateArray['m']==12))?1:0;

		if (($R && ( ($this->DateArray['m']==2) && (($this->DateArray['d']<1) || ($this->DateArray['d']>29)) ))||(!$R && (($this->DateArray['m']==2) && (($this->DateArray['d']<1) || ($this->DateArray['d']>28))))) {
			//echo "<br/>Ungültiger Tag";
			return false;
		}
		else
		if ( ($month31 && ( ($this->DateArray['d']<1) || ($this->DateArray['d']>31) ) ) || (!$month31 && ( ($this->DateArray['d']<1) || ($this->DateArray['d']>30) ) ) ) {
			//echo "<br/>Ungültiger Tag";
			return false;
		}
		return true;
	}

	// formatiertes Datum ausgeben
	public function returnDate($format) {
		if ($this->validateDate()) {
			for ($i=0; $i<strlen($format); $i++) { // alle zeichen von $out durchlaufen
				$v = $format[$i];

				// trennzeichen oder platzhalter?
				$returnString .= (!preg_match('/[Yymndjw]/', $v)) ? $v : $this->DateArray[$v];
			}
			return $returnString; // konvertiertes datum ausgeben
		}
	}

	// return timestamp
	public function returnTimestamp() {
		return gmmktime("0","0","0",$this->DateArray['m'],  $this->DateArray['d'], $this->DateArray['Y']);
	}
	
	// number of days of the current month
	public function daysOfMonth() {
		// herausfinden, ob Schaltjahr
		$A = (($this->DateArray['Y']%4)==0)?1:0;
		$B = (($this->DateArray['Y']%100)==0)?1:0;
		$C = (($this->DateArray['Y']%400)==0)?1:0;
		$R = ($A && (!($B)) && (!($C))) || ($A && $B && $C);

		// Tage des Monats berechnen
		if (($this->DateArray['m']==1)||($this->DateArray['m']==3)||($this->DateArray['m']==5)||($this->DateArray['m']==7)||($this->DateArray['m']==8)||($this->DateArray['m']==10)||($this->DateArray['m']==12))
			$DaysOfMonth = 31;
		elseif ($R && ($this->DateArray['m']==2))
			$DaysOfMonth = 29;
		elseif (!$R && ($this->DateArray['m']==2))
			$DaysOfMonth = 28;
		else
			$DaysOfMonth = 30;

		return $DaysOfMonth;
	}

	// zum aktuellen Datum $days Tage addieren
	public function add($days) {
		$this->DateArray['j'] = $this->DateArray['j']+$days;

		while ($this->DaysOfMonth() < $this->DateArray['j']) { // neuer Monat, bis Tagesanzahl in Monat 'passt'
			$DaysOfMonth = $this->DaysOfMonth();
			$this->DateArray['n']++;
			if ($this->DateArray['n'] >= 13) { // neues Jahr?
				$this->DateArray['n'] = 1;
				$this->DateArray['Y']++;
			}
			$this->DateArray['j'] = $this->DateArray['j']-$DaysOfMonth; // Tage, die in 'altem' Monat liegen, abziehen
		}

		// andere Datumsvariablen anpassen
		$this->DateArray['d'] = $this->LeadingZero($this->DateArray['j']);
		$this->DateArray['m'] = $this->LeadingZero($this->DateArray['n']);
		$this->DateArray['y'] = substr($this->DateArray['Y'], 2);
		$this->weekday();
		return true;
	}

	// vom akutellen Datum $days Tage abziehen
	public function sub($days) {
		$this->DateArray['j'] = $this->DateArray['j']-$days;

		while ($this->DateArray['j'] <= 0) { // neuer Monat, bis Tagesanzahl in Monat 'passt'
			$this->DateArray['n']--;
			$DaysOfMonth = $this->DaysOfMonth();
			if ($this->DateArray['n'] <= 0) { // neues Jahr?
				$this->DateArray['n'] = 12;
				$this->DateArray['Y']--;
			}
			$this->DateArray['j'] = $this->DateArray['j']+$DaysOfMonth; // Tage, die in 'altem' Monat liegen, abziehen
		}

		// andere Datumsvariablen anpassen
		$this->DateArray['d'] = $this->LeadingZero($this->DateArray['j']);
		$this->DateArray['m'] = $this->LeadingZero($this->DateArray['n']);
		$this->DateArray['y'] = substr($this->DateArray['Y'], 2);
		$this->weekday();
		return true;
	}
	
	// which day of the week?
	private function weekday() {
		$DayInfo = gmmktime("0","0","0",$this->DateArray['m'],  $this->DateArray['d'], $this->DateArray['Y']);
		$wday = localtime($DayInfo, 1);
		$wday = $wday["tm_wday"];
		if ($wday == 0)
			$wday = 7;
		$this->DateArray['w'] = $wday;
	}
	
	// used by __construct()
	private function leadingZero($date) {
		if (strlen($date) == 1)
			$date = '0'.$date;
		return $date;
	}
}
?>