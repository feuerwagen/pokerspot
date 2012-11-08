<?php
/**
* Handle form requests an redirect them to the responsible module.
* Request path: form/$section/$action ($section is the name of the module or class 
* to handle the request while $action is the method of it to execute).
* The class can only be used with jQuery forms or an equal javascript, because it returns
* a JSON array to report the success or the fail of the operation.
* 
* @uses: SiteController
* @author: Elias Müller
* @version: 0.1
* @since: chispa 0.1
* @package: core
* @subpackage: backend
*/

class cForms {
    /**
    * Handle the submitted form -> get basic errors or call the form events of the module
    */
    public function handleRequest() {
        // invoke the module
        // workaround because php accepts no variable as class name before ::
        $module = call_user_func($this->section."::getInstance");
    }
}
?>