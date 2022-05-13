<?php

namespace CloudMonster\Services;


use CloudMonster\Helpers\Help;
use CloudMonster\Helpers\UploadedTmpFile;
use CloudMonster\Models\Buckets;

class RemoteUpload {

    protected string $link;
    protected string $linkType;
    protected Buckets $bucket;
    protected bool $isInit = false;

    public function __construct(Buckets $bucket)
    {
        $this->bucket = $bucket;
        if($bucket->isLoaded()){
            $this->isInit = true;
            $this->link = $bucket->getLink();
            $this->linkType = $this::getLinkType($this->link);
        }
    }

    public function run(){

        if($this->isInit){

            $tmpFile = $this->getFile();
            $this->bucket->done();

            if($tmpFile->isExist()){
                if($this->bucket->uploadReady()){
                    //attempt to run file upload process to cloud drives
                    ProcessManager::update();
                }
            }else{
                $this->bucket->broken();
            }

        }
    }

    protected function getFile(): UploadedTmpFile
    {
        if(!empty($this->link)){
            $success = false;

            if($this->linkType == 'google'){
                $googleStorage = new GoogleDriveStorage(Help::getGoogleDriveId($this->link), $this->bucket);
                if($googleStorage->hasFile()){
                    $googleStorage->fillBucket();
                    if($googleStorage->download()){

                        $success = true;
                    }
                }
            }

            if(! $success ){
                //attempt to get download file url
                $dl = $this::getDl($this->link);
                if(!empty($dl)){
                    //attempt to download remote file to server
                    $remoteUpload = new \CloudMonster\Helpers\RemoteDownload($dl);
                    if($remoteUpload->isOk()){
                        $remoteUpload->recordProgress($this->bucket->getID());
                        if($remoteUpload->setFolder("/{$this->bucket->getUniqId()}")
                            ->saveFile()){
                            $this->bucket->assign([
                                'mime' => $remoteUpload->getFileInfo('mime'),
                                'size' => $remoteUpload->getFileInfo('size'),
                                'ext' => $remoteUpload->getFileInfo('ext')
                            ])->save();
                        }
                    }
                }
            }

        }
        return new UploadedTmpFile($this->bucket->getUniqId());
    }

    public static function getDl(string $link) : string
    {
        $type = self::getLinkType($link);
        $dl = '';
        switch ( $type ){
            case 'google':
                    $results = \CloudMonster\Drives\Google\Download::getDL($link);
                    $dl = $results['link'] ?? '';
                break;
            case 'onedrive':
                    $results = \CloudMonster\Drives\Onedrive\Download::getDL($link);
                    $dl = $results['link'] ?? '';
                break;
            case 'direct':
                    $dl = $link;
                break;
        }
        return $dl;
    }

    /**
     * Get type of link
     * @param string $link
     * @return string
     */
    public static function getLinkType(string $link) : string
    {
        $type = 'direct';
        if(Help::isGoogleDrive($link)){
            $type = 'google';
        }elseif(Help::isOneDrive($link)){
            $type = 'onedrive';
        }
        return $type;
    }

}