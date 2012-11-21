<?php
/**
* main config file for the whole site.
* It defines side-wide config params which won't be changed while site is online.
* The params for each class should be placed in a sub-array with the name of the class as key.
*
* @version 0.1
*/

return array(
	// chispa settings
	'chispa'=>array(
		'version'=>'0.5', // don't change, unless you know, waht you're doing
		'path'=>'', // overwrite auto-recognized value if not empty
		'htmlpath'=>'', // overwrite auto-recognized value if not empty
		'root'=>'/Volumes/Dateien/elias/Sites/', // set DOCUMENT_ROOT, if wrong or no value set by server
		'debug'=>true, // display all warnings and errors
	),
	// site information
	'site'=>array(
		'title'=>'Poker',
		'version'=>'0.2 alpha',
		'has_frontend'=>false
	),
	// Navigation: Menu Structure for the backend
	'nav'=>array(
		'overview'=>'Übersicht',
		'poker'=>'Poker',
		'config'=>'System'
	),
    // Database: host name, db name, user and password
    'db'=>array(
		'host'=>'localhost',
		'database'=>'poker',
		'user'=>'root',
		'password'=>'x1y3z5a1'
	),
);