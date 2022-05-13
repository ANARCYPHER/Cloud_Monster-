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

use CloudMonster\App;
use CloudMonster\Models\Buckets;
use CloudMonster\Models\CloudDrives;
use CloudMonster\Models\CloudFolder;
use CloudMonster\Models\Files;
use CloudMonster\Models\LocalFolders;
use CloudMonster\Models\ProcessTracker;
use CloudMonster\Helpers\Help;
use CloudMonster\Helpers\Logger;
use Phpfastcache\Exceptions\PhpfastcacheSimpleCacheException;

/**
 * Class CloudUpload
 * @author John Antonio
 * @package CloudMonster\Services
 */
class CloudUpload {

    /**
     * Current Drive App
     * @var
     */
    protected $driveApp;

    /**
     * Files model
     * @var Files
     */
    protected Files $file;

    /**
     * Bucket Model
     * @var Buckets
     */
    protected Buckets $bucket;

    /**
     * Cloud Drive model
     * @var CloudDrives
     */
    protected CloudDrives $drive;

    /**
     * Cloud Folder Model
     * @var CloudFolder
     */
    protected CloudFolder $folder;

    /**
     * Status
     * @var bool
     */
    protected bool $isReady = false;

    /**
     * Sleeping time for each upload process
     * @var int
     */
    protected int $sleepTime = 3;

    /**
     * Current File ID
     * @var int
     */
    protected int $fileId = 0;

    /**
     * Current process ID
     * @var string
     */
    protected string $processId = '';

    /**
     * Process tracker
     * @var ProcessTracker
     */
    protected ProcessTracker $processTracker;


    /**
     * CloudUpload constructor.
     * @param int $fileId
     */
    public function __construct(int $fileId) {

        $this->fileId = $fileId;
        $this->file = new Files;
        $this->drive = new CloudDrives();
        $this->bucket = new Buckets();
        $this->folder = new CloudFolder();
        $this->setProcessTracker();
        Logger::info('MonsterCloudUpload: Master loop started PROCESS_ID::[' . $this->processId . ']');

    }

    /**
     * Set process tracker
     */
    protected function setProcessTracker() {

        //init process tracker
        $this->processTracker = new ProcessTracker();
        //attempt to call to process tracker
        try {

            $this->processTracker->call();
            $this->processId = '#' . $this->processTracker->getCallerId();

        } catch(\Exception $e) {
            Logger::debug('Unable to call to process tracker');
        }

    }

    /**
     * Init current file
     * @return bool
     */
    protected function initFile(): bool {

        $success = false;
        //attempt to load current file
        if ($this->file->load($this->fileId)) {
            //attempt to load current bucket
            if ($this->bucket->load($this->file->getBucketId())) {
                if (!$this->file->IsUsed() && $this->file->isWaiting()) {

                    $this->file->used();
                    $success = true;

                }
            }
        }

        return $success;

    }

    /**
     * Attempt to setup drive app for upload
     * @return $this
     */
    public function setup(): static {

        //attempt to init file
        if ($this->initFile()) {

            //load  drive
            if ($this->drive->load($this->file->getCloudDriveId())) {

                //initialize drive app
                $driveClass = ucwords($this->drive->getType());
                $class = "\\CloudMonster\\Drives\\{$driveClass}\\App";

                if (class_exists($class)) {

                    $this->driveApp = new $class($this->drive);
                    $this->ready();

                } else {

                    Logger::debug('MonsterCloudUpload: DriveApp class does not exist. Class::' . $class);

                }

            } else {

                Logger::debug('MonsterCloudUpload: Unable to load cloud drive');

            }

        } else {

            Logger::debug('MonsterCloudUpload: File initialization failed');

        }

        return $this;

    }

    /**
     * Before run
     */
    protected function beforeRun() {

        //attempt to connect
        $this->driveApp->connect();

        //init upload
        $this->driveApp->initUpload($this->bucket->getUniqId(), $this->fileId);
        $this->driveApp->upload->setFilename($this->bucket->getFullName());

        //set upload location
        $this->driveApp->upload->setLocation($this->bucket->getLocation());

        //load cloud parent folder
        if ($this->bucket->getFolderId() !== LocalFolders::ROOT_FOLDER) {
            $loadData = [
                'localFolderId' => $this->bucket->getFolderId(),
                'cloudDriveId' => $this->drive->getID()
            ];
            $parentFolder = $this->folder->getOne($loadData);
            if (!empty($parentFolder)) {
                $this->driveApp->upload->setParentFolder($parentFolder);
            }
        }

        //send file ID to process tracker
        $this->processTracker->add($this->fileId);

    }


