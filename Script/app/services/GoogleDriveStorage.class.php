<?php

namespace CloudMonster\Services;


use CloudMonster\Models\Buckets;
use CloudMonster\Models\CloudDrives;
use Google\Service\Drive\DriveFile;

class GoogleDriveStorage{

    protected string $fileId;
    protected array $supportedDrives;
    protected DriveFile $fileData;
    protected bool $isLoaded = false;
    protected CloudDrives $currentDrive;
    protected Buckets $bucket;

    public function __construct($fileId, Buckets $bucket)
    {
        $this->fileId = $fileId;
        $this->bucket = $bucket;
        $this->currentDrive = new CloudDrives();
        $this->init()
             ->loadCurrentDrive();
    }

    public function hasFile(): bool
    {
        return $this->isLoaded;
    }

    public function download(): bool
    {

        try{

            $this->currentDrive
                ->cloudApp
                ->file
                ->download( $this->bucket , "b_" . $this->bucket->getID());

            return true;

        }catch(\Exception $e){

        }

        return false;
    }


    private function init(): static
    {
        $supportedDrives = $this->currentDrive->get([
            'type' => 'google',
            'status' => 0
        ]);
        if(! empty($supportedDrives)){
            $this->supportedDrives = $supportedDrives;
        }
        return $this;
    }

    private function loadCurrentDrive() : void
    {
        if(!empty($this->supportedDrives)){
            foreach ($this->supportedDrives as $drive){
                if($this->currentDrive->load($drive['id']) && $this->currentDrive->isActive()){
                    if($this->currentDrive->loadCloudApp()){
                        $this->currentDrive->cloudApp->initFile($this->fileId);
                        $file = $this->currentDrive->cloudApp->file->get();
                        if(!empty($file)){
                            if(! $file->trashed && $file->ownedByMe){
                                $this->fileData = $file;
                                $this->isLoaded = true;
                                break;
                            }
                        }
                    }
                }
                $this->currentDrive->clean();
            }
        }
    }

    public function fillBucket()
    {
        $this->bucket->assign([
            'name' => $this->fileData->name,
            'mime' => $this->fileData->mimeType,
            'size' => $this->fileData->size,
            'ext' => $this->fileData->fileExtension
        ])->save();
    }

}