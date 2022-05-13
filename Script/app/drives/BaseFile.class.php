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

namespace CloudMonster\Drives;


use CloudMonster\Models\Buckets;
use CloudMonster\Models\Files;



/**
 * Class BaseFile
 * @author Jhon Antonio
 * @package CloudMonster\Drives
 */
class BaseFile{

    /**
     * @var object
     */
    protected object $app;

    /**
     * Current file ID
     * @var string
     */
    protected string $id = '';

    /**
     * File location
     * @var string
     */
    protected string $location = '';

    /**
     * Parent folder info
     * @var array
     */
    protected array $parentFolder = [];

    /**
     * tmp file info
     * @var array
     */
    protected array $tmp = [];

    /**
     * File not found error message
     * @var string
     */
    protected string $fileNotFoundError = '';

    /**
     * Target File instance
     * @var Files
     */
    protected Files $file;

    /**
     * File constructor.
     * @param  $app
     * @param string $fileId
     */
    public function __construct($app, string $fileId = ''){
        $this->app = $app;
        $this->id = $fileId;
    }

    /**
     * Get current file ID
     * @return string
     */
    public function getId(): string
    {
        return !empty($this->id) ? $this->id : 'xxx';
    }

    /**
     * Set file location
     * @param string $location
     */
    public function setLocation(string $location = ''){
        $this->location = rtrim($location, '/');
    }

    /**
     * Get file location
     * @param string $path
     * @return string
     */
    public function getLocation($path = ''): string
    {
        $location = $this->location ;
        if(!empty($path)) $location .=   '/' . $path;
        if(empty($this->location)) $location = ltrim($location, '/');
        return $location;
    }

    /**
     * Set parent folder info
     * @param array $folder
     */
    public function setParentFolder(array $folder = []){
        $this->parentFolder = $folder;
    }

    /**
     * Get parent folder ID
     * @return int|mixed
     */
    public function getParentId(){
        return $this->parentFolder['code'] ?? 0;
    }

    /**
     * Set temporary data
     * @param array $data
     */
    public function setTmp($data =[]){
        $this->tmp = $data;
    }

    /**
     * get temporary data
     * @param string $t
     * @return string
     */
    public function getTmp($t = '') : string{
        return $this->tmp[$t] ?? '';
    }

    /**
     * @throws \Exception
     */
    public function download(Buckets $bucket, $progressId = 0){

        $this->app->initDownload($this->id);

        $this->app->download->setSize($bucket->getSize());
        $this->app->download->setFilename($bucket->getTmpFullName());

        $tmpDir = $bucket->getTmpDir();
        if(!is_dir($tmpDir)){
            @mkdir($tmpDir);
        }

        $this->app->download->setUniqId($bucket->getUniqId());
        $this->app->download->setFilePath($bucket->getTmpFile());
        $this->app->download->setLocation($this->location);

        if(!empty($progressId)){
            $this->app->download->setProgressId($progressId);
        }

        $this->app->download->run();

    }

    /**
     * Check file is alive or not
     * @return bool
     */
    public function check() : bool{

        if(empty($this->get())){
            if($this->getError() == $this->fileNotFoundError){
                return false;
            }
        }
        return true;
    }

    /**
     * Rename file in cloud drive
     * @param string $name
     * @return bool
     */
    public function rename(string $name) : bool{
        return false;
    }

    /**
     * Delete file in cloud drive
     * @return bool
     */
    public function delete() : bool{
        return false;
    }

    /**
     * Move file in in cloud drive
     * @return bool
     */
    public function move() : bool{
        return false;
    }

    /**
     * Get file in cloud drive
     * @return array
     */
    public function get(){
        return [];
    }

    /**
     * Get error
     * @return string
     */
    public function getError(): string
    {
        return  $this->app->getError();
    }


}