<?php
/**
* loads the requested objects
* The bootstrap class analyses the url params and executes corresponding actions
* (i.e. including files, creating objects, running methods...). Additionally, it serves the
* general config params for all objects.
* 
* @author: Elias Müller
* @version: 0.4
* @since: chispa 0.5a
* @package: core
* @subpackage: general
*/

// load basic classes
require_once("classes/db.class.php"); // db access
require_once("classes/template.class.php"); // template engine
//require_once("classes/c_forms.class.php"); // form generation
require_once("classes/check.class.php"); // input sanitation / validation
require_once("classes/error.class.php"); // error handling
require_once("classes/date.class.php"); // date operations / formatting

/**
* autoloader for modules (main model)
*
* @version: 0.1
* @since: chispa 0.1
*/
function __autoload($class) {
    $class_orig = from_camel_case($class);
    $parts = explode('_', $class);
    if (is_dir("modules/".$class_orig))
        require_once("modules/".$class_orig."/".$class_orig.".class.php");
    elseif (count($parts) > 1 && is_dir("modules/".$parts[0]."/".$class_orig))
        require_once("modules/".$parts[0]."/".$class_orig."/".$class_orig.".class.php");
    else
        Error::addError('Das Modul '.$class.' ist im passenden Verzeichnis nicht vorhanden!', true);
}

/**
 * Translates a camel case string into a string with underscores (e.g. firstName -&gt; first_name)
 * @param    string   $str    String in camel case format
 * @return   string           $str Translated into underscore format
 */
function from_camel_case($str) {
    $str[0] = strtolower($str[0]);
    $func = create_function('$c', 'return "_" . strtolower($c[1]);');
    return preg_replace_callback('/([A-Z])/', $func, $str);
}

/**
 * Translates a string with underscores into camel case (e.g. first_name -&gt; firstName)
 * @param    string   $str                     String in underscore format
 * @param    bool     $capitalise_first_char   If true, capitalise the first char in $str
 * @return   string                            $str translated into camel caps
 */
function to_camel_case($str, $capitalise_first_char = false) {
    if($capitalise_first_char) {
      $str[0] = strtoupper($str[0]);
  }
  $func = create_function('$c', 'return strtoupper($c[1]);');
  return preg_replace_callback('/_([a-z])/', $func, $str);
}

final class cBootstrap {     
    /**
    * the request params (area, controller, action, element)
    */
    private $params = array();
    
    /**
    * the current get vars
    */
    private $get = array();
    
    /**
    * the current post vars
    */
    private $post = array();
    
    /**
    * the config vars from config.inc.php
    */
    private $config = array();
    
    /**
    * stores the active (= available) modules
    */
    private $active_modules = array();
    
    /**
    * the singleton object of this class
    */
    static private $object;

    /**
    * the current request path
    */
    private $path;

	/**
	 * the current user of the site
	 *
	 * @var object User 
	 **/
	public $user;
    
