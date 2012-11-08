<?php
/**
* Basic methods for all chispa modules.
* Event handlers which module can implement:
* 
* @author: Elias Müller
* @version: 0.1
* @since: chispa 0.1
* @package: core
* @subpackage: general
*/

abstract class cModule {
    /**
    * reference to the object, the module ist loaded into (i.e. the SiteController instance)
    */
    private $base;
    
    /**
    * internal identifier of the module instance (should be unique)
    */
    public $module_id;
    
    /**
    * save the reference to the base object
    *
    * @param object $base reference to the base object
    */
    public function loadBaseObject(&$base) {
        $this->base =& $base;
    }
    
    // optional event methods to implement
    
    /**
    * general: module should be installed
    */
    //public function onInstall() {}
    
    /**
    * general: module is loaded
    */
    //public function onLoad() {}
    
    /**
    * general: module is removed
    */
    //public function onUnload() {}
    
    /**
    * backend: main menu should be generated
    */
    //public function onBackendMenuGeneration() {}
    
    /**
    * backend: a module site should be displayed
    */
    //public function onBackendSiteRequest() {}
    
    /**
    * backend: blackboard (overview) should be built -> provide element
    */
    //public function onBlackboardRequest() {}
    
    /**
    * backend: editing form should be built
    */
    //public function onFormRequest($action) {}
    
    /**
    * backend: form is submitted -> get the validation options
    */
    //public function onFormSubmit($action) {}
    
    /**
    * backend: form inputs are checked -> handle the validated/sanatized form inputs 
    */
    //public function onFormAction($action) {}
    
    /**
    * backend: category is created
    */
    //public function onCatCreate() {}
    
    /**
    * backend: category type is selected -> provide additional options
    */
    //public function onCatTypeSelected() {}
    
    /**
    * backend: user right list should be built -> provide special rights
    */
    //public function onRightListRequest() {}
    
    /**
    * frontend: page (main content) should be built
    */
    //public function onSiteRequest() {}
    
    /**
    * frontend: block (sidebar) should be built
    */
    //public function onBlockRequest() {}
}
?>