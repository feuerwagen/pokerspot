<?php
/**
* Backend controller for the module module (sic!); Manages the module list.
* 
* @uses: BackendController
* @author: Elias Müller
* @version: 0.2
* @since: chispa 0.5a
* @package: core
* @subpackage: backend
*/

require_once("classes/backend_controller.class.php");

class Modules extends BackendController {
	/**
    * Returns the section of the main menu, which is currently active
    * @return string The name of the section
    */
    protected function getSection() {
        return 'config';
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
			case 'list':
				$title = 'Module';
				break;
		}
		return parent::generateTitle($title);
	}
	
	/**
    * Build site content depending on requested action.
    */  
    protected function buildSite() {
        switch ($this->s->action) {
            case 'list':
                $content = $this->listModules();
                break;
        }
        parent::buildSite($content);
    }
          
    /**
    * Handle form action
    */
    protected function formAction() {
        switch ($this->s->action) {
			case 'activate':
				if ($this->s->element != '') {
					$module = Module::getInstance($this->s->element);
					$module->active = ($this->vars['option'] == 'set') ? true : false;
					if ($module->save())
						return true;
				} else 
					Error::addWarning('Fehler: Es wurde kein Modul ausgewählt!');
				return false;
				break;
        }
    }
    
    // module-specific methods start here
    
    /**
    * List all availiable Modules
    */
    private function listModules() {
        // get all modules listed in the database
        $db = DB::getInstance();
        $sql = "SELECT name
                  FROM modules";
        $result = $db->query($sql);
        
        do {
            $mod_db[$result->name] = '';
        } while ($result->next());        
        
        // get all available modules from the file system
        $dir = opendir('modules/');
        // run through module directory
        while (false !== ($name = readdir($dir))) {
            // only real folders
            if ($name != "." && $name != ".." && is_dir('modules/'.$name)) {
                // is there any MODULENAME.info file?
                $file = 'modules/'.$name.'/'.$name.'.info';
                if (is_readable($file)) {
                    // read module information
                    $info = array();
                    $raw = file($file);
                    foreach ($raw AS $line) {
                        $parts = explode(':', $line);
						if (strtolower($parts[0]) == 'requires') {
							$req = explode(',', trim($parts[1]));
							if (count($req) > 0 && $req[0] != '')
								$info[strtolower($parts[0])] = $req;
						} else {
							$info[strtolower($parts[0])] = trim($parts[1]);
						}
                    }
         
                    // is all given info correct?
                    if ($info['id'] == $name && !empty($info['name']) && !empty($info['version']) && !empty($info['group']) && !empty($info['description'])) {				
						$module = Module::getInstance($name);
						unset($mod_db[$name]);

						$module->version = $info['version'];
						$module->requires = $info['requires'];
						$module->name = $info['name'];
						$module->description = $info['description'];
						$module->save();
						
                        $groups[$info['group']][] = $module;
						$modules[$module->id] = $module;
                    }
                }
            }
        }
        closedir($dir);

		// are there any module entries in the db for which no module folder exist?
		if (count($mod_db) > 0 )
			Error::addError('Achtung: Zu einigen Einträgen in der Datenbank konnte kein Modul gefunden werden!');
			
		// resolve dependancies
		foreach($modules AS $m) {
			$activable = $deact = true;
			
			// all required modules active?
			if (is_array($m->requires)) {
				foreach($m->requires AS $r) {
					if ($r->active === false) {
						$activable = false;
						break;
					}
				}
			}
			$m->can_act = $activable;
			
			// any active modules which rely on the current one?
			foreach($modules AS $d) {
				if ($d->active === true && is_array($d->requires)) {
					foreach($d->requires AS $r) {
						if ($r === $m->id) {
							$deact = false;
							break;
						}
					}
				}
			}
			$m->can_deact = $deact;
		}
			
        $tpl = new Template('module');
        $tpl->assign('modules', $modules);
		$tpl->assign('groups', $groups);
        $tpl->assign('permission', $this->s->user->hasRights('module:activate'));

        return $tpl->fetch("modules_table.html");
    }
}
?>