    /**
    * Save the url-params into private vars.
    *
    * @param string $config path to the config file
    */
    private function __construct ($config = '') {
		// load config array
        if(!empty($config))
			$this->config = require($config);

        // set request params
        $_GET['parts'] = explode('/', $this->requestPath());
		
		// get site paths
		if ($this->config['chispa']['path'] == '') {
			$this->config['chispa']['path'] = str_replace( '\\', '/', realpath(substr(dirname(__FILE__), 0, 0-strlen('classes'))));
			if (substr($this->config['chispa']['path'], -1) != '/')
				$this->config['chispa']['path'] .= '/';
		}
		
		// DOCUMENT_ROOT fixes
		if ((!isset($_SERVER['DOCUMENT_ROOT'])) OR (empty($_SERVER['DOCUMENT_ROOT']))) { // fix for IIS Webserver
			if(isset($_SERVER['SCRIPT_FILENAME'])) {
				$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 1-strlen($_SERVER['PHP_SELF'])));
			} elseif(isset($_SERVER['PATH_TRANSLATED'])) {
				$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 1-strlen($_SERVER['PHP_SELF'])));
			} else {
				$_SERVER['DOCUMENT_ROOT'] = $this->config['chispa']['root'];
			}
		} elseif (str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 1-strlen($_SERVER['PHP_SELF']))) != $_SERVER['DOCUMENT_ROOT'] && $this->config['chispa']['root'] != '') { // general fix
			$_SERVER['DOCUMENT_ROOT'] = $this->config['chispa']['root'];
		}
		
		if (isset($_SERVER['HTTP_HOST']) AND (!empty($_SERVER['HTTP_HOST'])) && $this->config['chispa']['htmlpath'] == '') {
			if(isset($_SERVER['HTTPS']) AND (!empty($_SERVER['HTTPS'])) AND strtolower($_SERVER['HTTPS'])!='off') {
				$this->config['chispa']['htmlpath'] = 'https://';
			} else {
				$this->config['chispa']['htmlpath'] = 'http://';
			}
			$this->config['chispa']['htmlpath'] .= $_SERVER['HTTP_HOST'];
			$this->config['chispa']['htmlpath'] .= str_replace( '\\', '/', substr($this->config['chispa']['path'], (strlen($_SERVER['DOCUMENT_ROOT']) - 1)));
		}
		
		// set error reporting
		ini_set('error_reporting', E_ALL & ~E_NOTICE);
		if ($this->config['chispa']['debug'] === true)
			ini_set("display_errors", 1);
		else
			ini_set("display_errors", 0);
			
        // determine basic site area (backend, form handling, frontend?)
		$area = (is_array($_GET["parts"])) ? array_shift($_GET["parts"]) : NULL;
        switch ($area) {
            case "admin":
				$this->params["area"] = 'backend';
				break;
            case "form":
                $this->params["area"] = 'form';
                break;
            case "site":
				$this->params["area"] = 'frontend';
				break;
            default:
                $this->params["area"] = ($this->config['site']['has_frontend'] === true ) ? 'frontend' : 'backend';
				if ($area !== NULL) array_unshift($_GET["parts"], $area);
                break;
        }

		$this->params["controller"] = (!empty($_GET["parts"][0])) ? filter_var($_GET["parts"][0], FILTER_SANITIZE_STRING) : (($this->params["area"] == "backend") ? 'blackboard' : '');
        $this->params["action"] = filter_var($_GET["parts"][1], FILTER_SANITIZE_STRING);
		if (is_array($_GET["parts"])) {
			$this->params["params"] = filter_var_array(array_slice($_GET["parts"], 2), FILTER_SANITIZE_STRING); // save optional, unnamed params
            $this->params["element"] = str_replace('.html','',end($this->params["params"])); // set element for legacy modules
        }
        unset($_GET["parts"]);
        
        // save the remaining get vars and delete $_GET array
		if (is_array($_GET))
        	$this->get = filter_var_array($_GET, FILTER_SANITIZE_STRING);
        unset($_GET);
        
        // save the post vars and delete $_POST array
        $this->post = $_POST;
        unset($_POST);
    }

    /**
     * Returns the requested URL path of the page being viewed.
     *
     * Examples:
     * - http://example.com/node/306 returns "node/306".
     * - http://example.com/drupalfolder/node/306 returns "node/306" while
     *   base_path() returns "/drupalfolder/".
     * - http://example.com/path/alias (which is a path alias for node/306) returns
     *   "path/alias" as opposed to the internal path.
     * - http://example.com/index.php returns an empty string (meaning: front page).
     * - http://example.com/index.php?page=1 returns an empty string.
     *
     * @return
     *   The requested Drupal URL path.
     *
     * @see current_path()
     */
    private function requestPath() {
      if (isset($this->path)) {
        return $this->path;
      }

      if (isset($_SERVER['REQUEST_URI'])) {
        // This request is either a clean URL, or 'index.php', or nonsense.
        // Extract the path from REQUEST_URI.
        $request_path = strtok($_SERVER['REQUEST_URI'], '?');
        $base_path_len = strlen(rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/'));
        // Unescape and strip $base_path prefix, leaving q without a leading slash.
        $this->path = substr(urldecode($request_path), $base_path_len + 1);
        // If the path equals the script filename, either because 'index.php' was
        // explicitly provided in the URL, or because the server added it to
        // $_SERVER['REQUEST_URI'] even when it wasn't provided in the URL (some
        // versions of Microsoft IIS do this), the front page should be served.
        if ($this->path == basename($_SERVER['PHP_SELF'])) {
          $this->path = '';
        }
      }
      else {
        // This is the front page.
        $this->path = '';
      }

      // Under certain conditions Apache's RewriteRule directive prepends the value
      // assigned to $_GET['q'] with a slash. Moreover we can always have a trailing
      // slash in place, hence we need to normalize $_GET['q'].
      $this->path = trim($this->path, '/');

      return $this->path;
    }
    
    /**
    * Returns requested param (modules or info from params array)
    *
    * @param requested var
    */
    public function __get($var) {
        switch ($var) {
            case "area":
            case "controller":
			case "action":
			case "element":
			case "params":
				return $this->params[$var];
				break;
            case "modules":
                return $this->active_modules;
				break;
			case "config":
			case "get":
			case "post":
				return $this->$var;
				break;
            default:
                return false;
                break;
        }
    }
    
    /**
    * get the singleton object of this class or create new
    * 
    * @param string $config path to the config file
    * @return cBootstrap reference to the bootstrap object
    */
    static public function getInstance($config = '') {
		if(!is_object(self::$object)) {
            self::$object = new cBootstrap($config);
        }
        return self::$object;
    } 
        
    /**
    * get the Config params for a class
    *
    * @param string $class_name the name of the class to configure 
    * @return array the set of config values
    */
    public function getConfig($class_name) {
        if(!empty($this->config[$class_name]))
            return $this->config[$class_name];
        return false;
    }

	/**
	 * compose the path to display the current site
	 *
	 * @return string path
	 * @author Elias Müller
	 **/
	public function getPath(){
		if ($this->params["controller"] != '') {
			$path = $this->params["controller"].'/';
			if ($this->params["action"] != '') {
				$path .= $this->params["action"].'/';
				if ($this->params["element"] != '') {
					$path .= $this->params["element"];
				}
			}
		}
		return $path;
	}
    
    /**
    * Determine the area of the site (frontend or backend) and load the adequate controller class
    * @return SiteController instance of the requested controller
    */
    public function loadController() {
        // check if necessary params given (form needs controller and action)
        if ($this->params["area"] == 'form' && (empty($this->params["controller"]) || empty($this->params["action"]))) { 
            Error::addError("<strong>Aufruf fehlerhaft:</strong> Angabe des Controllers und der Aktion nötig!", true);
            return false;
        }
        
        // load module list
        if (count($this->active_modules) == 0) {
    		$db = DB::getInstance();
            $sql = "SELECT name, title
                      FROM modules
                     WHERE active = 1";
            $result = $db->query($sql);
            do {
                $this->active_modules[$result->name] = $result->title;
            } while ($result->next());
        }
        
        $controller = $this->loadModule($this->params["controller"], true);
		
		if ($controller !== false) {
			$actions = $controller->registerActions();

			// set default action, if action not set yet
			if ($this->params['action'] == '') {
				$this->params['action'] = current($actions);
			} else {
				// edit current action, if submodule is loaded
				$parts = explode('-', $this->params['action']);
				if (count($parts) == 2)
					$this->params['action'] = $parts[1];
			}

			// check if action exists in module
			if (!in_array($this->params['action'], $actions))
				Error::addError('Das Modul <strong>'.$this->params["controller"].'</strong> unterstützt die gewählte Aktion ('.$this->params['action'].') leider nicht!', true); 

			$controller->handleRequest();
		} else {
			foreach(Error::$messages AS $type => $msg) {
				foreach($msg AS $m) {
					echo '<h2>Fehler:</h2>'.$m;
				}
			}
		}
    }
    
    /**
    * Load a module.
    * @param string $module_name The name of the module
	* @param bool $site_call true, if the loaded module should display a site (only to set by cBootstrap)
    * @return SiteController An instance of the requested module
    */
    public function loadModule($module_name, $site_call = false) {
        // check if requested module is active
        if (!array_key_exists($module_name, $this->active_modules)) {
            Error::addError('<strong>Ihre Anfrage konnte leider nicht bearbeitet werden</strong><br/>Interner Fehler: Modul "'.$module_name.'" ist nicht aktiv. Bitte benachrichtigen Sie den Administrator!', true);
            return false;
        } 
        
        // check if module exists
        if (!is_dir("modules/".$module_name)) {
            Error::addError('<strong>Ihre Anfrage konnte leider nicht bearbeitet werden</strong><br/>Interner Fehler: Modul "'.$module_name.'" nicht vorhanden. Bitte benachrichtigen Sie den Administrator!', true);
            return false;
        }
        
        $class = ($this->params["area"] == 'frontend') ? 'frontend' : 'backend';

		require_once('classes/site_controller.class.php');
		$action = ($site_call === true) ? $this->params['action'] : '';
		$controller = SiteController::factory($class, $module_name, $action);
        
        return $controller;
    }

	/**
    * checks if the user is logged in
    *
    * @return bool true if logged in, false if not
    */
    public function auth() {
	 	session_start();
        if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin'])
            return false;
        if (!is_object($this->user))
            $this->user = User::getInstance($_SESSION['loggedin']);
        return true;
    }

	/**
	 * set request params (area, controller, action, element) to previous values if form is submitted incorrectly
	 *
	 * @return void
	 * @author Elias Müller
	 **/
	public function resetParams($part = 'hash', $area = 'backend') {
		// get previous path from submitted url or hash value, if existing
		$parts = explode('#', $this->post['path']);
		$params = ($parts[1] != '' && $part == 'hash') ? explode('/', $parts[1]) : explode('/', $parts[0]);
		$el = explode('.', end($params));
		
		// remove useless elements (area / empty)
		if ($params[0] == '')
			array_shift($params);
		if ($params[0] == 'admin' || $params[0] == 'site' || $params[0] == 'form')
			array_shift($params);

		$this->params['area'] = $area;
		$this->params['controller'] = ($params[0] != '') ? $params[0] : '';
		$this->params['action'] = ($params[1] != '') ? $params[1] : '';
		$this->params['element'] = ($el[0] != '') ? $el[0] : '';
	}
}
?>