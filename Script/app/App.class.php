<?php

/*
   +------------------------------------------------------------------------+
   | CloudMonster - Handle multi-Cloud Storage in parallel
   | Copyright (c) 2021 PHPCloudMonster. All rights reserved.
   +------------------------------------------------------------------------+
   | @author John Antonio
   | @author_url 1: https://phpcloudmonster.com
   | @author_url 2: https://www.codester.com/johnanta
   | @author_email: johnanta89@gmail.com
   +------------------------------------------------------------------------+
*/

namespace CloudMonster;


use CloudMonster\Core\Database;
use CloudMonster\Core\Request;
use CloudMonster\Core\View;
use CloudMonster\Helpers\Help;

/**
 * Class App
 * @author John Antonio
 * @package CloudMonster
 */
class App{

    /**
     * Application configuration
     * @since 1.0
     */
    protected static array $config;

    /**
     * Application data
     * @since 1.0
     */
    protected static array $data = [];

    /**
     * Application alerts
     * @since 1.0
     */
    protected static array $alerts = [];


    /**
     * Viewer Object
     * @since 1.0
     */
    protected View $view;


    protected string $action = '';
    protected string $actionNamespace = "\\CloudMonster\\";

    protected array $args = [];

    protected bool $autoInit = true;


    /**
     * Constructor: Checks logged user status
     * @since 1.0
     */
    public function __construct($config = []){

        if(!is_array($config)){
            return false;
        }

        if(!isset($this::$config) && !empty($config)){
            $this::$config = $config;
        }
        if(!isset($this->view)){
            $this->view = new View(TEMPLATE_DIR.'/pages/cpanel');
        }

        if (isset($_SESSION["alerts"])) {
            $this::$alerts = $_SESSION["alerts"];
            unset($_SESSION["alerts"]);
        }

    }


    /**
     * ROUTE APP
     */
    protected function route(){

        if(!empty($this->action)){

            $this->action = Help::removeSpecialChars($this->action);
            $actionClass = $this->actionNamespace . "{$this->action}";

            if(class_exists($actionClass)){

                //get next action
                $nextAction =  array_shift($this->args) ;

                $targetAction = new $actionClass($this);
                $targetAction->setAction($nextAction);


                if(empty($nextAction) || !$this->autoInit) $nextAction = 'init';
                $nextAction = Help::removeSpecialChars($nextAction);

                if(method_exists($actionClass, $nextAction)){

                     return call_user_func_array(
                         [$targetAction, $nextAction],
                         $this->args
                     );

                }else{

                    if(Help::isDev()){
                        die("{$nextAction} :: method is does not exists in Class::{$actionClass} !");
                    }else{
                        Help::e404();
                    }

                }

            }else{

                if(Help::isDev()){
                    die("Class::{$actionClass}  does not exists in app !");
                }else{
                    Help::e404();
                }

            }

        }

        if(Help::isDev()){
            die("route failed !");
        }else{
            Help::e404();
        }

    }

    /**
     * Run Application
     * @since 1.0
     */
    public function run(){

        $reqUrl = Request::get('a');

        if (!empty($reqUrl)) {

            $var = explode("/", $reqUrl);
            $var[0] = str_replace(".", "", $var[0]);
            $var[0] = str_replace("-", "_", $var[0]);
            $var[0] =
                isset($var[0][0]) && is_numeric($var[0][0])
                    ? "_" . $var[0]
                    : $var[0];
            $this->action = $var[0];
            unset($var[0]);

            //load custom slugs
            $this->loadCustomSlug();

            $this->args = $var;

            return $this->route();

        }else{
            //redirect to login page
            Help::redirect('cplogin');
        }

    }

    /**
     * Fix custom slugs
     */
    protected function fixCustomSlugs() : void{

        $customSlugs = APP::getConfig('custom_slugs');

        if(!empty($customSlugs) && is_array($customSlugs)){
            if(array_key_exists($this->action, $customSlugs)){
                $this->action = $customSlugs[$this->action];
            }
        }

    }


