<?php
/**
* the centerpoint of every action on chispa-sites.
* This script loads the config file, calls the bootstrap and initiates the controller action.
*
* @author: Elias Müller
* @version: 0.2
* @since: chispa 0.1
* @package: core
*/

// path to the configuration file
$config = dirname(__FILE__).'/config.inc.php';

// load bootstrap object to handle the request
require_once("classes/c_bootstrap.class.php");
$loader = cBootstrap::getInstance($config);

// load the controller-object for this site and execute requested action
$site = $loader->loadController();
?>