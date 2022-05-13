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

use CloudMonster\Helpers\Help;
use CloudMonster\Services\CloudUpload;


/**
 * Class BaseUploader
 * @author John Antonio
 * @package CloudMonster\Drives
 */
class BaseUploader
{


    /**
     * file ID
     */
    protected int $id = 0;

    /**
     * Unique file ID
     * @var string
     */
   protected string $uniqId = '';

    /**
     * File Info
     * @var object
     */
    protected object $file;

    /**
     * Upload chunk size
     * @var int
     */
    protected int $chunkSize =  262144;

    /**
     * Is successfully uploaded
     * @var bool
     */
    protected bool $isOk = false;

    /**
     * Uploaded fie link
     * @var string
     */
    protected $uploadeFile = '';

    /**
     * Filename for uploaded file
     * @var string
     */
    protected string $filename = '';

    /**
     * parent folder info
     * @var array
     */
    protected array $parentFolder = [];

    /**
     * Upload location
     * @var string
     */
    protected string $location = '';


    /**
     * Upload constructor.
     * @param  $app
     * @param string $uniqId
     * @param int $fileId
     */
    public function __construct($app, string $uniqId = '', int $fileId = 0  )
    {
        $this->app = $app;
        $this->uniqId = $uniqId;
        $this->id = $fileId;

        $this->chunkSize = CloudUpload::getUploadChunkSize();

        $this->setFile();

    }

    /**
     * Set file
     */
    protected function setFile() : void{
        $file = Help::getUploadedFile($this->uniqId);
        if(!empty($file) && file_exists($file) && !is_dir($file)){
            $fInfo = new \finfo();
            $mimeType = $fInfo->file($file, FILEINFO_MIME_TYPE);
            $filename = $this->id . '_' . pathinfo($file, PATHINFO_BASENAME);
            $fileSize = filesize($file);

            //check chunk size is large than file size
            if($fileSize <= $this->chunkSize){
                $this->chunkSize = 262144;
            }

            $data = [
                'filename' => $filename,
                'mimeType' => $mimeType,
                'size' => $fileSize,
                'path' => $file
            ];
            $this->file = Help::toObject($data);
        }
    }

    /**
     * Check file isset or not
     * @return bool
     */
    protected function issetFile(): bool
    {
        return isset($this->file) && !empty($this->file);
    }

    /**
     * Get current uniq ID
     * @return string
     */
    protected function getId(): string
    {
        return $this->id;
    }

    /**
     * Get filename
     * @return string
     */
    public function getFilename(): string
    {
        $ext =  pathinfo($this->file->path, PATHINFO_EXTENSION);
        return !empty($this->filename) ? $this->filename : Help::random(15) . '.' . $ext;
    }

    public function setFilename($name = ''){
        $this->filename = $name;
    }

    /**
     * Get uploaded file ID
     * @return string
     */
    public function getFileId(): string
    {

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
     * Get upload location
     * @return string
     */
    public function getLocation(): string
    {
        if(empty($this->location)){
            return $this->getFilename();
        }
        return $this->location;
    }

    /**
     * Set upload location info
     * @param string $location
     */
    public function setLocation(string $location = ''){
        $this->location = rtrim($location, '/');
    }

    public function __get($name){
        if(property_exists($this, $name)){
            return $this->$name;
        }
        return '';
    }

    public function getSharedLink(): string
    {
        if(isset($this->sharedLink)){
            return $this->sharedLink;
        }
        return '';
    }

    public function getError(): string
    {
        return  $this->app->getError();
    }


}