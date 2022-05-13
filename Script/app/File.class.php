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

use CloudMonster\Models\Files;
use CloudMonster\Models\Visitors;
use CloudMonster\Helpers\Help;
use CloudMonster\Services\Analytics;


/**
 * Class File
 * @package CloudMonster
 */
class File extends App
{



    public function __construct($app)
    {

        $this->action = $app->action;
        $this->args = $app->args;
        $app->autoInit = false;

        //start visitor session
        Session\Visits::start(true);
    }

    protected function init(){

        $success = false;
        $error = 'not found';
        $link = '';

        $id = $this->action;

        if(!empty($id)){

            $file = new Files;

            //attempt to load file by slug
            if($file->load(['slug'=>$id])){

                //check need to check file availability
                if($file->needToCheck()){
                    dnd('file checked', true);
                    $file->check();
                }

                if($file->isActive()){

                    //attempt to get original file link
                    $link = $file->getOriginalFileLink();

                    if(!empty($link)){
                        $success = true;
                    }

                }

                //check analytics system is enabled or not
                if(Analytics::isSystemEnabled()){
                    //add visit
                    $visitor = new Visitors();
                    $visitor->addVisit($file);
                }


            }

        }


        if($success){
            if(Help::isDev()){
                die($link);
            }
            header("HTTP/1.1 301 Moved Permanently");
            Help::redirect($link, true);
        }else{
            header("HTTP/1.1 404 Not Found");
            die("<code>$error</code>");
        }

    }



    public function __destruct()
    {
        parent::__destruct();
    }


}
