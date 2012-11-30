<?php
/**
* main class for chispa.
* This is the base class for the whole cms. It needs to be extended 
* by special classes for frontend and backend and also by modules.
* 
* @author: Elias Müller
* @version: 0.2
* @since: chispa 0.5a
* @package: core
* @subpackage: general
*/

abstract class SiteController { 
    /**
     * singleton object of bootstrap -> access post/get/params/config
     *
	 * @var object ChispaBootstrap
     */
    protected $s;

	/**
	 * the current user of the site
	 *
	 * @var object User 
	 **/
	protected $user;
	
	/**
     * the singleton objects of the modules
     */
    private static $objects = array();
	
	/**
     * the config array from config.inc.php (only the subarray for the current controller)
     */
    protected $config = array();

	/**
	 * filtered post/get vars when handling a form action
	 *
	 * @var array
	 **/
	protected $vars = array();
    
    protected function __construct() {        
        $this->s = cBootstrap::getInstance();
		session_start();
    }

	/**
    * get the singleton object of this class or create new
    * 
    * @param string $name name of the controller
    * @return object reference to the controller object
    */
    static public function getInstance($name) {
		if(!is_object(self::$objects[$name])) {
			$class = to_camel_case($name, TRUE);
            self::$objects[$name] = new $class();
        }
        return self::$objects[$name];
    }

	/**
	 * find the needed controller and create new object
	 *
	 * @return object reference to the controller object
	 * @author Elias Müller
	 **/
	static public function factory($class, $module, $action) {
		$name = $module.'s';
		if ($action != '') {
			$r = explode('-', $action);
			// sub or main module?
			if (count($r) == 2) {
				$name = $r[0].'s';
				$class = $r[0].'/'.$name;
			}
		}
				
		require_once("modules/".$module."/".$class.".class.php");
 
        return self::getInstance($name);
	}

	/**
	 * generate the current right string
	 *
	 * @return string the right
	 * @author Elias Müller
	 **/
	final protected function right() {
		$class = from_camel_case(substr(get_class($this), 0, -1));
		$right = ($this->s->controller != $class) ? $class.'-' : '';
		$right .= $this->s->action;
		return $right;
	}

    /**
     * register a new hook function and find all active modules, which provide this hook
     *
     * @return array the matching modules
     * @author Elias Müller
     **/
    final protected function addHook($name) {
		$modules = $this->s->modules;
		$hooks = array();
		
		foreach ($modules AS $module => $title) {
			$m = $this->s->loadModule($module);
			if (method_exists($m, 'hook'.$name) === true)
				$hooks[] = $m;
		}
		
		return $hooks;
    }

	/**
	 * escape all submitted vars, if neccesary
	 *
	 * @return void
	 * @author Elias Müller
	 **/
	final protected function escapeFormVars() {
		if (get_magic_quotes_gpc() == 0) {
			$db = DB::getInstance();
	        $this->vars = $db->escape($this->vars);
        }
	}

	/**
    * Handles the current request; Must be implemented in FrontendController / BackendController.
    */
    abstract public function handleRequest();
}
?>