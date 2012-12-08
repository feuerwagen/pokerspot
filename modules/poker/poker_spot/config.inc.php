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

$rules = array(
	'title' => array(
		'mandantory' => true,
		'format' => 'string'
	),
	'stack_p1' => array(
		'mandantory' => true,
		'format' => 'int',
		'check' => 'range:1:inf'
	),
	'stack_p2' => array(
		'mandantory' => true,
		'format' => 'int',
		'check' => 'range:1:inf'
	),
	'value' => array(
		'format' => 'float',
		'check' => 'poker_raise:action'
	)
);

return array(
	// possible module actions
	'actions' => array(
		'form' => array(
			'create' => 'Spot anlegen',
			'delete' => 'Spot löschen',
			'update' => 'Alle Spots bearbeiten',
			'reload' => '',
		), 'backend' => array(
			'list' => 'Liste der Spots anzeigen',
			'create' => 'Spot anlegen',
			'delete' => 'Spot löschen',
			'own' => 'Eigene Spots bearbeiten',
			'update' => 'Alle Spots bearbeiten',
		)
	),
	// menu items for the backend
	'menu' => array(
		'poker' => array(
			array(
				'action' => 'list',
				'title' => 'Spots',
				'priority' => 3
			)
		)
	),
    // messages for form error handling
    'messages' => array(
    	'title' => array('Bitte gib einen Titel für den Spot ein!'),
    	'stack_p1' => array('Bitte gib eine Stacksize für Player 1 ein.', 'Die Stacksize für Player 1 ist keine Zahl!', 'Die Stacksize für Player 1 ist zu klein (<= 0)!'),
    	'stack_p1' => array('Bitte gib eine Stacksize für Player 2 ein.', 'Die Stacksize für Player 2 ist keine Zahl!', 'Die Stacksize für Player 2 ist zu klein (<= 0)!'),
    	'value' => array('', 'Mindestens ein Raise-Value ist keine Zahl!', 'Mindestens ein Raise-Value ist zu klein (Achtung: Immer absolute Werte angeben)!'),
    ),
	// form rules
	'rules' => array(
		'update' => $rules,
		'create' => $rules,
		'delete' => array(),
		'reload' => array(),
	),
);