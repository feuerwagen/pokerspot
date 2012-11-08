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
			'login' => '',
			'update' => 'Benutzer bearbeiten',
			'create' => 'Benutzer hinzufügen',
			'delete' => 'Benutzer löschen',
			'self' => '',
			'reload' => ''
		),
		'backend' => array(
			'list' => 'Benutzer auflisten',
			'update' => 'Benutzer bearbeiten',
			'create' => 'Benutzer hinzufügen',
			'delete' => 'Benutzer löschen',
			'self' => '',
			'logout' => ''
		),
	),
	// menu items for the backend
	'menu' => array(
		'config' => array(
			array(
				'action' => 'list',
                'title' => 'Benutzer',
				'priority' => 1
			),
        )
	),
    // messages for form error handling
    'messages' => array(
        "username" => array("Bitte gib einen Benutzernamen ein!", "Der Benutzername darf nur aus Buchstaben und Zahlen bestehen!", "Der gewählte Benutzername ist schon vergeben!"),
        "realname" => array("Bitte gib den Namen des Benutzers ein!", "Der Name hat nicht das korrekte Format!"),
        "email" => array("Bitte eine E-Mail-Adresse eingeben!", "Die E-Mail-Adresse hat nicht das korrekte Format!"),
        "password" => array("Bitte ein Passwort eingeben!", "Das Passwort ist ungültig (keine Leerzeichen erlaubt, mindestens sechs Zeichen lang)!", "Das Passwort in beiden Feldern stimmt nicht überein!"),
		"password_confirm" => array("Bitte das Passwort zur Bestätigung eingeben!", "Das Passwort ist ungültig (keine Leerzeichen erlaubt, mindestens sechs Zeichen lang)!")
    ),
	// form rules
	'rules' => array(
        'self' => array(
            "realname" => array(
                "mandantory" => true,
                "format" => "string"
            ),
            "email" => array(
                "mandantory" => true,
                "format" => "email"
            ),
            "password" => array(
                "format" => "password",
                "check" => "equal:password_confirm"
            ),
            "password_confirm" => array(
                "format" => "password"
            )
        ),
		'update' => array(
            "realname" => array(
                "mandantory" => true,
                "format" => "string"
            ),
            "email" => array(
                "mandantory" => true,
                "format" => "email"
            ),
            "password" => array(
                "format" => "password",
                "check" => "equal:password_confirm"
            ),
            "password_confirm" => array(
                "format" => "password"
            )
        ),
		'create' => array(
            "username" => array(
                "mandantory" => true,
                "format" => "urlstring",
                "check" => "unique:users"
            ),
            "realname" => array(
                "mandantory" => true,
                "format" => "string"
            ),
            "email" => array(
                "mandantory" => true,
                "format" => "email"
            ),
            "password" => array(
                "mandantory" => true,
                "format" => "password",
                "check" => "equal:password_confirm"
            ),
            "password_confirm" => array(
                "mandantory" => true,
                "format" => "password"
            )
        ),
		'login' => array(
            "username" => array(
                "mandantory" => true,
                "format" => "urlstring"
            ),
            "password" => array(
                "mandantory" => true,
                "format" => "password"
            )
        ),
		'delete' => array(),
		'reload' => array(),
    ),
);