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
		'version'=>'0.1 alpha',
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
	// Invoice / Stat: prices for special rooms 
	'prices'=>array(
		'saal'=>150,
		'keller'=>80,
		'schänke'=>80
	),
	// Mail: server settings
	'mail'=>array(
		'user'=>'m01ca773',
		'password'=>'YW6D9nc4DAxvLDfx',
		'host'=>'jugendburg-balduinstein.de',
		'smtp'=>'relay-auth.rwth-aachen.de',
		'smtp_port'=>465,
		'smtp_user'=>'em554929+rwth-aachen.de',
		'smtp_password'=>'9wtCu#va2',
	),
	// pdf: margins, fonts and other format options
	'pdf'=>array(
		'page'=>array(
			'format'=>'A4',
			'orientation'=>'P',
		),
		'creator'=>'TCPDF',
		'author'=>'Jugendburg Balduinstein',
		'unit'=>'mm',
		'margin'=>array(
			'header'=>60,
			'footer'=>10,
			'top'=>60,
			'bottom'=>25,
			'left'=>25,
			'right'=>25
		),
		'font'=>array(
			'main'=>'rotis_serif',
			'header'=>'rotis_sans_light',
		),
		'size'=>array(
			'main'=>12,
			'header'=>13,
			'title'=>14,
		),
		'header'=>array(
			'text'=>"auf der burg
65558 balduinstein
telefon: (06432) 83910
email: kontakt@jugendburg-balduinstein.de",
			'logo'=>'images/logo_invoice.png'
		)
	),
);