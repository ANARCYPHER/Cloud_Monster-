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

use CloudMonster\Services\CloudUpload;


/**
 * Class BaseDownload
 * @author John Antonio
 * @package CloudMonster\Drives
 */
class BaseDownload{

    /**
     * file ID
     * @var string
     */
    protected string $id = '';

    /**
     * Download uniq ID
     * @var string
     */
    protected string $uniqId = '';

    /**
     * Filename
     * @var string
     */
    protected string $filename = '';

    /**
     * Download chunk size
     * @var int
     */
    protected int $chunkSize =  262144;

    protected string $location = '';


    /**
     * File size
     * @var int
     */
    protected int $size = 0;


    /**
     * File download path
     * @var string
     */
    protected string $filepath = '';

    protected string $progressId;


    /**
     * Current drive app
     * @var object
     */
    protected object $app;


    /**
     * BaseDownload constructor.
     * @param  $app
     * @param string $fileId
     */
    public function __construct($app,string $fileId)
    {
        $this->app = $app;
        $this->id = $fileId;
        $this->chunkSize = CloudUpload::getUploadChunkSize();
    }

    /**
     * Set filename for file
     * @param $name
     */
    public function setFilename($name){
        $this->filename = $name;
    }

    /**
     * set unique identity number
     * @param $uniqId
     */
    public function setUniqId($uniqId){
        $this->uniqId = $uniqId;
    }

    /**
     * Set file path
     * @param $path
     */
    public function setFilePath($path){
        $this->filepath = $path;
    }

    /**
     * Set filesize
     * @param $size
     */
    public function setSize($size){
        $this->size = $size;
    }

    /**
     * Run downloader
     */
    protected function run(){

    }

    /**
     * Set taregt file location in cloud drive
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
     * Get error
     * @return string
     */
    public function getError(): string
    {
        return  $this->app->getError();
    }

    /**
     * Get direct download link with file name
     * @param string $url
     * @return array
     */
    public static function getDL(string $url) : array{
        return [
            'link' => '',
            'filename' => ''
        ];
    }

    public function setProgressId($id){
        $this->progressId = $id;
    }

    public function getProgressId(): string
    {
        return !empty($this->progressId) ? $this->progressId : $this->uniqId;
    }


}