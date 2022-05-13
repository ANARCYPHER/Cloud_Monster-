<?php



use CloudMonster\Helpers\Logger;

include_once ROOT . '/vendor/autoload.php';

//application core functions
include_once ROOT . '/app/helpers/functions.php';


//init custom error handlers
set_error_handler("cloudMonsterErrorhandler");
register_shutdown_function("cloudMonsterShutdownHandler");



//Main application handler
include (ROOT . '/app/App.class.php');
include (ROOT . '/app/CPanel.class.php');



//auto load modules
spl_autoload_register(function($reqClass)
{
    $reqClass = str_replace('CloudMonster\\','App\\', $reqClass);
    $dirList = explode( '\\',$reqClass);
    $className = array_pop($dirList);
    $filePath = cleanDS(ROOT . '/' . strtolower(implode('/',$dirList)) . '/' . $className . '.class.php');

    if($file = fileExists($filePath)){
        include_once $file;
    }else{
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
        Logger::error("Fatal error: class not found.  {{ ' . $reqClass . ' }}", $bt);
//        trigger_error("Fatal error: class not found.  {{ ' . $reqClass . ' }}", E_USER_ERROR);
        return false;
    }
});
