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
use CloudMonster\models\Buckets;
use CloudMonster\models\CloudDrives;
use CloudMonster\models\Files;
use CloudMonster\models\LocalFolders;
use CloudMonster\models\CloudFolder as Folder;



/**
 * Class CloudFile
 * @author John Antonio
 * @package CloudMonster\Services
 */
class CloudFile {

    /**
     * Drive App
     * @var
     */
    protected $driveApp;

    /**
     * Cloud Folder
     * @var Folder
     */
    protected Folder $folder;

    /**
     * Local Folder
     * @var LocalFolders
     */
    protected LocalFolders $localFolder;

    /**
     * Cloud Drive Model
     * @var CloudDrives
     */
    protected CloudDrives $drive;

    /**
     * Bucket Model
     * @var Buckets
     */
    protected Buckets $bucket;

    /**
     * Cloud Files Model
     * @var Files
     */
    protected Files $file;

    /**
     * Active drive list
     * @var array
     */
    protected array $driveList = [];

    /**
     * Temporary data
     * @var array
     */
    protected array $tmp = [];

    /**
     * Target cloud file list
     * @var array
     */
    protected array $cloudFiles = [];

    /**
     * Current folder ID
     * @var int
     */
    protected int $folderId = 0;

    /**
     * Loop sleep time
     * @var int
     */
    protected int $sleepTime = 0;

    /**
     * Valid actions list
     * @var array|string[]
     */
    protected array $actions = [];

    /**
     * Current action
     * @var string
     */
    protected string $action = '';

    /**
     * Current file location
     * @var string
     */
    protected string $location;

    /**
     * Current file type
     * @var string
     */
    protected string $type;

    /**
     * Filename
     * @var string
     */
    protected string $filename;

    /**
     * Is file or folder
     * @var bool
     */
    protected bool $isFile = false;

    /**
     * Callback function
     */
    protected $callback;

    /**
     * status
     * @var bool
     */
    protected bool $isOk = false;


    /**
     * CloudFile constructor.
     * @param int $folderId
     * @param bool $isFile
     */
    public function __construct(int $folderId, bool $isFile = false) {

        $this->folder = new Folder();
        $this->localFolder = new LocalFolders();
        $this->drive = new CloudDrives();

        $this->folderId = $folderId;
        $this->isFile = $isFile;

        $this->actions = [
            'create',
            'rename',
            'move',
            'delete',
            'check',
            'download'
        ];

    }

    /**
     * initialization
     */
    protected function init() {
        //set file type
        $this->type = $this->isFile ? 'file' : 'folder';
        //load bucket
        if ($this->isFile) {
            $this->bucket = new Buckets();
            $this->file = new Files();
            if ($this->bucket->load($this->folderId)) {
                $this->folderId = $this->bucket->getFolderId();
            } else {
                Logger::debug('Bucket not found');
                exit;
            }
        }
        //load local folder
        if ($this->localFolder->load($this->folderId)) {
            if (!$this->isFile) {
                //get current cloud drive list
                if ($this->action !== 'create') {
                    $drives = $this->folder->get(['localFolderId' => $this->folderId], [], ['cloudDriveId as id.skip']);
                } else {
                    $drives = $this->drive->get([], [], ['id']);
                }
            } else {
                //get current cloud drive list
                $loadData = ['bucketId' => $this->bucket->getId(), 'status' => '' ];
                if(!empty($this->cloudFiles)) $loadData['id'] = [
                    $this->cloudFiles,
                    'IN'
                ];
                $drives = $this->file->get($loadData, [], ['cloudDriveId as id.skip']);
            }

            if (!empty($drives)) {
                $this->driveList = $drives;
                $this->loadTmpData();
                $this->isOk = true;
            } else {
                Logger::debug('Drives not found');
            }
        } else {
            Logger::debug('Folder not found');
        }
        //set file name
        $this->filename = $this->isFile ? $this->bucket->getFullName() : $this->localFolder->getName();

    }

    public function setCloudFiles(array $data = []){
        $this->cloudFiles = $data;
    }

    /**
     * Load temporary data
     */
    protected function loadTmpData() {

        $tmpData = !$this->isFile ? $this->localFolder->getTmp() : $this->bucket->getTmp();
        if (!empty($tmpData) && Help::isJson($tmpData)) {
            $tmpData = Help::toArray($tmpData);
            $this->tmp = $tmpData;
        }

    }

    /**
     * Create folder location
     */
    protected function createLocation() {

        $skipCurrentFolder = $this->action === 'create';
        $this->location = $this->localFolder->getLocation($skipCurrentFolder);
        if ($this->isFile) {
            $this->location.= '/' . $this->bucket->getFullName();
            $this->location = ltrim($this->location, '/');
        }

    }

    /**
     * Start file operate
     * @param string $action
     * @return bool
     */
    public function operate(string $action, $callback = null): bool {

        if (in_array($action, $this->actions)) {

            $this->action = $action;
            $this->callback = $callback;

            $this->init();
            $this->run();

        }

        return false;

    }


