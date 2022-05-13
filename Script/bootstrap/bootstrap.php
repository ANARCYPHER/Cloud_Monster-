<?php

namespace CloudMonster;

use CloudMonster\Helpers\Help;
use CloudMonster\Helpers\Logger;

/**
 * Turn on output buffering
 */
ob_start();

/**
 * application
 */
const APP = true;

/**
 * application version
 */
const VERSION = '1.0';


/**
 * Start session
 * If session is not started, attempt to start it
 */
if (!isset($_SESSION)) {
    session_start();
}


/**
 * Error Reporting
 * Enable/ disable error reporting
 */
if (!DEBUG) {
    error_reporting(0);
} else {
    ini_set('display_error', 1);
    ini_set('error_reporting', E_ALL);
    error_reporting(-1);

}


/**
 * Database connection
 * Attempt to Connect to Database
 */
include (ROOT . '/app/core/Database.class.php');
$db = new Core\Database($config);



/**
 * initialize application class
 */
include_once ROOT . '/bootstrap/init.php';


$app = new App($db->get_config());

/**
 * Set default timezone
 */
date_default_timezone_set(APP::getConfig('timezone'));

/**
 * On/ Off Logger
 */
Help::isDev() ? Logger::on() : Logger::off();


