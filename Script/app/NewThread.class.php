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


use CloudMonster\Models\Buckets;
use CloudMonster\Services\CloudFile;
use CloudMonster\Services\CloudUpload;
use CloudMonster\Services\ProcessManager;
use CloudMonster\Services\RemoteUpload;
use CloudMonster\Services\ReUpload;
use CloudMonster\Services\Thread;


class NewThread extends App
{


    /**
     * NewThread constructor.
     * @param $app
     */
    public function __construct($app)
    {

        $this->action = $app->action;
        $this->args = $app->args;

        //only supported POST request
        if(!Request::isPost()){
            die('Invalid request');
        }

        session_write_close();
        ignore_user_abort(true);
        set_time_limit(0);

        //attempt to start thread session
        if(!Session\Thread::isInit()){
            Session\Thread::start(true);
        }

        //attempt to check thread request
        $this->check();

    }

    /**
     * Check thread request
     */
    protected function check(){
        $threadId = Request::post('id');

        $authToken = Request::getAuth('X-Token');
        $isValidRequest = false;
        if(!empty($authToken)){
            if($authToken == THREAD_SECRET_TOKEN){
                $isValidRequest = true;
            }
        }

        if(!$isValidRequest) die('invalid request');

        if(!empty($threadId))
            Thread::requestReceived($threadId);
    }


    /**
     * Handle cloud files
     */
    protected function handleCloudFile(){

        $folderId = Request::post('folder');
        $type = Request::post('type');
        $action = Request::post('action');
        $ids = Request::post('ids');

        if(!empty($action) && !empty($folderId)){
            sleep(1);
            $isFile = ($type == 'bucket');
            $cloudFolder = new CloudFile($folderId, $isFile);
            if(!empty($ids) && is_array($ids)) $cloudFolder->setCloudFiles($ids);
            $cloudFolder->operate($action);

        }

    }

    /**
     * Start auto re-upload
     */
    protected function autoReUpload(){

        $bucketId = Request::post('bucket_id');

        if(!empty($bucketId)){

            $bucket = new Buckets;
            if($bucket->load($bucketId)){

                $reUpload = new ReUpload($bucket);
                $reUpload->run();

            }

        }

    }

    /**
     * Start upload
     */
    protected function upload(){

        $fileId = Request::post('file_id');

        $cloudUpload = new CloudUpload($fileId);
        $cloudUpload->setup()->run();

    }

    protected function remoteUpload(){
        $bucketId = Request::post('bucket_id');
        if(!empty($bucketId) && is_numeric($bucketId)){
            $bucket = new Buckets();
            if($bucket->load($bucketId)){
                $remoteUpload = new RemoteUpload($bucket);
                $remoteUpload->run();
            }
        }

    }

    /**
     * Start upload process
     */
    protected function runProcess(){

        $processManager = new ProcessManager();
        $processManager->run();


    }


    public function __destruct()
    {
        Session\Thread::destroy();
        parent::__destruct();
    }


}
