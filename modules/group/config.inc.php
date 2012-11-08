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
			'activate' => 'Benutzerrechte ändern',
			'create' => 'Benutzergruppen anlegen',
			'delete' => 'Benutzergruppen löschen',
			'reload' => ''
		),
		'backend' => array(
			'list' => 'Benutzergruppen auflisten',
			'delete' => 'Benutzergruppen löschen',
			'create' => 'Benutzergruppen anlegen'
		)
	),
	// menu items for the backend
	'menu' => array(
		'config' => array(
			array(
				'action' => 'list',
                'title' => 'Gruppen',
				'priority' => 2
			),
        )
	),
    // messages for form error handling
    'messages' => array(
		"name" => array("Bitte einen Namen für die Gruppe angeben!", "Der Name hat nicht das korrekte Format!", "Eine Gruppe mit dem Namen ist bereits vorhanden!"),
		"right" => array("Fehler: Es wurde kein Recht angegeben, das für die Gruppe aktiviert werden soll!", "Das Recht ist im falschen Format angeben (Nur Buchstaben und ein Doppelpunkt erlaubt)!"),
		"option" => array("Fehler: Das Feld <strong>option</strong> ist nicht gesetzt!")
    ),
	// form rules
	'rules' => array(
        "create" => array(
			"name" => array(
				"mandantory" => true,
				"format" => "name",
				"check" => "unique:groups"
			)
		),
		"activate" => array(
			"right" => array(
				"mandantory" => true,
				"format" => "rightstring"
			),
			"option" => array(
				"mandantory" => true
			)
		),
		'delete' => array(),
		'reload' => array(),
    ),
);