    protected function loadCustomSlug(){
        if(!empty($this->action)){

            $customSlugs = $this->getCustomSlugs();

            if(!empty($customSlugs) && is_array($customSlugs)){

                foreach ($customSlugs as $k => $v){
                    if($this->action == $v){
                        $this->action = $k;
                        break;
                    }
                }

            }

        }
    }


    /**
     * Save application data
     * @since 1.0
     */
    protected function addData($data, $name = "")
    {
        if (!empty($name)) {
            $this::$data[$name] = $data;
        } else {
            $this::$data = $data;
        }
    }

    /**
     * Get saved app data
     * @since 1.0
     */
    public static function getData()
    {
        return self::$data;
    }

    /**
     * Save alerts in session
     * @since 1.0
     */
    public static function saveAlerts()
    {
        $_SESSION["alerts"] = self::$alerts;
    }

    /**
     * Add alert
     * @since 1.0
     */
    protected function addAlert($msg, $type = "danger")
    {
        if (!array_key_exists($type, $this::$alerts)) {
            $this::$alerts[$type] = [];
        }
        $this::$alerts[$type][] = $msg;
    }

    protected function addAlerts(array $alerts)
    {
        foreach ($alerts as $alert) {
            $this->addAlert($alert);
        }
    }
    /**
     * get app alertd
     * @since 1.0
     */
    public static function getAlerts(): array
    {
        return self::$alerts;
    }

    /**
     * Check alerts
     * @since 1.0
     */
    protected function hasAlerts($t = "danger", $all = false)
    {
        if (
            (isset($this::$alerts[$t]) && !empty($this::$alerts[$t])) ||
            ($all && !empty($this::$alerts))
        ) {
            return true;
        }
        return false;
    }

    /**
     * Ajax response
     * @since 1.0
     */
    protected function ajaxResponse(
        array $data = [],
        bool $status = false,
        bool $skip = false
    ) {
        $response = ["success" => $status];

        if(empty($data))
            $data = $this::$data;

        if (($status || $skip) && !empty($data)) {
            $response["data"] = $data;
        }

        $response["alerts"] = $this->getAlerts();
        $this->jsonResponse($response);
    }

    /**
     * Echo json data
     */
    protected function jsonResponse($resp)
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code(200);
        echo json_encode($resp);
        exit();
    }

    /**
     * Get app config
     */
    public static function getConfig($config)
    {
        if (self::hasConfig($config)) {
            $conVar = self::$config[$config];
            if(Help::isJson($conVar))
                $conVar = Help::toArray($conVar);
            return $conVar;
        }
        die("Required configuration does not exist ! -> " . $config);
    }

    public static function hasConfig($config): bool
    {
        return array_key_exists($config, self::$config);
    }

    protected function updateConfig($data = [])
    {
        $db = Database::getInstance();
        foreach ($data as $config => $val) {
            $this::$config[$config] = $val;
            $db->where("config", $config);
            $db->update("settings", ["var" => $val], 1);
        }
        unset($db);
    }




    protected function getCustomSlugs(){
        $slugs = $this::getConfig('custom_slugs');
        if(is_array($slugs)){
            return $slugs;
        }
        return [];
    }

    public static function getCustomSlug($slug = ''){
        $customSlugs = self::getConfig('custom_slugs');
        if(!empty($customSlugs) && is_array($customSlugs)){
            if(array_key_exists($slug, $customSlugs)){
                if(!empty($customSlugs[$slug])){
                    return $customSlugs[$slug];
                }
            }
        }
        return $slug;
    }

    protected function updateCustomSlug($targetAction,  $newSlug){
        $customSlugs = $this->getCustomSlugs();
        $customSlugs[$targetAction] = $newSlug;
        $this->updateConfig(['custom_slugs'=> Help::toJson($customSlugs)]);
    }

    protected function setAction($action = ''){
        if($action === null)  $action = '';
        $this->action = $action;
    }




    protected function init(){
        die('App Init Failed');
    }

    /**
     * @throws \Exception
     */
    public function __destruct(){
        $db = Database::getInstance();
        if($db->ping()){
            $db->disconnect();
        }
    }

}