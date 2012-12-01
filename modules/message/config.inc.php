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
			'send' => '',
			'mark' => '',
			'reload' => '',
			'update' => '',
			'reply' => '',
			'delete' => '',
		), 'backend' => array(
			'send' => '',
			'list' => '',
			'show' => '',
			'update' => '',
			'reply' => '',
			'delete' => '',
		)
	),
	// menu items for the backend
	'menu' => array(
		'overview' => array(
			array(
				'action' => 'list',
				'title' => 'Nachrichten',
				'priority' => 3
			),
		),
	),
    // messages for form error handling
    'messages' => array(
		'subject' => array('', ''),
		'text' => array('Bitte eine Nachricht eingeben!', ''),
		'user' => array('Bitte mindestens einen Empfänger auswählen!'),
	),
	// form rules
	'rules' => array(
		'send' => array(
			'subject' => array(
				'format' => 'string'
			),
			'text' => array(
				'mandantory' => true
			),
		/*	'user' => array(
				'mandantory' => true
			),//*/
		),
		'update' => array(
			'subject' => array(
				'format' => 'string'
			),
			'text' => array(
				'mandantory' => true
			),
		),
		'reply' => array(
			'subject' => array(
				'format' => 'string'
			),
			'text' => array(
				'mandantory' => true
			),
		),
		'mark' => array(),
		'reload' => array(),
	),
);