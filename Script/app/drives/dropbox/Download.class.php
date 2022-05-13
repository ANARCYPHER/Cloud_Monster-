<?php


namespace CloudMonster\Drives\Dropbox;


use CloudMonster\Drives\BaseDownload;
use CloudMonster\Services\UploadProgress;
use Exception;


class Download extends BaseDownload
{


    /**
     * @throws Exception
     */
    public function run(){

        // Download and the save the file at the given path



        $this->app->service->setProgressCallback(new UploadProgress($this->uniqId, [
            'fileSize' => $this->size,
            'keepUpload' => true,
            'chunkSize' => $this->chunkSize
        ]));

        $this->app->service->download('/' . $this->getLocation(), $this->filepath);


        if(!file_exists($this->filepath)){
            throw new Exception('File not downloaded');
        }

    }



}