<?php
/**
* provides methods for error handling
* 
* @author: Elias Müller
* @version: 0.5
* @since: chispa 0.5a
* @package: core
* @subpackage: general
*/

class Error {
	/**
	 * text messages and errors to display to the user
	 *
	 * @var array
	 **/
	public static $messages;
	
	/**
	 * receive a status message to display to the user
	 *
	 * @param string $message The Message 
	 * @return void
	 * @author Elias Müller
	 **/
	public static function addMessage($message) {
		self::$messages['message'][] = $message;
	}
    
	/**
	 * receive a warning (i.e. wrong form input) to display to the user
	 *
	 * @param string $message The Message 
	 * @return void
	 * @author Elias Müller
	 **/
	public static function addWarning($message, $trigger = false) {
		self::$messages['warning'][] = $message;
		if ($trigger)
			self::log($message, 'Chispa Warning');
			
	}

	/**
	 * receive a error message to display to the user
	 *
	 * @param string $message The Message 
	 * @return void
	 * @author Elias Müller
	 **/
	public static function addError($message, $trigger = false) {
		self::$messages['error'][] = $message;
		if ($trigger)
			self::log($message, 'Chispa Error');

	}
	
	/**
	 * log error or warning in log file
	 *
	 * @return void
	 * @author Elias Müller
	 **/
	static private function log($message, $type) {
		$s = cBootstrap::getInstance();
		$user = (is_object($s->user)) ? ' caused by '.$s->user->id : '';
		$dbg = debug_backtrace();
		$dbg = $dbg[1];
		$message = str_replace('<br/>', ' ', $message);
		$message = str_replace('<br>', ' ', $message);
		$msg = $type.': '.strip_tags($message).$user.' in '.$dbg['file'].' on line '.$dbg['line'];
		error_log($msg);
	}
}
?>