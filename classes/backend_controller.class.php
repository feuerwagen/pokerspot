<?php
/**
* main class for the backend of chispa.
* Basic methods for the backend, like the authentication system.
* Request path: admin/$section/$page ($section is the section of the backend we're in 
* - typically provided my a module - while $page is a certain view inside of it)
* 
* @uses: SiteController
* @author: Elias Müller
* @version: 0.3
* @since: chispa 0.5a
* @package: core
* @subpackage: backend
*/
require_once("classes/site_controller.class.php");
require_once("classes/json.class.php");

abstract class BackendController extends SiteController {     
	/**
	 * possible actions of the module
	 *
	 * @var array
	 **/
	protected $actions;
	
	/**
	 * entries in the (sub) menu for the module 
	 *
	 * @var array
	 **/
	protected $menuItems;
	
	/**
	 * store form output (read in handleRequest()):
	 * - errors: form fields with errors (written in handleForm())
	 * - reload: ids of the site elems which should be reloaded after form submission (written in formAction())
	 *
	 * @var array
	 **/
	protected $form;
		
    /**
     * load config
     */
    protected function __construct($class = '') {
		parent::__construct();
		
		$this->s->auth();
		// load module config file
		if ($class != '')
			$class .= '/';
		$class .= substr(strtolower(get_class($this)), 0, -1);
		$this->config = require("modules/".$class."/config.inc.php");
		
		// load config for sub modules (actions / menu items)
		if (is_array($this->config['sub'])) {
			foreach ($this->config['sub'] AS $sub) {
				$sub_config = require("modules/".$class."/".$sub."/config.inc.php");
				foreach ($sub_config['actions'] AS $area => $actions) {
					foreach ($actions AS $name => $title)
						$this->config['actions'][$area][$sub.'_'.$name] = $title;
				}
				
				foreach ($sub_config['menu'] AS $section => $items) {
					foreach ($items AS $key => $item) {
						$item['action'] = $sub.'_'.$item['action'];
						if ($section == 'root')
							$this->config['menu'][$section][$key] = $item; 
						else
							$this->config['menu'][$section][] = $item; 
					}
				}
			}
		}
    }
	
	/**
    * Returns current section: Must be implemented in module.
    */
    abstract protected function getSection();
	
	/**
    * Handle form action: Must be implemented in module.
    */
    abstract protected function formAction();

	/**
    * Build the requestet HTML file
    */
    protected function buildSite($content = '', $clean = false) {
		if ($this->s->post['call'] == 'ajax') {
			if (empty(Error::$messages)) {
				echo $content;
			} else {
				foreach (Error::$messages AS $type => $texts) {
					$err = '';
					foreach ($texts AS $message)
						$err .= '<p>'.$message.'</p>';
					$errors .= '<div class="'.$type.'">'.$err.'</div>';
				}
				echo $errors;
			}
        } else {
	        $tpl = new Template();

	        if ($this->s->auth()) {
				$title = $this->generateTitle();
				$m = $this->generateNav();
				$b = $this->generateToolbar();
	        } else {
				// user is not logged in, so display login form
	            $tpl->assign('path', (($this->s->action == 'logout') ? '' : $this->s->getPath()));
				$tpl->assign('username', $this->s->post['username']);
				$tpl->assign('password', $this->s->post['password']);
			
	            $content = $tpl->fetch('login.html');
				$title = BackendController::generateTitle('Anmeldung');
			}
        
	        $tpl->assign('title', $title); // $title should be string
	        $tpl->assign('nav', $m['main']); // $nav should be array of links/titles/attributes
	        $tpl->assign('subnav', $m['sub']); // $subnav dito
	        $tpl->assign('path', $this->s->getPath());
			$tpl->assign('user', $this->s->user);
			$tpl->assign('clean', $clean);
	        $tpl->assign('messages', Error::$messages);
			$tpl->assign('buttons', $b); // buttons for right toolbar
	        $tpl->assign('content', $content); // $content should be a single string
	        $tpl->display('!.html');
		}
    }
    