    /**
     * @throws PhpfastcacheSimpleCacheException
     */
    protected function afterRun() {
        //close current process tracker
        $this->processTracker->end();



    }


    /**
     * @return bool
     */
    public function run(): bool {

        $error = 'Unknown error';
        $success = false;
        $uploadedFileId = '';

        //check status
        if ($this->isReady) {

            //update file status
            $this->file->processing();

            try {

                //attempt to run cloud drive uploader
                $this->beforeRun();
                $this->driveApp->upload->run();
                $this->afterRun();

                //attempt to start upload process
                if ($uploadedFileId = $this->driveApp->upload->getFileId())
                    $success = true;

            }
            catch(\Throwable $e) {
                $error = $e->getMessage();
            }

        }

        //save results
        if ($this->file->isLoaded()) {
            if ($success) {
                //success

                //file code
                $data = ['code' => $uploadedFileId];

                //shared web url
                if(!empty($this->driveApp->upload->getSharedLink())){
                    $data['sharedLink'] = $this->driveApp->upload->getSharedLink();
                }

                $this->file->assign($data)->save();
                $this->file->active();
            } else {
                //failed
                $this->file->assign(['msg' => $error])->save();
                $this->file->inactive();
            }
            //check upload is completed in current bucket
            if($this->bucket->isUploadQueryEmpty()){
                Help::deleteDir($this->bucket->getTmpDir());
            }
        }

        Logger::error('RUN PROCESS USAGE:: ' . Help::formatSizeUnits(memory_get_peak_usage(true)));

        //attempt to load next file
        return $this->loadNext();

    }

    /**
     * Load next file for upload
     * @return bool
     */
    protected function loadNext(): bool {

        sleep($this->sleepTime);

        if ($file = $this->getNext()) {

            $this->file->clean();
            $this->drive->clean();
            $this->bucket->clean();

            unset($this->driveApp);

            $this->fileId = $file['id'];

            Logger::info('MonsterCloudUpload: [' . $this->processId . '] Next file loaded for upload');

            return $this->setup()->run();

        } else {

            Logger::info('MonsterCloudUpload: [' . $this->processId . '] Upload query is empty.');

        }

        return false;

    }

    /**
     * Get next file
     * @return array|mixed
     */
    public function getNext() {
        return $this->file->getOne(['pstatus' => Files::WAITING, 'isUsed' => 0], [], ['id' => 'ASC']);
    }

    /**
     * Check process limit exceeded or not
     * @return bool
     */
    public static function isProcessExceeded(): bool {

        $tmpFile = new Files;
        $processingFiles = $tmpFile->countProcessingFiles();
        $maxProcessLimit = App::getConfig('max_upload_process');
        unset($tmpFile);

        return $processingFiles >= $maxProcessLimit;

    }

    public static function isActive(): bool
    {
        $tmpFile = new Files;
        if(!empty($tmpFile->countProcessingFiles())){
            return true;
        }
        return false;
    }

    public static function getUploadChunkSize(): int
    {
        $defaultSize = 1048576;
        $chunkSize = App::getConfig('upload_chunk_size');
        if(!empty($chunkSize) && is_numeric($chunkSize)){
            $chunkSize = Help::convertToBytes($chunkSize . 'MB');
            if($chunkSize > $defaultSize){
                return $chunkSize;
            }
        }
        return $defaultSize;
    }

    public function await() {

    }


    /**
     * status
     */
    protected function ready() {
        $this->isReady = true;
    }

    /**
     * destructor
     */
    public function __destruct() {

        Logger::info('MonsterCloudUpload: [' . $this->processId . '] Master loop stopped.');

        if (isset($this->processTracker)) {

            $this->processTracker->sayBye();

        }

    }



}
