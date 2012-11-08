<?php
/**
* Backend controller for the blackboard module.
* 
* @uses: BackendController
* @author: Elias Müller
* @version: 0.2
* @since: chispa 0.5a
* @package: core
* @subpackage: backend
*/

require_once("classes/backend_controller.class.php");

class Blackboards extends BackendController {
	/**
    * Returns the section of the main menu, which is currently active
    * @return string The name of the section
    */
    protected function getSection() {
        return 'overview';
    }

    /**
    * Build site content depending on requested action.
    */  
    protected function buildSite() {
        switch ($this->s->action) {
            case 'show':
				//$content = '<h3>Es gibt auch wieder eine neue <a href="files/pdf/babel_handbuch.pdf">Anleitung für den Belegungskalender</a>!</h3>';
				$hooks = $this->addHook('Blackboard');
				foreach ($hooks AS $hook) {
					$elem = $hook->hookBlackboard();
					$elems['priorities'][] = ($elem['priority'] != '') ? $elem['priority'] : 1000;
					$elems['content'][] = $elem;
				}
				if (is_array($elems['priorities'])) {
					array_multisort($elems['priorities'], SORT_NUMERIC, SORT_ASC, $elems['content']);
					foreach ($elems['content'] AS $e) {
						$content .= '<div id="bb_'.$e['id'].'">'.$e['content'].'</div>';
					}
				}
                break;
        }
        parent::buildSite($content);
    }
    
    /**
    * Handle form action
    */
    protected function formAction() {
        switch ($this->params["action"]) {
        }
    }
    
    // module-specific methods start here
}
?>