	/**
	 * generate the title for the page
	 *
	 * @param string title Existing title (i.e. generated by modules)
 	 * @return string Title for the page
	 * @author Elias Müller
	 **/
	protected function generateTitle($title = '') {
		$title = $this->s->config['site']['title'].(($title != '') ? ' :: '.$title : '');
		return $title;
	}
	
	/**
    * Return the submenu items for the requested section used by this module.
	*
    * @param string $section The name of the section
    * @return array The menu items
    */
    public function getMenuItems($section = NULL) {	
        return ($section != NULL) ? $this->config['menu'][$section] : $this->config['menu'];
    }

	/**
    * Return the buttons for the right toolbar used by this module.
	*
    * @return array The buttons
    */
    public function getToolbarButtons($section) {	
        return false;
    }
	
	/**
     * Returns array of possible actions of the module depending on the area
	 *
	 * @return array
     * @author Elias Müller
     **/
    final public function registerActions() {
		$actions = $this->config['actions'][$this->s->area];
		
		if (is_array($actions)) {
			foreach($actions AS $name => $title)
				$a[] = $name;
			return $a;
		}
		return array();
    }

	/**
     * Returns array of restrictable actions of the module
	 *
	 * @return array
     * @author Elias Müller
     **/
    final public function registerRights() {
		$actions = $this->config['actions'];
		$rights = array();
		if (is_array($actions)) {
			foreach ($actions AS $area) {
				foreach ($area AS $right => $title) {
					if ($title != '') {
						$rights[$right] = $title;
					}
				}
			}
		}
		
		return $rights;
    }

    /**
    * Make the most basic decision about what to do in the backend.
    */
    final public function handleRequest() {
		$right = $this->right();
		
		if ($this->s->area == "form") { // check if form submitted
			if (empty(Error::$messages)) {
				// execute requested action
				$result = $this->handleForm();
			}
			
			// error occured while processing the form data?
			if ($result === false || !empty(Error::$messages)) { 
				if ($this->s->post['call'] == 'ajax') { // form submitted via ajax -> return JSON object
					$ret['messages'] = (!empty(Error::$messages)) ? Error::$messages : array('error' => array('Unbekannter Fehler'));
					if (count($this->form['errors']) > 0)
						$ret['fields'] = $this->form['errors'];
					if (count($this->form['reload']) > 0)
						$ret['reload'] = $this->form['reload'];
					if (count($this->form['dialog']) > 0)
						$ret['dialog'] = $this->form['dialog'];
					echo FastJSON::encode($ret);
				} elseif ($this->s->post['call'] == 'load') { // ajax load() -> return string with messages
					if (!empty(Error::$messages)) {
						foreach (Error::$messages AS $texts) {
							foreach ($texts AS $msg) {
								$message .= '<p>'.$msg.'</p>';
							}
						}
						echo $message;
					}
				} elseif ($this->s->post['call'] == 'load_text') { // ajax load() for form field -> return string with messages
					if (!empty(Error::$messages)) {
						$message = "Fehler:\r\n";
						foreach (Error::$messages AS $texts) {
							foreach ($texts AS $msg) {
								$message .= '– '.$msg."\r\n";
							}
						}
						echo $message;
					}
				} else { // form submitted normal -> display form + error message(s)
					$this->s->resetParams();
					$this->buildSite();
				}
			} else {
				if ($this->s->post['call'] == 'ajax') { // form submitted via ajax -> return JSON object
					$ret = array();
					if (count($this->form['reload']) > 0)
						$ret['reload'] = $this->form['reload'];
					if (count($this->form['autocomplete']) > 0)
						$ret = $this->form['autocomplete'];
					if (count($this->form['dialog']) > 0)
						$ret['dialog'] = $this->form['dialog'];
					echo FastJSON::encode($ret);
				} elseif ($this->s->post['call'] != 'load' && $this->s->post['call'] != 'load_text') { // form submitted normal -> display target site
					$this->s->resetParams();
					$this->buildSite();
				}
			}
		} elseif(!$this->s->auth()) { // check if user is logged in
			BackendController::buildSite();
		} elseif (!$this->s->user->hasRights($this->s->controller.':'.$right) && array_key_exists($right, $this->registerRights())) { // check if action is allowed for the user
			Error::addError('Du besitzt nicht die erforderlichen Rechte, um die Aktion '.$action.' auszuführen!');
			BackendController::buildSite();
		} else { // normal behaviour: generate the requested backend site 
			$this->buildSite();
		}
    }
    
