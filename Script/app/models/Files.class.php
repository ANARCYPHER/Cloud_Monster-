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
 * Class Files
 * @author John Antonio
 * @package CloudMonster\Models
 */
class Files extends Model
{

    /**
     * Database table
     * @var string table name
     */
    protected static string $tmpTbl = "files";

    # active file status code number
    const ACTIVE = 1;

    # inactive file status code number
    const INACTIVE = 0;

    # waiting file status code
    const WAITING = 2;

    # processing file status code
    const PROCESSING = 3;


    /**
     * Files constructor.
     */
    public function __construct()
    {
        parent::__construct($this::$tmpTbl);
    }


    /**
     * Update the functional status of the relevant file
     * @param int $st status code
     * @return bool success or failure
     */
    protected function updatePStatus(int $st): bool
    {
        return $this->assign(['pstatus'=>$st])->save();
    }

    /**
     * Check the file is active
     * @return bool active or not
     */
    public function isActive(): bool
    {
        return $this->getPstatus() == $this::ACTIVE;
    }

    /**
     * Check the file is inactive
     * @return bool inactive or not
     */
    public function isInactive(): bool
    {
        return $this->getPstatus() == $this::INACTIVE;
    }

    /**
     * Check the file is waiting
     * @return bool waiting or not
     */
    public function isWaiting(): bool
    {
        return $this->getPstatus() == $this::WAITING;
    }

    /**
     * Check the file is processing
     * @return bool processing or not
     */
    public function isProcessing(): bool
    {
        return $this->getPstatus() == $this::PROCESSING;
    }

    /**
     * Notice that the file has been successfully used for
     * uploading to the cloud drive.
     * @return bool
     */
    public function used(): bool
    {
        return $this->assign(['isUsed'=>1])->save();
    }

    /**
     * Notice that the file has been successfully uploaded
     * to the cloud drive.
     * @return bool
     */
    public function active(): bool
    {
        return $this->updatePStatus($this::ACTIVE);
    }

    /**
     * Notice that the file has not been successfully uploaded
     * to the cloud drive.
     * @return bool
     */
    public function inactive(): bool
    {
        return $this->updatePStatus($this::INACTIVE);
    }

    /**
     * Notice that the file is pending upload to the cloud drive
     * @return bool
     */
    public function await(): bool
    {
        return $this->updatePStatus($this::WAITING);
    }

    /**
     * Notify that the relevant file is being uploaded
     * to the cloud drive.
     * @return bool
     */
    public function processing(): bool
    {
        return $this->updatePStatus($this::PROCESSING);
    }

    /**
     * Notice that the upload of the relevant file
     * to Cloud Drive has been canceled.
     */
    public function canceled(){
        $this->used();
        $this->inactive();
    }

    /**
     * Add message
     * @param string $msg message
     * @return bool
     */
    public function addMsg(string $msg = ''): bool
    {
        return $this->assign(['msg'=>$msg])->save();
    }

    /**
     * Counting Files Uploading to a Cloud Drive
     * @return int
     */
    public function countProcessingFiles() : int{
        $this->db->where('pstatus', $this::PROCESSING);
        $this->db->where('isUsed', 1);
        return $this->db->getValue ($this->tbl, "count(*)");
    }

    /**
     * Format file status
     * @param $st
     * @return string
     */
    public static function formatStatus($st): string
    {
        $m = '';
        switch($st){
            case self::ACTIVE:
                $m = 'active';
                break;
            case self::INACTIVE:
                $m = 'failed';
                break;
            case self::WAITING:
                $m = 'waiting';
                break;
            case self::PROCESSING:
                $m = 'process';
                break;
            default:
                $m = 'unknown';
        }
        return $m;

    }

    public static function getStatusCode($st): int
    {
        $m = 9; //fake code
        switch($st){
            case 'active':
                $m = self::ACTIVE;
                break;
            case 'failed':
                $m = self::INACTIVE;
                break;
            case 'waiting':
                $m = self::WAITING;
                break;
            case 'process':
                $m = self::PROCESSING;
                break;
        }
        return $m;

    }

    /**
     * Get files by bucket ID
     * @param int $id bucket ID
     * @return array data of bucket
     */
    public function getByBucketId(int $id) : array{
        $list = $this->getAllFiles($id);
        return array_reverse($list);

    }

    /**
     * Obtain a complete description of the relevant file
     * @return array file info
     */
    public function getFileInfo() : array{

        if($this->isEdit()){

            $this->db->join(CloudDrives::getTbl() . ' c', 'f.cloudDriveId = c.id', 'LEFT');
            $this->db->join(Buckets::getTbl() . ' b', 'b.id = f.bucketId', 'LEFT');
            $this->db->groupBy('f.id');
            $this->db->where('f.id', $this->getID());
            $data = $this->db->getOne($this->tbl . ' f',  'f.*,b.name as fileName, c.name, c.type');

            if($this->db->count > 0){

                $data['fstatus'] = $this::formatStatus($data['pstatus']);
                return $data;

            }

        }

        return [];

    }

