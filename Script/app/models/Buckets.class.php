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


namespace CloudMonster\Models;

use CloudMonster\App;
use CloudMonster\Core\Model;
use CloudMonster\Helpers\Help;
use CloudMonster\Services\CloudFile;

/**
 * Class Buckets
 * @author John Antonio
 * @package CloudMonster\Models
 */
class Buckets extends Model {

    /**
     * Database table
     * @var string table name
     */
    protected static string $tmpTbl = "buckets";

    # Active re-upload session identity number
    const ACTIVE_RE_UPLOAD_SESSION = 1;

    # In-active re-upload session identity number
    const INACTIVE_RE_UPLOAD_SESSION = 0;

    # Failed re-upload session identity number
    const FAILED_RE_UPLOAD_SESSION = 2;


    /**
     * Buckets constructor.
     */
    public function __construct() {
        parent::__construct($this::$tmpTbl);
    }

    /**
     * Create file in the current bucket
     * @param int $cloudId ID of current cloud drive
     * @return bool success or failure
     */
    public function createFile(int $cloudId): bool {

        $tmpFile = new Files();

        //required data for new file
        $data = [
            'bucketId' => $this->getID(),
            'cloudDriveId' => $cloudId,
            'slug' => Help::slug(8)
        ];

        //attempt to create file
        if ($success = $tmpFile->assign($data)->save()) {

            //wait till upload to cloud drives
            $tmpFile->await();

        } else {
            //debug
            $success = false;
        }

        unset($tmpFile);

        return $success;

    }

    /**
     * Update drive files in the current bucket.
     *
     * -- Relevant bucket In, update by inserting separate files for
     * the selected drivers
     *
     * @param array $driveIds ID list of cloud drives.
     * @return bool  success or failure
     */
    public function update(array $driveIds = []): bool {

        $cloudDrives = new CloudDrives();
        $success = false;

        if (!empty($driveIds) && $this->isEdit()) {
            //create file of each drive item
            foreach ($driveIds as $id) {
                //attempt to load current drive item
                if ($cloudDrives->load($id)) {
                    //attempt to create file for the current drive item
                    if ($this->createFile($id)) {
                        $success = true;
                    }
                }
            }
        }

        return $success;

    }

    /**
     * Obtain the location of the relevant bucket.
     * @return string
     */
    public function getLocation(): string {

        $location = '';

        if ($this->isEdit()) {

            $tmpLocalFolder = new LocalFolders();

            if ($tmpLocalFolder->load($this->getFolderId())) {

                # First get the position of the folder where the bucket is located and append bucket name to it
                $location = $tmpLocalFolder->getLocation() . '/' . $this->getFullName();
                $location = ltrim($location, '/');

            }

            unset($tmpLocalFolder);
        }

        return $location;

    }

    /**
     * Move the relevant bucket into another folder.
     *
     * -- This also starts the process of moving files
     * in the cloud drive.
     *
     * @param int $moveTo The ID of the destination folder.
     * @return bool success or failure
     */
    public function move(int $moveTo = 0): bool {

        $success = false;

        if ($this->isEdit()) {

            $tmpFolder = new LocalFolders();
            if ($moveTo !== $this->getFolderId()) {

                if ($tmpFolder->isExist($moveTo)) {

                    $tmpData = ['location' => $this->getLocation() ];
                    $tmpData = Help::toJson($tmpData);

                    if ($this->assign(['folderId' => $moveTo, 'tmp' => $tmpData])->save()) {
                        //also start move bucket in the cloud drives
                        CloudFile::process('move', $this);
                        $success = true;
                    }

                } else {

                    $this->addError('destination folder not found');

                }

            } else {

                $this->addError('bucket is already exists in this folder');

            }

        }

        return $success;

    }