    /**
     * Setup current drive app
     * @return bool
     */
    protected function setup(): bool {

        $success = false;

        //attempt to load current drive model
        if ($this->drive->isLoaded()) {

            //attempt to set cloud file load data
            $loadData = ['cloudDriveId' => $this->drive->getID() ];
            if (!$this->isFile) {
                $loadData['localFolderId'] = $this->localFolder->getID();
            } else {
                $loadData['bucketId'] = $this->bucket->getID();
            }

            //create drive app class name
            $driveClass = ucwords($this->drive->getType());
            $class = "\\CloudMonster\\Drives\\{$driveClass}\\App";

            //check target class exist or not
            if (class_exists($class)) {

                //init drive app
                $this->driveApp = new $class($this->drive);
                //attempt to connect drive app
                $this->driveApp->connect();

                //attempt to get current cloud file ID
                $fileCode = $this->{$this->type}->load($loadData) ? $this->{$this->type}->getCode() : 0;
                if($fileCode == null) $fileCode = '0';

                //init sub class
                $initClass = 'init' . ucwords($this->type);
                $this->driveApp->{$initClass}($fileCode);
                $this->driveApp->{$this->type}->setLocation($this->location);


                //set parent folder details for cloud folders
                if ($this->action === 'create' || $this->action === 'move') {

                    if (($this->localFolder->getParentId() !== LocalFolders::ROOT_FOLDER) || $this->isFile) {
                        $localFolderId = !$this->isFile ? $this->localFolder->getParentId() : $this->localFolder->getID();
                        $loadData = ['localFolderId' => $localFolderId, 'cloudDriveId' => $this->drive->getID() ];
                        $parentFolder = $this->folder->getOne($loadData);
                        if (!empty($parentFolder)) {
                            $this->driveApp->{$this->type}->setParentFolder($parentFolder);
                        }
                    }

                }

                if ($this->action === 'move' || $this->action === 'rename') {
                    //set temporary data to drive app
                    $this->tmp['code'] = $this->{$this->type}->getCode();
                    $this->driveApp->{$this->type}->setTmp($this->tmp);

                }

                $success = true;

            } else {

                Logger::debug('CloudFolder: DriveApp class does not exist. Class::' . $class);

            }

        }

        return $success;

    }

    /**
     * start process
     */
    protected function run() {

        //check status
        if ($this->isOk) {

            //attempt to create file location
            $this->createLocation();

            //attempt to loop each drive
            foreach ($this->driveList as $drive) {

                //attempt to load current drive
                if ($this->drive->load($drive['id'])) {

                    try {
                        //attempt to setup drive app
                        if ($this->setup()) {

                            //execute current operate
                            $this->{$this->action}();

                        }
                    }
                    catch(\Throwable $e) {

                        Logger::debug('CloudFolder: ' . $e->getMessage());

                    }

                }

                //clean loaded object data
                $this->folder->clean();
                $this->drive->clean();

                if (isset($this->file))
                    $this->file->clean();

            }

        }

    }


    /**
     * Create new folder
     */
    public function create() {

        if ($fileCode = $this->driveApp->{$this->type}->{$this->action}($this->filename)) {

            $data = [
                'localFolderId' => $this->localFolder->getID(),
                'cloudDriveId' => $this->drive->getID(),
                'code' => $fileCode
            ];

            if ($this->folder->assign($data)->save()) {
                //success
            }

        }

    }

    /**
     * Move folder
     */
    public function move() {

        if ($this->driveApp->{$this->type}->{$this->action}()) {
            //success
        }

    }

    /**
     * Rename folder
     */
    public function rename() {

        if ($this->driveApp->{$this->type}->{$this->action}($this->filename)) {
            //success
        }

    }

    /**
     * Delete folder
     */
    public function delete() {

        if ($this->driveApp->{$this->type}->{$this->action}()) {
            //success
        }

    }

    /**
     * Get file
     */
    public function check(){

        $status = $this->driveApp->{$this->type}->{$this->action}();
        if(method_exists($this->{$this->type}, 'checked')){
            $this->{$this->type}->checked($status);
        }

    }


    /**
     * Download File
     */
    public function download() {

        if ($this->driveApp->{$this->type}->{$this->action}($this->bucket)) {
            //success
        }

    }

    protected function isCustomProcess(): bool
    {
        return !empty($this->cloudFiles);
    }

    /**
     * Create file operate process
     * @param string $action
     * @param object $objFile
     * @param array $specificIds
     */
    public static function process(string $action, object $objFile, array $specificIds = [] ) {

        $data = [];

        if ($objFile->isLoaded()) {

            $isFolder = !$objFile->isObjVal('folderId');
            $data['folder'] = $objFile->getId();
            $data['type'] = $isFolder ? 'folder' : 'bucket';
            $data['action'] = $action;

            if(!empty($specificIds))
                $data['ids'] = $specificIds;

            //check operate permission
            if(CloudDrives::hasOperatePermission($action, $data['type'])){
                $thread = new Thread('handle-cloud-file', $data);
                $thread->create();
                unset($thread);
            }

        }

    }

    /**
     * App destruct
     */
    public function __destruct() {

        if ($this->isOk) {

            //attempt to delete local file, after cloud files deleted
            if ($this->action == 'delete') {
                if (!$this->isFile) {
                    if ($this->localFolder->isDeleted()) {
                        $this->localFolder->parent()->delete();
                    }
                } else {
                    if(!$this->isCustomProcess()){
                        if ($this->bucket->isDeleted()) {
                            $this->bucket->delete();
                        }
                    }else{
                        foreach ($this->cloudFiles as $file){
                            $this->file->clean();
                            if($this->file->load($file)){
                                if($this->file->isDeleted()){
                                    $this->file->delete();
                                }else{
                                    $this->file->assign(['code'=>''])->save();
                                }
                            }
                        }
                    }
                }
            }

            if ($this->action == 'move') {
                if (!$this->isFile) {
                    //                    $this->localFolder->assign(['tmp'=>''])->save();

                } else {
                    //                    $this->bucket->assign(['tmp'=>''])->save();

                }
            }

        }

    }
}