    /**
    * Handle the submitted forms.
    */
    final protected function handleForm() {
        // check, if user is logged in (except for /user/login OR /booking/website)
        if ($this->s->auth() === false && !($this->s->controller == 'user' && $this->s->action == 'login') && !($this->s->controller == 'booking' && $this->s->action == 'website')) {
            Error::addError('<strong>Die Anfrage konnte leider nicht bearbeitet werden</strong><br/>Sie sind nicht im System angemeldet!', true);
            return false;
        }

		// check if action is allowed for the user
		$right = $this->right();
		$action = $this->s->controller.':'.$right;

		if (!($this->s->controller == 'user' && $this->s->action == 'login') && !($this->s->controller == 'booking' && $this->s->action == 'website') && !$this->s->user->hasRights($action) && array_key_exists($right, $this->registerRights())) { 
			Error::addError('<strong>Die Anfrage konnte leider nicht bearbeitet werden</strong><br/>Du besitzt nicht die erforderlichen Rechte, um die Aktion '.$action.' auszuführen!', true);
			return false;
		}
                
        // get the validation rules for this action
        $rules = $this->config['rules'][$this->s->action];
        
        // is it a valid action?
        if (!is_array($rules)) {
            Error::addError('<strong>Die Anfrage konnte leider nicht bearbeitet werden</strong><br/>Interner Fehler: Es sind keine Regeln für die Aktion "'.$this->s->action.'" im Modul "'.$this->s->controller.'" definiert. Bitte benachrichtigen Sie den Administrator!', true);
            return false;
        }

        // validate the form fields using the rules of the module for this action
		$vars = array_merge($this->s->get, $this->s->post);
        $check = new Check($vars, $rules, $this->config["messages"]);
		
        if (true === $check->run($escape)) {
            // replace unfiltered post vars
            $this->vars = $check->vars;
            // execute the requested action
            return $this->formAction();
        } else {
			$this->form['errors'] = $check->errorFields;

            foreach ($check->errorMessages AS $error) {
                Error::addWarning($error);
            }
			return false;
        }
    }
    
    /**
     * generate the backend toolbar
     *
     * @return array the toolbar items
     * @author Elias Müller
     **/
    final protected function generateToolbar() {  
        $modules = $this->s->modules;
		$nav = $this->s->getConfig('nav');
		$section = $this->getSection();
		$buttons = array();
        
		$new = false; 
        // walk through the modules and get entries for the toolbar
        foreach ($modules AS $module => $name) {
            $cur_module = $this->s->loadModule($module);
            $items = $cur_module->getToolbarButtons($section);
						
            // does the module provide buttons?
            if (!empty($items)) {
				foreach ($items AS $item) {
					if ($new) {
						$item['class'] .= ' separator';
						$new = false;
					}
					
					$link = $this->generateMenuLink($item, $module);
					if ($link !== false) {
						$priorities[] = $link['priority'];
						$buttons[] = $link;
					}
				}
            }
			$new = ($new) ? false : true;
        }

		if (count($buttons) == 0)
			$buttons = '';
		else
			array_multisort($priorities, SORT_NUMERIC, SORT_ASC, $buttons);

		return $buttons;
    }