    /**
     * Actions to be taken before deleting the relevant bucket.
     */
    protected function beforeDelete() {

        if (!$this->isSoftDelete()) {

            //Attempt to delete all the files in the relevant bucket
            $cloudFiles = new Files();
            $files = $cloudFiles->getByBucketId($this->getID());

            //load each file inside the current bucket
            foreach ($files as $file) {
                if ($cloudFiles->load($file['id'])) {
                    //delete the file
                    $cloudFiles->delete();
                }
                $cloudFiles->clean();
            }

        }

    }

    /**
     * Actions to be taken after deleted the relevant bucket.
     */
    protected function afterDelete() {

        if ($this->isSoftDelete()) {
            //create cloud drive process for delete files in cloud drives
            CloudFile::process('delete', $this);
        }

    }

    /**
     * Rename the relevant bucket name.
     *
     * -- This also starts the process of renaming files
     * in the cloud drive.
     *
     * @param string $name new name for bucket
     * @return bool success or failure
     */
    public function rename(string $name): bool {

        $tmpData = [
            'location' => $this->getLocation()
        ];
        $tmpData = Help::toJson($tmpData);

        if ($this->assign([
            'name' => $name,
            'tmp' => $tmpData
        ])->save()) {
            //create cloud drive process for rename files in cloud drives
            CloudFile::process('rename', $this);

            return true;

        }

        return false;

    }

    /**
     * Get the relevant bucket full name - name with file extension
     * @return string
     */
    public function getFullName(): string {

        if ($this->isEdit()) {
            return $this->getName() . '.' . $this->getExt();
        }
        return '';

    }

    /**
     * Get the full name of the temporary file that holds
     * in the local storage.
     * @return string
     */
    public function getTmpFullName(): string
    {
        if ($this->isEdit()) {
            return $this->getUniqId() . '.' . $this->getExt();
        }
        return '';
    }

    /**
     * Get the folder location where the temporary file is kept
     * in the local storage.
     * @return string
     */
    public function getTmpDir(): string
    {
        if ($this->isEdit()) {
            return Help::cleanDS(Help::storagePath('tmp') . '/' . $this->getUniqId());
        }
        return '';
    }

    /**
     * Get the location where the temporary file is kept
     * in the local storage.
     * @return string
     */
    public function getTmpFile(): string
    {
        if($this->isEdit()){
            return Help::cleanDS( $this->getTmpDir() . '/tmp.' . $this->getExt());
        }
        return '';
    }

    /**
     * Check if the bucket related temporary file exists
     * @return bool exist or not exist
     */
    public function isTmpFileExist(): bool
    {

        if($this->isEdit()){

            $file = $this->getTmpFile();
            if(file_exists($file)){

                return true;

            }

        }

        return false;

    }

    /**
     * Notice that the temporary file in the relevant bucket is ready
     * to be uploaded to the cloud drive.
     * @return bool success or failure
     */
    public function uploadReady() : bool{

        if($this->isEdit()){

            $this->db->where('bucketId', $this->getID());
            $this->db->where('isUsed', 1);
            $this->db->where('pstatus', Files::WAITING);

            return $this->db->update(Files::getTbl(), ['isUsed'=>0]);

        }

        return false;
    }

    /**
     * Cancel uploading files in the bucket
     * @param string $reason Reason for cancellation
     */
    public function cancelUpload(string $reason = ''){

        if($this->isEdit()){

            if(empty($reason)) {
                $reason = 'Canceled by cloud monster';
            }

            $tmpFile = new Files;
            $files = $tmpFile->get([
                'bucketId' => $this->getID(),
                'pstatus' => Files::WAITING
            ]);
            if(!empty($files)){
                foreach ($files as $file){
                    if($tmpFile->load($file['id'])){
                        $tmpFile->canceled();
                        $tmpFile->addMsg($reason);
                        $tmpFile->clean();
                    }
                }
            }

        }

    }

