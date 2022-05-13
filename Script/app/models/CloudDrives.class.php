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


/**
 * Class CloudDrives
 * @author John Antonio
 * @package CloudMonster\Models
 */
class CloudDrives extends Model
{

    /**
     * Database table
     * @var string table name
     */
    protected static string $tmpTbl = "cloud_drives";

    /**
     * The relevant cloud drive app
     * @var object
     */
    public object $cloudApp;

    # Broken cloud drive status code
    const DRIVE_ERROR = 2;

    # paused cloud drive status code
    const DRIVE_PAUSED = 1;

    # active cloud drive status code
    const DRIVE_ACTIVE = 0;


    /**
     * CloudDrives constructor.
     */
    public function __construct()
    {
        parent::__construct($this::$tmpTbl);
    }

    /**
     * update the relevant cloud drive status.
     * (active/ failed/ paused)
     * @param int $st status code
     * @return bool success or failure
     */
    protected function updatePStatus(int $st): bool
    {
        return $this->assign(['status'=>$st])->save();
    }

    /**
     * Notification that an error has occurred while authentication
     * the relevant cloud drive.
     * @return bool success or failure
     */
    public function error(): bool
    {
        return $this->updateStatus($this::DRIVE_ERROR);
    }

    /**
     * Notification that there is no error while authentication
     * the relevant cloud drive.
     * @return bool success or failure
     */
    public function active(): bool
    {
        return $this->updateStatus($this::DRIVE_ACTIVE);
    }

    /**
     * Notice that the relevant cloud driver has been paused.
     * @return bool success or failure
     */
    public function paused(): bool
    {
        return $this->updateStatus($this::DRIVE_PAUSED);
    }

    /**
     * Check if the relevant cloud drive has error.
     * @return bool
     */
    public function isError(): bool
    {
        return $this->getStatus() === $this::DRIVE_ERROR;
    }

    /**
     * Check if the relevant cloud drive is active.
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getStatus() === $this::DRIVE_ACTIVE;
    }

    /**
     * Check if the relevant cloud drive is paused.
     * @return bool
     */
    public function isPaused(): bool
    {
        return $this->getStatus() === $this::DRIVE_PAUSED;
    }

    /**
     * Get all the relevant cloud drivers along with the relevant
     * bucket information.
     * @return array Returns the list of cloud drives
     */
    public function getList() : array{


        $this->db->join(Files::getTbl() . ' f', 'c.id = f.cloudDriveId', 'LEFT');
        $this->db->join(Buckets::getTbl() . ' b', 'f.bucketId = b.id', 'LEFT');
        $this->db->groupBy("c.id");

        $data = $this->db->get($this->tbl . ' c', null, 'c.*, count(f.id) as files,  count(b.id) as buckets, sum(b.size) as size');

        if($this->db->count > 0){

           foreach ($data as $key => $val){
                $data[$key]['fstatus'] = $this::getFormattedStatus($val['status']);
           }

           return $data;

        }


        return [];

    }

    /**
     * Make the status number of the relevant cloud drive easy to read
     * @param int $st status code
     * @return string formatted status
     */
    public static function getFormattedStatus(int $st): string
    {

        switch($st){

            case self::DRIVE_ACTIVE:
                $status = 'active';
                break;
            case self::DRIVE_PAUSED:
                $status = 'paused';
                break;
            case self::DRIVE_ERROR:
                $status = 'error';
                break;
            default:
                $status = 'unknown';


        }

        return $status;

    }

    /**
     * Load the relevant cloud drive app for current drive
     * @return bool success or failure
     */
    public function loadCloudApp(): bool
    {
        if($this->isEdit()){
            $class = "\\CloudMonster\\Drives\\{$this->getType()}\\App";
            if(class_exists($class)){
                $this->cloudApp = new $class($this);
                try{
                    $this->cloudApp->connect();
                    return true;
                }catch(\Exception $e){

                }

            }
        }

        return false;
    }

    /**
     * Perform the following tasks before saving the
     * relevant cloud drive.
     */
    protected function beforeSave()
    {
        //assign drive name
        if(empty($this->getName())){
            $dc = $this->count([
                'type' => $this->getType()
            ]);
            $name = ucwords($this->getType()) . ' ' . $dc + 1;
            $this->addVal(['name'=>$name]);
        }
    }

    /**
     * Check if there are buckets  in the relevant cloud drive
     * @return bool
     */
    public function hasBuckets(): bool
    {
        if($this->isEdit()){

            $this->db->where('cloudDriveId', $this->getID());
            return $this->db->has(Files::getTbl());

        }
        return false;
    }

    /**
     * Get cloud drive format structure
     * @param string $source
     * @return array drive structure
     */
    public static function getStructure(string $source = '') : array{
        $jsonData = Help::getVarJson('cloud_drives');
        $data = [];
        if(Help::isJson($jsonData)){
            $data = Help::toArray($jsonData);
            if(!empty($source)){
                $data = $data[$source] ?? [];
            }
        }
        return $data;
    }

    /**
     * Confirm we has permission to current cloud drive operate
     * @param string $operate drive operate
     * @param string $type file type - folder/bucket
     * @return bool
     */
    public static function hasOperatePermission(string $operate = '', string $type = 'folder'): bool
    {

        if(in_array($type, ['folder','bucket'])){
            if($type == 'bucket') $type = 'file';
            if($operate !== 'delete'){
                $targetConfig = $type . "_op_" . $operate;
                if(App::hasConfig($targetConfig)){
                    if(App::getConfig($targetConfig) == 1){
                        return true;
                    }
                }
            }else{

                return $type == 'file' ? CLOUD_FILE_DELETE : CLOUD_FOLDER_DELETE;

            }

        }

        return false;

    }




}