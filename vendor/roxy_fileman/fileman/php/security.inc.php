<?php

//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);

// IMPORTANT: to gain access to Symfony session information,
// you must ensure that session_name('symfony')  gets called before session_start().
// Modify functions.php.inc to ensure this.

require_once(dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('qubit', 'prod', false);
sfContext::createInstance($configuration);

function checkAccess($action){
    if (!sfContext::getInstance()->user->hasCredential(array('administrator'), false)) {
      exit;
    }
}
?>
