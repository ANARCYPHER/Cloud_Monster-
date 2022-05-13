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
use CloudMonster\Models\Buckets;
use CloudMonster\Models\Visitors;

/**
 * Class Bucket
 * @package CloudMonster
 */
class Bucket extends App
{


    /**
     * Bucket constructor.
     * @param $app
     */
    public function __construct($app)
    {

        $this->action = $app->action;
        $this->args = $app->args;

        $app->autoInit = false;

    }

    /**
     * Default action
     * @param string $type
     */
    public function init(string $type = '')
    {

        $bucket = new Buckets;
        $error = 'not found';
        $success = false;
        $file = null;

        $id = $this->action;


        if(!empty($id)){

            //attempt to load bucket
            if($bucket->load($id)){

                //check is shared or not
                if($bucket->getShared()){

                    //attempt to find some file in the current bucket
                    $file = $bucket->findFileByRand($type);

                    //check file has been loaded or not
                    if($file->isLoaded()){


                        $link = siteurl() . '/file/' . $file->getSlug();
                        $success = true;

                    }else{

                        $error = 'bucket is empty';

                    }

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
