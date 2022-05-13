<?php




function dnd($data, $t = false) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    if(!$t) die();
}

function cloudMonsterErrorhandler($level, $msg, $file, $line){
    $eType = getErrorType($level);
    showErrLog($msg,$eType, $file, $line);
}


function cloudMonsterShutdownHandler()
{
    $lastError = error_get_last();
    if(isset($lastError['type'])){
        $eType = getErrorType($lastError['type']);

        switch ($lastError['type'])
        {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_PARSE:
                showErrLog($lastError['message'], $eType, $lastError['file'],  $lastError['line']);
        }
    }

}

function getErrorType($level): string
{

    switch ($level) {
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_PARSE:
            $eType = 'fatal';
            break;
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
            $eType = 'error';
            break;
        case E_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_USER_WARNING:
            $eType = 'warning';
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $eType = 'info';
            break;
        case E_STRICT:
            $eType = 'debug';
            break;
        default:
            $eType = 'warning';
    }
    return $eType;
}

function showErrLog($msg,$level, $file, $line){

    @ob_end_clean();
    $title = ($level == 'fatal' || $level == 'error') ?  'error occurred' : $level;
    $root = defined('ROOT') ? ROOT : dirname(__FILE__, 3) ;
    $baseDir = $root . '/app/vars/__app_pvt__';
    $appUri = defined('APP_URI') ? APP_URI : '';
    $logo = $appUri . '/public/assets/cpanel/img/logo.png' ;


    if(class_exists('\CloudMonster\Helpers\Logger')){
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $bt[0]['file'] = $bt[0]['args'][2];
        $bt[0]['line'] = $bt[0]['args'][3];
        if($level == 'fatal' || $level == 'error'){
            \CloudMonster\Helpers\Logger::error($msg, $bt);
        }else{
            \CloudMonster\Helpers\Logger::warn($msg, $bt);
        }
    }


    $page = $baseDir . '/error_handler_page.php';
    if(file_exists($page)){

        include_once $page;

    }else{
        die('App stopped.');
    }
    @ob_flush();
    @flush();

exit;

}

function includeUserTemplatePart($dir = ''){
    include_once ROOT . '/' . TEMPLATE_DIR . '/pages/frontend/user/' . $dir;
}

function includeAdminTemplatePart($dir = ''){
    include_once ROOT . '/' . TEMPLATE_DIR . '/pages/cpanel' . $dir . '.php';
}

function getTemplatePath(): string
{
    return  ROOT . '/' . TEMPLATE_DIR . '/pages/cpanel';
}

function buildURIPath($path = ''){
    echo  APP_URI . '/' . $path;
}

function getURIPath($path = ''){
    return  APP_URI . '/' . $path;
}

function buildResourceURI($resource){
    echo APP_URI .  '/' . TEMPLATE_DIR . '/' . $resource;
}
function getResourceURI($resource){
    return APP_URI .  '/' . TEMPLATE_DIR . '/' . $resource;
}
function imgUri($resource = ''){
    echo APP_URI .  '/' . TEMPLATE_DIR . '/assets/cpanel/img/' . $resource;
}

function getImgUri($resource = ''){
    return APP_URI .  '/' . TEMPLATE_DIR . '/assets/cpanel/img/' . $resource;
}

function _e($var, $obj = []){
    $e = '';
    if(!empty($obj) && is_array($obj)){
        if(array_key_exists($var, $obj)){
            $e =  $obj[$var];
        }
    }else{
        $e = $var;
    }
    echo !empty($e) ? htmlentities($e) : $e;
}
function _g($var, $obj = []){
    $e = '';
    if(!empty($obj) && is_array($obj)){
        if(array_key_exists($var, $obj)){
            $e =  $obj[$var];
        }
    }else{
        $e = $var;
    }
    return !empty($e) ? htmlentities($e) : $e;
}

function _isChecked($var, $obj = []){
    $val = _g($var, $obj);
    if( $val == 1){
        return 'checked="checked"';
    }
    return '';
}

function _selected($v1, $v2){
    if(is_array($v2)){
        if(in_array($v1, $v2)){
            echo  'selected';
        }
    }
    if(!is_array($v2) && $v1 == $v2){
        echo 'selected';
    }
    echo '';
}

function postReq(){
    echo $_SERVER['REQUEST_URI'];
}

function siteurl(): string
{
    return APP_URI;
}

function buildUrl($params = []){
    echo parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)  . '?' . http_build_query($params);
}



function navActive(){
    echo 'active';
}

function includePartial($path = '', $drives = []){

    includeAdminTemplatePart('/partials'.$path);
}
function cleanDS($uri) : string{
    $ds = DIRECTORY_SEPARATOR;
    return str_replace(['/','\\'], $ds, $uri);
}

function fileExists($fileName, $caseSensitive = false) {

    if(file_exists($fileName)) {
        return $fileName;
    }
    if($caseSensitive) return false;

    // Handle case insensitive requests
    $directoryName = dirname($fileName);
    $fileArray = glob($directoryName . '/*', GLOB_NOSORT);
    $fileNameLowerCase = strtolower($fileName);
    foreach($fileArray as $file) {
        if(strtolower($file) == $fileNameLowerCase) {
            return $file;
        }
    }
    return false;
}




