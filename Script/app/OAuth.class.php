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
use CloudMonster\Core\Session;
use CloudMonster\Helpers\Validation;
use CloudMonster\Models\CloudDrives;
use CloudMonster\Models\Files;
use CloudMonster\Models\Login;
use CloudMonster\Models\Visitors;
use CloudMonster\Helpers\Help;


/**
 * Class OAuth
 * @package CloudMonster
 */
class OAuth extends App
{



    public function __construct($app)
    {

        $this->action = $app->action;
        $this->args = $app->args;


    }



    protected function drive($source = ''){

        $cloudDrive = new CloudDrives();

        $success = false;

        if($source == 'onedrive'){

            //verify one drive auth
            $code = Request::get('code');
            $error = Request::get('error_description');

            if(Session::isset('driveId')){
                $driveId = Session::get('driveId');
                Session::delete('driveId');
                $cloudDrive->load($driveId);
            }

            if(!empty($code)){
                //check current one drive session is already exist
                //verify drive
                if($cloudDrive->isLoaded()){
                    //attempt to load current cloud app
                    if($cloudDrive->loadCloudApp()){

                        if($cloudDrive->cloudApp->verifyLogin($code)){

                            $cloudDrive->active();
                            $success = true;

                        }else{

                            $cloudDrive->error();

                        }

                    }

                }
            }else{
                $this->addAlert($error);
                $cloudDrive->error();
            }

            if(isset($driveId)){
                if($success){
                    $this->addAlert('OneDrive API authenticated successfully', 'success');
                }else{
                    $this->addAlert('OneDrive API authentication failed');
                }
                Help::redirect('cpanel/drives/edit/' . $driveId);
            }





        }


        dnd('something went wrong');


    }

    public function __destruct()
    {
        parent::__destruct();
    }


}
