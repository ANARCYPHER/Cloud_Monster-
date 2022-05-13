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


use CloudMonster\Helpers\Help;
use CloudMonster\Models\Login;

class CPanel extends App
{

    protected bool $locked = true;

    public function __construct($app)
    {

        $this->action = $app->action;
        $this->args = $app->args;
        $this->actionNamespace = '\\CloudMonster\\CPanel\\';
        $this->view = $app->view;
        $this->view->setBaseDir(TEMPLATE_DIR.'/pages/cpanel');

        if(!$app instanceof CPanel){
            $app->autoInit = false;
            $this->locked = false;
        }



    }


    public function init()
    {
        if(!$this->locked){

            //login required
            if(!Login::isLogged()){
                $this->addAlert('you must log in to access Cpanel', 'warning');
                Help::redirect('cplogin');
            }

            return $this->route();
        }else{
            die('no route');
        }


    }





    public function __destruct()
    {
        parent::__destruct();
    }


}