    /**
     * Retrieve a file randomly from the relevant bucket,
     * depending on the file type or not.
     * @param string $sourceType file source type
     * @return Files
     */
    public function findFileByRand(string $sourceType = ''): Files
    {

        $fileObj = new Files();

        if($this->isEdit()){

            if(!empty($sourceType)){

                $tmpCloudDrives = new CloudDrives();
                $drives = $tmpCloudDrives->get([
                    'type' => $sourceType
                ],[],['id']);

                if(!empty($drives)){

                    $drives = Help::extractData($drives, 'id');
                    $this->db->where('cloudDriveId', $drives , 'IN');

                }else{

                    return $fileObj;

                }

            }

            $this->db->where('bucketId', $this->getID());
            $this->db->where('status', 0);
            $this->db->where('pstatus', Files::ACTIVE);
            $this->db->orderBy('RAND()');

            $result = $this->db->getOne(Files::getTbl());

            if($this->db->count > 0){

                $fileObj->load($result['id']);

            }

        }

        return $fileObj;

    }

    /**
     * Toggle bucket sharing
     * @return bool success or failure
     */
    public function toggleShared(): bool
    {

        if($this->isEdit()){

            $status = !((bool) $this->getShared());
            return $this->assign(['shared'=>$status])->save();

        }

        return false;

    }

    /**
     * Update re-upload session in the current bucket
     * @param int $st  enabled or disabled
     * @return bool success or failure
     */
    public function updateReUploadSession(int $st): bool
    {

        if($this->isEdit()){
            return $this->assign(['autoReUploadSession'=>$st])->save();
        }

        return false;

    }

    /**
     * Check if re-upload session is active or not
     * @return bool active or inactive
     */
    public function isReUploadSessionActive(): bool
    {

        if($this->isEdit()){
            return $this->getAutoReUploadSession() == Buckets::ACTIVE_RE_UPLOAD_SESSION;
        }

        return false;

    }

    /**
     * Check if there are still files left to upload to the
     * cloud drive in the relevant bucket.
     * @return bool
     */
    public function isUploadQueryEmpty(): bool
    {

        if($this->isEdit()){

            $this->db->where('bucketId', $this->getID());
            $this->db
                ->where (
                    "(pstatus = ? or pstatus = ?)",
                    [Files::WAITING, Files::PROCESSING]
                );

            return !$this->db->has(Files::getTbl());

        }

        return false;

    }

    /**
     * Enable/ Disable bucket processing mode
     * @param bool $st
     * @return bool
     */
    public function processing(bool $st = true): bool
    {
        if($this->isEdit()){
            $st = (int) $st;
            $this->addVal(['isProcessing'=>$st]);
            return $this->save();
        }
        return false;
    }

    /**
     * Bucket is ready to use
     */
    public function done()
    {
        if($this->isEdit()){
            $this->processing(false);
        }
    }

    public function isDone(): bool
    {
        if($this->isEdit()){
            return $this->getIsProcessing() == 0;
        }
        return false;
    }

    /**
     * All files are not ready in the bucket
     * @return bool
     */
    public function filesNotReady(): bool
    {
        if($this->isEdit()){
            $this->db->where('bucketId', $this->getID());
            return $this->db->update(Files::getTbl(), ['isUsed'=>1]);
        }
        return false;
    }



    /**
     * Bucket is broken
     * @return bool
     */
    public function broken(): bool
    {
        if($this->isEdit()){
            $files = $this->getFiles();
            $fileObj = new Files();
            foreach ($files as $file){
                if($fileObj->load($file['id'])){
                    $fileObj->canceled();
                    $fileObj->addMsg('Source file not found');
                }
                $fileObj->clean();
            }
        }
        return false;
    }

    /**
     * Get all files inside the bucket
     * @return array
     */
    public function getFiles(): array
    {
        if($this->isEdit()){
            $files = new Files();
            return $files->getByBucketId($this->getID());
        }
        return [];
    }

    /**
     * Get public bucket link
     * @param $slug
     * @return string
     */
    public static function getPublicLink($slug): string
    {
        return siteurl() . '/' . App::getCustomSlug('bucket') . '/' . $slug;
    }





}
