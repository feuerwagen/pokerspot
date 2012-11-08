<?php
/**
* config file for one chispa module.
* It defines config params which won't be changed while site is online.
* Pre-definded sub arrays are:
*
* - actions: possible actions in the different site areas; if value is empty, this action is avaiable for all users
* - menu: menu items for the backend; array 'root' defines actions for the predefined root menu items
* - messages: error messages for form handling
* 		if one field needs more than one message (i.e. for mandantory check an the correct format)
* 		they will be used in the order of the checks (first: mandantory, second: format, third: special check)
* - rules: rules to check submitted values against; subarrays for each possible form action
*		possible keys for each value: mandantory (true, false), format (name of check method), check (method name:param)
*
* @version 0.1
*/

return array(
	// possible module actions
	'actions' => array(
		'form' => array(
			'submenu' => '',
			'reload' => '',
			'cleanlog' => '',
		),
		'backend' => array(
			'info' => 'Systemkonfiguration anzeigen',
		)
	),
	// menu items for the backend
	'menu' => array(
		'config' => array(
			array(
				'action' => 'info',
                'title' => 'System',
				'priority' => 1
			),
        ),
	),
    // messages for form error handling
    'messages' => array(),
	// form rules
	'rules' => array(
        'submenu' => array(),
		'reload' => array(),
		'cleanlog' => array(),
    ),
);