	/**
     * generate the backend menu
     *
	 * @param string $section if set, only the submenu for the selected section will be generated
     * @return array the menu items (main menu and submenu)
     * @author Elias Müller
     **/
    final protected function generateNav($section = NULL) {  
        $modules = $this->s->modules;
		$nav = $this->s->getConfig('nav');
		$active_section = $this->getSection();
		$menu = array();
		$submenu = array();
		$nav_items = array();
        
        // walk through the modules and get entries for the menu
        foreach ($modules AS $module => $name) {
            $cur_module = $this->s->loadModule($module);
            $items = $cur_module->getMenuItems($section);

            // does the module provides menu items?
            if (!empty($items)) {
                if ($section !== NULL) { // get only items for current submenu
					foreach ($items AS $item) {
						$link = $this->generateMenuLink($item, $module);
						if ($link !== false) {
							$priorities[] = $link['priority'];
							$submenu[] = $link;
						}
					}
				} else { // get all items
					foreach ($items AS $sec => $sub) {
						if ($sec == 'root') { // main menu
							foreach ($sub AS $key => $item) {
								$nav_active[$key] = $module;
								if (!isset($nav_items[$key]))
									$nav_items[$key] = $item;
							}
						} else { // submenu
							foreach ($sub AS $item) {
								$link = $this->generateMenuLink($item, $module);
								if ($link !== false && !isset($nav_active[$sec])) // activate corresponding main menu item
									$nav_active[$sec] = false; 
								if ($link !== false && $sec == $active_section) { // items for current submenu?
									$priorities[] = $link['priority'];
									$submenu[] = $link;
								}
							}
						} // end menu type
					} // end items
				} // end section
            } // end menu items exist
        } // end modules

		if (count($submenu) == 0)
			$submenu = '';
		else
			array_multisort($priorities, SORT_NUMERIC, SORT_ASC, $submenu);

		if ($section !== NULL) {
			return $submenu;
		} else {
			// get used items in main menu 
			foreach ($nav AS $name => $title) {
				if (isset($nav_active[$name])) {
					$item = (is_array($nav_items)) ? $nav_items[$name] : array();
					$item['title'] = $title;
					$item['name'] = $name;
					
					$link = $this->generateMenuLink($item, $nav_active[$name]);
					if ($link !== false)
						$menu[$name] = $link;
				}
			}
			$m['main'] = $menu;
			$m['sub'] = $submenu;
			return $m;
		}
    }

	/**
	 * Build the array structure for a menu link from the given params
	 *
	 * @param array $item Elements:
	 * - title: Title for the link / Content
	 * - action, element: Defines what to perform when link is clicked
	 * - params: additional link params, should start with '?'
	 * - name: section name (only for main menu items)
	 * - class: css classes to set for this link
	 * - id: id to set for this link
	 * - disabled: disable link, if true (only for HoverMenu)
	 * - dialog: type of dialog (form, delete) to show when link is clicked (only for toolbar)
	 * - priority: defines, on which position the link will be shown in the menu (lower priority -> higher position)
	 * @return array Link information
	 * @author Elias Müller
	 **/
	final private function generateMenuLink($item, $module = false) {
		$config = $this->s->getConfig('site');
		
		if (is_array($item) && $item['title'] != '') {
			// menu item 
			if ($module !== false) {
				if (!isset($item['action'])) {
					$link = false;
				} else {
					$link = (($config['has_frontend'] === true) ? 'admin/' : '').$module;
				
					if (isset($item['action'])) {
						// display link only if user is allowed to see it
						$action = $module.':'.$item['action'];
						$m = $this->s->loadModule($module);

						if (!$this->s->user->hasRights($action) && array_key_exists($item['action'], $m->registerRights()))
							return false;
						$link .= '/'.$item['action'];
						if (isset($item['element'])) {
							$link .= '/'.$item['element'].'.html';
						}
						$link .= $item['params'];
					}
				}
			} else {
				$link = '#';
			}
			
			// (sub)menu item active, if current section or matching module (+ action + element)
			if (($this->s->controller == $module 
					&& (!isset($item['action']) || $this->right() == $item['action'])
					&& ((!isset($item['element']) && !($this->right() == $item['action'])) || $this->s->element == $item['element'])) 
				|| $item['name'] == $this->getSection()) {
				$item['class'] .= ' active';
			}
			if ($item['disabled'] === true) {
				$item['class'] .= ' disabled';
				$link = false;
			}

			if ($item['class'] != '')
				$attr .= ' class="'.trim($item['class']).'"';
			if ($item['id'] != '')
				$attr .= ' id="'.trim($item['id']).'"';
			
			$link = array(
	            'link' => $link,
	            'title' => $item['title'],
	            'attr' => $attr,
				'name' => $item['name'],
				'dialog' => $item['dialog'],
				'priority' => (($item['priority']) ? $item['priority'] : 1000)
	        );
			
			return $link;
		}
	}
}
?>