    /**
     * Retrieve all files
     * @param int $bucketId (optional) bucket ID
     * @param int|string $pstatus (optional) process status code
     * @param int $driveId (optional) cloud drive ID
     * @return array
     */
    public function getAllFiles(int $bucketId = 0, int|string $pstatus = '', int $driveId = 0) : array{

        $this->db->join(CloudDrives::getTbl() . ' c', 'f.cloudDriveId = c.id', 'LEFT');
        $this->db->join(Visitors::getTbl() . ' v', 'v.fileId = f.id', 'LEFT');
        $this->db->join(Buckets::getTbl() . ' b', 'b.id = f.bucketId', 'LEFT');
        if(!empty($bucketId))
            $this->db->where('f.bucketId', $bucketId);
        if(!empty($driveId))
            $this->db->where('f.cloudDriveId', $driveId);
        if(is_numeric($pstatus))
            $this->db->where('f.pstatus', $pstatus);
        $this->db->orderBy('f.id', );
        $this->db->groupBy('f.id');
        $this->db->where('f.status', 0);
        $data = $this->db->get($this->tbl . ' f', null, 'f.*,b.name as fileName, c.name, c.type, sum(ifnull(v.visit, 0)) as totalVisits, count(v.visit) as uniqVisits');

        if($this->db->count > 0){
            foreach ($data as $k => $file){
                $data[$k]['pstatus'] = $this::formatStatus($file['pstatus']);
            }
            return $data;
        }

        return [];

    }


    /**
     * Update the status of those files if the process has stopped
     * unexpectedly while uploading the relevant files to the cloud drive.
     * @return bool
     */
    public function isAlive(): bool
    {

        if($this->isEdit()){

            $lastUpdated = $this->getUpdatedAt();
            $timeFirst = strtotime($lastUpdated) + 60;
            $timeSecond = strtotime(Help::timeNow());
            if ($timeFirst >= $timeSecond) {
                return true;
            }

        }

        return false;

    }

    /**
     * Actions to be taken before deleting the relevant file.
     */
    protected function beforeDelete()
    {
        if(!$this->isSoftDelete()){

            //attempt to remove tracker
            $this->removeTracker();

            //attempt to delete visitors data related to this file
            $visitors = new Visitors();
            $visitors->delByFileId($this->getID());

        }
    }

    /**
     * Removing the file-tracking processor related to the file
     */
    public function removeTracker(){
        if($this->isEdit()){
            $trackers = new ProcessTracker();
            $trackers->delByFileId($this->getID());
            unset($trackers);
        }
    }

    /**
     * Obtain the original link to the file
     * @return string file link
     */
    public function getOriginalFileLink(): string
    {

        $link = '';

        if($this->isEdit()){

            if(empty($this->getSharedLink())){

                $tmpCloudDrive = CloudDrives::getInstance();

                if($tmpCloudDrive->load($this->getCloudDriveId())){

                    $data = CloudDrives::getStructure($tmpCloudDrive->getType());
                    if(isset($data['baseUrl']) && isset($data['sharedFileUrl'])){
                        $link = $data['sharedFileUrl'];
                        $link = str_replace(['{ BASE_URL }', '{ FILE_CODE }'], [$data['baseUrl'], $this->getCode()], $link);
                    }

                }

                unset($tmpCloudDrive);

            }else{

                $link = $this->getSharedLink();

            }


        }

        return !empty($link) && Help::isUrl($link) ? $link : '';

    }

    /**
     * Check whether the file is functional or not
     * @return bool
     */
    public function needToCheck(): bool
    {

        if($this->isEdit()){
            $checkTime = App::getConfig('file_check_time') * 60 * 60;
            if(!empty($checkTime) && is_numeric($checkTime)){

                $lastChecked = $this->getLastCheckedAt();
                $timeFirst = strtotime($lastChecked) + $checkTime;
                $timeSecond = strtotime(Help::timeNow());
                if ($timeFirst < $timeSecond) {
                    return true;
                }

            }

        }
        return false;

    }

    /**
     * Check whether the file is functional or not
     * @param bool $success success or failure
     * @return bool
     */
    public function checked(bool $success = false): bool
    {

        if(!$success && !$this->isActive()){
            return false;
        }

        $success ? $this->active() : $this->inactive();
        $msg = !$success ? 'file not found' : '';
        return $this->assign([
            'lastCheckedAt' => Help::timeNow(),
            'msg' => $msg
        ])->save();
    }

    /**
     * Check the functionality of the file
     * @return bool success or failure
     */
    public function check(): bool
    {

        if($this->isEdit()){

            $tmpBucket = new Buckets();
            $tmpBucket->load($this->getBucketId());

            $cloudFile = new CloudFile($tmpBucket->getID(), true);
            $cloudFile->setCloudFiles([$this->getID()]);
            $cloudFile->operate('check');

            $this->reload();


            unset($cloudFile);
            unset($tmpBucket);

            return true;

        }

        return false;

    }

    /**
     * Get public file link
     * @param $slug
     * @return string
     */
    public static function getPublicLink($slug): string
    {
        return siteurl() . '/' . App::getCustomSlug('file') . '/' . $slug;
    }





}