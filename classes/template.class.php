<?php
/**
* Extends the main Smarty class to enable changing template directories.
*
* @uses: Smarty
* @author: Elias Müller
* @version: 0.1
* @since: chispa 0.5a
* @package: core
* @subpackage: general
*/
require_once("classes/smarty/Smarty.class.php");

class Template extends Smarty {
    /**
    * Overwrite template directory path if Smarty is used by a module 
    */
    public function __construct($module = '') {
        if ($module != '')
            $this->template_dir = 'modules/'.$module.'/templates';
        parent::Smarty();
    }

	/**
     * trigger Smarty error
     *
     * @param string $error_msg
     * @param integer $error_type
     */
    function trigger_error($error_msg, $error_type = E_USER_WARNING) {
		// print the error message
        Error::addError("Smarty error: $error_msg", true);
		if ($error_type == E_USER_ERROR)
        	die("Smarty error!");
    }
}
?>