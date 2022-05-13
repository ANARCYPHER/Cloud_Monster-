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

namespace CloudMonster\Core;

use JetBrains\PhpStorm\Pure;
use CloudMonster\App;

/**
 * Class View
 * @author John Antonio
 * @package CloudMonster\Core
 */
class View{

    /**
     * View blank page
     * @var bool
     */
    protected bool $isBlank = false;

    /**
     * Model Data
     * @var array
     */
    protected array $data = [];

    /**
     * View directory
     * @var string
     */
    protected string $dir = '';

    /**
     * Model alerts
     * @var array
     */
    protected array $alerts = [];

    /**
     * Javascript file
     * @var string
     */
    protected string $jsHtml = '';

    /**
     * Page title
     * @var string
     */
    protected string $pageTitle = '';

    /**
     * page description
     * @var string
     */
    protected string $pageDescription = '';

    /**
     * Javascript Data
     * @var array
     */
    protected array $jsData = [];

    /**
     * Route action
     * @var string
     */
    protected string $action = '';


    /**
     * View constructor.
     * @param $dir
     */
    public function __construct($dir){
        $this->setBaseDir($dir);
    }

    /**
     * Set base view directory
     * @param $dir
     */
    public function setBaseDir($dir){
        $this->dir = ROOT . '/' . $dir;
    }

    /**
     * Set page title
     * @param $title
     */
    public function setTitle($title){
        $this->pageTitle = ucwords($title);
    }

    /**
     * Set route action
     * @param $action
     */
    public function setAction($action){
        $this->action = $action;
    }


    /**
     * Render App Template
     * @since 1.0
     */
    public function render($template, $subDir = '',  $isBlank = false){

        $this->beforeRender();
        if (is_array($this->data)) {
            foreach ($this->data as $k => $v) {
                $$k = $v;
            }
        } else {
            $data = $this->data;
        }

        

        if(!empty($subDir)){
            $this->dir = $this->dir . '/' . $subDir;
        }


        if (!file_exists($this->dir . '/' . $template . '.php')) {
            //template file not found
            die('File ' . $this->dir . '/' . $template . '.php not found !');
        }
        if (!$isBlank) {
            $this->header();
            include_once ($this->dir . '/' . $template . '.php');
            $this->footer();
        } else {
            include ($this->dir . '/' . $template . '.php');
        }
    }

    /**
     * Do something before render
     */
    private function beforeRender(){
        $this->loadData();
        $this->alerts = App::getAlerts();
    }

    /**
     * Load App Data
     * @since 1.0
     */
    protected function loadData(){
        $this->data = App::getData();
    }

    /**
     * Get template part
     * @since 1.0
     */
    protected function t($template) {
        if (!file_exists($this->dir . '/' . $template . '.php')) die('File ' . $template . ' does not exist !');
        return $this->dir . '/' . $template . '.php';
    }

    /**
     * Template header
     * @since 1.0
     */
    protected function header() {
        include_once $this->t(__FUNCTION__);
    }

    /**
     * Template footer
     * @since 1.0
     */
    protected function footer() {
        $themejs = $this->jsHtml;
        $jsData = $this->getJsData();
        include_once ($this->t(__FUNCTION__));
    }

    /**
     * Get Javascript data
     * @return string
     */
    protected function getJsData(): string
    {
        $script = '';
        if (!empty($this->jsData)){
            foreach ($this->jsData as $k => $v){
                $script .= 'var ' . $k . '=' . ''.$v.';';
           }
        }
        return $script;
    }

    /**
     * Display alerts
     */
    protected function displayAlerts() {
        if (!empty($this->alerts)) {
            $alertHtml = '';
            
            foreach ($this->alerts as $k => $v) {
//                <div class="alert alert-danger" role="alert">
//                    A simple danger alert—check it out!
//    </div>
                $alertHtml.= '<div class="alert  alert-' . $k . ' alert-dismissible " role="alert">' ;

                if (count($v) == 1) {
                    $alertHtml.= '' . $v[0] . '.';
                } else {
                    $list = '<ul class="m-0">';
                    foreach ($v as $al) {
                        $list.= '<li>' . $al . '.</li>';
                    }
                    $list.= '</ul>';
                    $alertHtml.= $list;
                }
                $alertHtml.= '<button type="button" class="close" data-dismiss="alert" aria-label="Close">  <i class="ti-close"></i></button>';
                $alertHtml.= '</div>';
            }
            echo $alertHtml;
        }
    }

    /**
     * Add Javascript data
     * @param $data
     * @param $var
     */
    public function addJsData($data, $var){
        if(is_array($data)){
            foreach ($data as $k => $v){
                if(!is_array($v)){
                    $this->jsData[$var.ucwords($k)] = $v;

                }
            }
        }else{
            $this->jsData[$var] = $data;
        }


    }

    /**
     * Add Javascript file
     * @param string $files
     * @param false $isFull
     * @param string $alt
     */
    public function addJs($files = '', $isFull = false, $alt = ''){


        $html = '';
        if(!is_array($files)){
            $tmp = $files;
            $files = [];
            $files[] = $tmp;
        }
        foreach($files as $file){
            if(!$isFull){
                $html .= '<script src="'.getResourceURI("assets/cpanel/js/".$file.".js?t=" . time()).'"></script>';
            }else{
                $html .= '<script src="'.$file.'" '.$alt.'t=></script>';
            }
        }

        $this->jsHtml .= $html;

    }


    public function getAction(): string
    {
        return $this->action;
    }

    public function isNavActive($nav = ''){
        return $this->getAction() == $nav;
    }


}