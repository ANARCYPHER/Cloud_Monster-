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

namespace CloudMonster\Services;


use CloudMonster\Helpers\Help;
use CloudMonster\Models\Buckets;
use CloudMonster\Models\CloudDrives;
use CloudMonster\Models\Files;

/**
 * Class ReUpload
 * @author John Antonio
 * @package CloudMonster\Services
 */
class ReUpload{

    /**
     * current cloud file
     * @var CloudFile
     */
    protected CloudFile $cloudFile;

    /**
     * Current bucket for re-upload
     * @var Buckets
     */
    protected Buckets $bucket;

    /**
     * Supported drive types for auto re-upload
     * @var array
     */
    protected array $supportedDrives = [
        'google', 'onedrive', 'dropbox'
    ];

    /**
     * Supported files for auto re-upload
     * @var array files id list
     */
    protected array $supportedFiles = [];

    /**
     * Re-upload error
     * @var string
     */
    protected string $error = '';


    /**
     * ReUpload constructor.
     * @param Buckets $bucket Current bucket for re-upload
     */
    public function __construct(Buckets $bucket){

        $this->bucket = $bucket;
        $this->cloudFile = new CloudFile($this->bucket->getID(), true);
        $this->check();

    }

    /**
     * Start auto re-upload process.
     * (we will create it with new thread)
     * @return bool
     */
    public function process(): bool
    {

        $thread = new Thread('auto-re-upload', [
            'bucket_id' => $this->bucket->getID()
        ]);

        $thread->withId()->create();
        $thread->await();

        return $thread->isOk();

    }

    /**
     * Attempt to run auto re-upload
     * @return bool
     */
    public function run(): bool
    {

        $success = false;

        //check re-upload session already exist or already tmp file exist
        // if yes, we do not need continue
        if(
            $this->bucket->isReUploadSessionActive() ||
            $this->bucket->isTmpFileExist()
        ){
            $this->bucket->uploadReady();
            return false;
        }

        if(!empty($this->supportedFiles)){

            //enable auto re-upload sess
            $this->bucket->updateReUploadSession(Buckets::ACTIVE_RE_UPLOAD_SESSION);


            //load each cloud file and attempt to download file
            foreach ($this->supportedFiles as $k => $file){

                //current cloud file id
                $fileId = $file['id'];

                //set specific cloud file id/s
                $this->cloudFile->setCloudFiles([$fileId]);
                //attempt to start download operate
                $this->cloudFile->operate('download');

                //verify file is downloaded or not
                if($this->bucket->isTmpFileExist()){
                    //update upload process manager
                    if($this->bucket->uploadReady()){
                        ProcessManager::update();
                    }
                    $success = true;
                    break;
                }

            }



            if($success){
                //disable auto re-upload sess
                $this->bucket->updateReUploadSession(Buckets::INACTIVE_RE_UPLOAD_SESSION);
            }else{
                //failed auto re-upload sess
                $this->bucket->cancelUpload('Auto re-upload failed');
                $this->bucket->updateReUploadSession(Buckets::FAILED_RE_UPLOAD_SESSION);
            }

        }

        return $success;

    }


    /**
     * Check is it ready for auto re-upload
     * @return bool ready or not
     */
    protected function check(): bool
    {

        if($this->bucket->isLoaded()){


            $tmpCloudDrive = new CloudDrives();
            $driveList = $tmpCloudDrive->get([
                'type' => [
                    $this->supportedDrives,
                    'IN'
                ]
            ],[],['id']);



            if(!empty($driveList)){

                $driveList = Help::extractData($driveList, 'id');

                $tmpFiles = new Files();
                $results = $tmpFiles->get([
                    'bucketId' => $this->bucket->getID(),
                    'cloudDriveId' => [
                        $driveList,
                        'IN'
                    ],
                    'pstatus' => Files::ACTIVE
                ],[],[]);

                if(!empty($results)){

                    $this->supportedFiles = $results;
                    return true;

                }else{

                    $this->addError('Supported cloud files not found');

                }

            }else{

                $this->addError('Supported drives not found');

            }


        }

        return false;

    }

    protected function addError($e){

        $this->error = $e;

    }

    public function getError(): string
    {
        return $this->error;
    }

    public function isOk(): bool
    {
        return empty($this->error);
    }





}