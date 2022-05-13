<?php


/**
 * Database configuration
 */
const DB_HOST = 'MYSQL_HOST';
const DB_USER = 'MYSQL_USER';
const DB_PASS = 'MYSQL_PASS';
const DB_NAME = 'MYSQL_DB';

const APP_URI = 'SITE_URL';


/**
 * Application debug mode
 * default : false
 * val : true/false
 */
const DEBUG = false;

/**
 * Cloud file delete operator
 * default: false
 * val : true/false
 */
const CLOUD_FILE_DELETE = false;

/**
 * Cloud folder delete operator
 * default: false
 * val : true/false
 */
const CLOUD_FOLDER_DELETE = false;


/**
 * Application root directory
 */
define('ROOT', dirname(__FILE__, 2));

const TEMPLATE_DIR = 'public';

/**
 * Storage directory (for tmp files, cache, cookiz)
 */
const STORAGE_DIR = 'storage';


/**
 * String encryption key
 */
const ENCRYPTION_KEY = 'STR_ENC_KEY';

/**
 * New Thread secret token
 */
const THREAD_SECRET_TOKEN = 'THREAD_TOKEN';


/**
 * development mode
 */
const DEVELOPMENT_MODE = false;



/**
 * Other configuration
 */
$config = [
    'site_name' => 'PHP Cloud Monster Application',
    'timezone' => 'Europe/Berlin',
    'site_url'=> APP_URI
];


include_once ROOT . '/bootstrap/bootstrap.php';



