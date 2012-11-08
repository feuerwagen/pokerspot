<?php
/**
* Backend controller for the user module.
* 
* @uses: BackendController
* @author: Elias Müller
* @version: 0.2
* @since: chispa 0.5a
* @package: core
* @subpackage: backend
*/

require_once("classes/backend_controller.class.php");

class Systems extends BackendController {
	/**
    * Returns current section
    */
	protected function getSection() {
		return 'config';
	}
	
	/**
    * Build site content depending on requested action.
    */  
    protected function buildSite() {
		switch ($this->s->action) {
			case 'info':
				$site = $this->s->getConfig('site');
				$site['modules']['all'] = Module::getNumber();
				$site['modules']['active'] = Module::getNumber(true);
				$site['users'] = User::getNumber(true);
				
				$db = DB::getInstance();
				$server = array(
					'type' => $_SERVER['SERVER_SOFTWARE'],
					'php' => array(
						'version' => phpversion(),
						'safe_mode' => ini_get('safe_mode'),
						'magic_quotes_gpc' => ini_get('magic_quotes_gpc'),
						'magic_quotes_runtime' => ini_get('magic_quotes_runtime'),
						'gpc_order' => ini_get('gpc_order'),
						'memory_limit' => ini_get('memory_limit'),
						'max_execution_time' => ini_get('max_execution_time'),
						'disable_functions' => ini_get('disable_functions'),
						'sql_safe_mode' => ini_get('sql.safe_mode'),
						'include_path' => ini_get('include_path'),
					),
					'gd' => $this->getPhpModuleInfo('gd'),
					'db' => $db->info(),
				);
				
				$file = 'CHANGELOG.txt';
                $changelog = (is_readable($file)) ? file_get_contents($file) : false;
				$file = 'logs/error.log';
                $errorlog = (is_readable($file)) ? file_get_contents($file) : false;
				if ($errorlog == '')
					$errorlog = 'Errorlog ist leer!';

				$tpl = new Template('system');
				$tpl->assign('chispa', $this->s->getConfig('chispa'));
				$tpl->assign('site', $site);
				$tpl->assign('server', $server);
				$tpl->assign('changelog', $changelog);
				$tpl->assign('errorlog', $errorlog);
				$content = $tpl->fetch('info.html');
				break;
		}
        parent::buildSite($content);
    }
    
    /**
    * Handle form action
    */
    protected function formAction() {
        switch ($this->s->action) {
			case 'reload':
				// element: submenu
				$this->s->resetParams('form');
				$module = $this->s->loadModule($this->s->controller);
                $tpl = new Template('system');
                $tpl->assign('subnav', $this->generateNav($module->getSection()));
                $tpl->display('submenu.html');
				return true;
                break;
            case 'submenu':
				if ($this->s->element == '') {
					Error::addWarning('Fehler: ID des Untermenüs ist nicht definiert!');
					return false;
				}
                $section = str_replace('nav_', '', $this->s->element);
				$this->s->resetParams();
                $tpl = new Template('system');
                $tpl->assign('subnav', $this->generateNav($section));
                $tpl->display('submenu.html');
				return true;
                break;
			case 'cleanlog':
				if ($this->s->element == 'error') {
					$file=fopen("logs/error.log","w+");
					fclose($file);
				}
				return true;
				break;
        }
    }
    
    // module-specific methods start here

	/**
	 * parses phpinfo() output
	 * (1) get informations for a specific module (parameter $modulname)
	 * (2) get informations for all modules (no parameter for $modulname needed)
	 *
	 * if a specified extension doesn't exists or isn't activated an array will be returned:
	 * Array
	 *     (
	 *          [error] => extension is not available
	 *     )
	 *
	 *
	 * to get specified information on one module use (1):
	 * getPhpModuleInfo($moduleName = 'gd');
	 *
	 * to get all informations use (2):
	 * getPhpModuleInfo($moduleName);
	 *
	 *
	 * EXAMPLE OUTPUT (1):
	 * Array
	 * (
	 *    [GD Support] => Array
	 *        (
	 *            [0] => enabled
	 *         )
	 * ...
	 * )
	 *
	 *
	 * EXAMPLE OUTPUT (2):
	 * Array
	 * (
	 *     [yp] => Array
	 *         (
	 *              [YP Support] => Array
	 *                  (
	 *                      [0] => enabled
	 *                   )
	 *
	 *         )
	 * ...
	 * }
	 *
	 * foreach ($moduleSettings as $setting => $value)
	 * $setting contains the modul settings
	 * $value contains the settings as an array ($value[0] => Local Value && $value[1] => Master Value)
	 *
	 * @param $modulName string specify modul name or if not get all settings
	 * @return array see above for example
	 * @author Marco Jahn
	 */
	private function getPhpModuleInfo($moduleName) {
		$moduleSettings = array();
		ob_start();
		phpinfo(INFO_MODULES); // get information vor modules
		$string = ob_get_contents();
		ob_end_clean();

		$pieces = explode("<h2", $string); // get several modules

		foreach ($pieces as $val) {
			// perform a regular expression match on every module header
			preg_match("/<a name=\"module_([^<>]*)\">/", $val, $sub_key);

			// perform a regular expression match on tabs with 2 columns
			preg_match_all("/<tr[^>]*>
					<td[^>]*>(.*)<\/td>
					<td[^>]*>(.*)<\/td>/Ux", $val, $sub);

			// perform a regular expression match on tabs with 3 columns
			preg_match_all("/<tr[^>]*>
					<td[^>]*>(.*)<\/td>
					<td[^>]*>(.*)<\/td>
					<td[^>]*>(.*)<\/td>/Ux", $val, $sub_ext);

			if (isset ($moduleName)) { // if $moduleName is specified
				if (extension_loaded($moduleName)) { //check if specified extension exists or is loaded
					if ($sub_key[1] == $moduleName) { //create array only for specified $moduleName
						foreach ($sub[0] as $key => $val)
							$moduleSettings[strip_tags($sub[1][$key])] = array (strip_tags($sub[2][$key]));
					}
				} else { //specified extension is not loaded or doesn't exists
					$moduleSettings['error'] = 'extension is not available';
				}
			} else { // $moduleName isn't specified => get everything
				foreach ($sub[0] as $key => $val)
					$moduleSettings[$sub_key[1]][strip_tags($sub[1][$key])] = array (strip_tags($sub[2][$key]));

				foreach ($sub_ext[0] as $key => $val)
					$moduleSettings[$sub_key[1]][strip_tags($sub_ext[1][$key])] = array (strip_tags($sub_ext[2][$key]), strip_tags($sub_ext[3][$key]));
			}
		}
		return $moduleSettings;
	}
}
?>