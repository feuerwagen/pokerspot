<?php
/**
* Bulids forms including all input fields, validation options and field descriptions. 
* For every new object, the necessary instructions for the form have to be given.
* Then, a fieldset has to be defined. No input field could be insertet without having a fieldset.
* Afterwards, the input fields can be inserted. This may be continued for more fieldsets.
* Finally the generate method returns the finished form, 
* if the label for the submit button has been set before.
* 
* @author: Elias Müller
* @version: 0.1
* @since:
* @package: core
* @subpackage: general
*/

class cForms {
    public function __construct($action, $name, $class="cmxform", $method="post") {
    }
    
    /**
    * Generate the HTML-Form
    */
    public function generate() {
    }
    
    /**
    * Set the rules for pre and post submit validation of form data
    *
    * @param array
    */
    public function setRules($rules) {
    }
    
    // FROM HERE: functions to generate form elements
    public function fieldset($legend) {
    }
    
    public function buttons($name_submit, $name_cancel='') {
    }
    
    public function inputRadiobutton($options, $selected='') {
    }
    
    public function inputCheckbox($options, $checked='') {
    }
    
    public function inputSelect($options, $selected='', $size=1, $multiple=0) {
    }
     
    public function inputTextarea($name, $rows, $class='', $style='') {
    }
    
    public function inputTinymce($name, $rows, $class='', $style='') {
    }
    
    public function inputImage($name) {
    }
}
?>