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

use CloudMonster\Core\Request;
use CloudMonster\Helpers\Validation;
use CloudMonster\Models\Files;
use CloudMonster\Models\Login;
use CloudMonster\Models\Visitors;
use CloudMonster\Helpers\Help;


/**
 * Class File
 * @package CloudMonster
 */
class CPLogin extends App
{



    public function __construct($app)
    {

        $this->action = $app->action;
        $this->args = $app->args;
        $app->autoInit = false;

        parent::__construct();
    }

    protected function init(){

       $this->view->setTitle('Login');

       //check user has been already logged
       if(Login::isLogged()){
           Help::gotoCpanel();
       }

        //check remembered cookies
        //attempt to login with remembered cookies
        if(Login::isRemembered()){
            Help::gotoCpanel();
        }


        if(Request::isPost()){

            $username = Request::post('username');
            $password = Request::post('password');
            $rememberMe = Request::post('remember_me') == 'on' ? 1 : 0;

            //attempt to validate input data
            $validation = new Validation();

            $validation
                ->name('Username')
                ->value($username)
                ->pattern('text')
                ->required();

            $validation
                ->name('Password')
                ->value($password)
                ->pattern('text')
                ->required();

            if($validation->isSuccess()){
                //attempt to login
                $login = new Login($username, $password);
                if($login->check()->isValid()){
                    //login success
                    $login->initSession();

                    //prepare remember session
                    if($rememberMe){
                        //update remember token
                        $remToken = Help::random(12);
                        $this->updateConfig([
                            'login_remember_token' => $remToken
                        ]);
                        $login->remember($remToken);
                    }

                    Help::gotoCpanel();
                }else{
                    //login failed
                    $this->addAlert('Invalid username or password');
                }
            }else{
                $this->addAlerts($validation->getErrors());
            }

            Help::redirect('self');

        }

        $this->view->render('login', '',true);

    }

    public function __destruct()
    {
        parent::__destruct();
    }


}
