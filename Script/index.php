<?php


declare(strict_types=1);

if(!file_exists( __DIR__."/config/config.php")){
    header("Location: ./install");
    exit;
}

require_once __DIR__.'/config/config.php';

$app->run();