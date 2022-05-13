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
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Exceptions\PhpfastcacheSimpleCacheException;
use Phpfastcache\Helper\Psr16Adapter;


/**
 * Class UploadProgress
 * @author John Antonio
 * @package CloudMonster\Services
 */

class UploadProgress{


    /**
     * Uniq File ID
     * @var string|int
     */
    protected string|int $fileId = 0;

    /**
     * Upload progress start time
     * @var float
     */
    protected float $startTime;

    /**
     * Previous upload time
     * @var float
     */
    protected float $prevTime;

    /**
     * Previous upload size
     * @var int
     */
    protected int $prevSize;

    /**
     * Upload file size
     * @var int
     */
    protected int $fileSize = 0;

    /**
     * Cache adapter
     * @var Psr16Adapter
     */
    protected Psr16Adapter $cache;

    /**
     * Check status
     * @var bool
     */
    protected bool $isReady = false;

    /**
     * Uploaded progress (bytes) (for keep upload)
     * @var int
     */
    protected int $uploaded = 0;

    /**
     * Each chunk size (for keep upload)
     * @var int
     */
    protected int $chunkSize = 0;

    /**
     * Enable/disable keep upload
     * @var bool
     */
    protected bool $isKeepUpload = false;

    /**
     * Progress cache alive time
     * @var int
     */
    protected int $ttl = 60;


    /**
     * UploadProgress constructor.
     * @param int $fileId
     * @param array $config
     */
    public function __construct(int|string $fileId = 0,array $config = []){

        //set meta data
        $this->fileId = $fileId;

        if(!empty($config['fileSize']) && is_int($config['fileSize']))
            $this->fileSize = $config['fileSize'];

        if(isset($config['keepUpload']) &&  !empty($config['chunkSize']) )
            $this->keepUpload($config['chunkSize']);

        //attempt to init cache
        $this->initCache();

        //set start time
        $this->startTime = $this->prevTime = microtime(true);
        $this->prevSize = 0;

    }


    /**
     * Wait till start upload progress manually
     */
    public function wait(){
        $this->startTime = $this->prevTime = 0;
    }

    /**
     * Start upload progress manually
     * @param float $time
     */
    public function start(float $time = 0){
        if(empty($time)) $time = microtime(true);
        $this->startTime = $this->prevTime = $time;
    }

    /**
     * Chunk size
     * @param int $chunkSize
     */
    public function keepUpload(int $chunkSize){
        if($chunkSize >= ($this->fileSize - $chunkSize)){
            $chunkSize = $this->fileSize / 5;
        }
        $this->chunkSize = $chunkSize;
        $this->isKeepUpload = true;
    }

    /**
     * Initialize cache
     */
    protected function initCache(){
        try{
            $this->cache = new Psr16Adapter('files', new ConfigurationOption([
                'path' =>  Help::storagePath('cache') . '/upload/progress'
            ]));
            $this->isReady = true;
        }catch(\Throwable $e){

        }
    }

    /**
     * Record progress
     * @param int $uploaded
     * @throws PhpfastcacheSimpleCacheException
     */
    public function record(int $uploaded = 0){

        if($this->isReady){
//            \CloudMonster\Helpers\Logger::warn('P: ' . $this->fileSize);
            if(!empty($this->fileSize) && is_numeric($this->fileSize)){

                //calc progress percentage
                $progressPercent =  ($uploaded / $this->fileSize) * 100;
                if($progressPercent >= 100) $progressPercent = 99;

                //calc average and current update speed
                $averageSpeed = $uploaded / (microtime(true) - $this->startTime);
                $currentSpeed = ($uploaded - $this->prevSize) / (microtime(true) - $this->prevTime);

                //update previous time and previous file size
                $this->prevTime = microtime(true);
                $this->prevSize = $uploaded;

                $timeRemaining = ($this->fileSize - $uploaded) / $averageSpeed;
                if($timeRemaining < 0) $timeRemaining = 0;

                $progressData = [
                    'progress' => round($progressPercent),
                    'uploaded' => Help::formatSizeUnits($uploaded),
                    'avgSpeed' => Help::formatSizeUnits($averageSpeed). '/s',
                    'currentSpeed' => Help::formatSizeUnits($currentSpeed) . '/s',
                    'remainingTime' => round($timeRemaining)
                ];


                $this->cache->set($this->fileId, $progressData, $this->ttl);

                if($this->isKeepUpload) {
                    $this->uploaded = $uploaded;
                }

            }
        }

    }

    /**
     * CURL file upload progress callback
     * @param $clientp
     * @param $dltotal
     * @param $dlnow
     * @param $ultotal
     * @param $ulnow
     * @throws PhpfastcacheSimpleCacheException
     */
    public function CurlProgressCallback($clientp,$dltotal,$dlnow,$ultotal,$ulnow){

        if(!empty($ulnow)){
            $nextChunk = $this->uploaded + $this->chunkSize;
            if($nextChunk <= $ulnow){
                $this->record($ulnow);
            }
        }

    }


    /**
     * @param $clientp
     * @param $dltotal
     * @param $dlnow
     * @param $ultotal
     * @param $ulnow
     * @throws PhpfastcacheSimpleCacheException
     */
    public function CurlDownloadProgress($clientp,$dltotal,$dlnow,$ultotal,$ulnow){

        if(!empty($dlnow)){
            $nextChunk = $this->uploaded + $this->chunkSize;
            if($nextChunk <= $dlnow){
                $this->record($dlnow);
            }
        }

    }


    /**
     * Get upload progress
     * @return array|mixed
     * @throws PhpfastcacheSimpleCacheException
     */
    public function get(){
        return $this->isReady ? $this->cache->get($this->fileId) : [];
    }

    public function isActive($key){
        return $this->isReady && $this->cache->has($key);
    }

    public static function call($fileId): UploadProgress
    {
        return new self($fileId);
    }

    /**
     * Close progress
     */
    public function close(){
        if($this->isReady){
//            $this->cache->delete($this->fileId);
            unset($this->cache);
        }

    }







}