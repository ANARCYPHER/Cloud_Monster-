<?php

namespace CloudMonster\Drives\Dropbox;



use CloudMonster\Drives\BaseFile;
use CloudMonster\Helpers\Help;
use Kunnu\Dropbox\Exceptions\DropboxClientException;
use Kunnu\Dropbox\Models\FileMetadata;
use Kunnu\Dropbox\Models\FolderMetadata;
use Kunnu\Dropbox\Models\ModelInterface;

/**
 * Class File
 * @package CloudMonster\Drives\Dropbox
 */

class File extends BaseFile {


    /**
     * Get file
     * @param false $isMeta
     * @return bool|ModelInterface|FolderMetadata|FileMetadata
     */
    public function get(bool $isMeta = false): bool|ModelInterface|FolderMetadata|FileMetadata
    {
        try{

            return $this->app->service->getMetadata('/' . $this->getLocation() , ["include_media_info" => $isMeta]);

        }catch(DropboxClientException $e){

            $this->app->addError($e->getMessage());

        }
        return false;
    }

    /**
     * Delete file
     * @return bool
     */
    public function delete(): bool
    {
        try {

             $this->app->service->delete('/' . $this->getLocation());
             return true;
        }catch(DropboxClientException $e) {

            $this->app->addError($e->getMessage());

        }
        return false;
    }

    /**
     * Copy file
     * @param string $parentFolderId
     * @return bool|string
     */
    public function copy(string $parentFolderId = ''): bool|string
    {
        try {

            $newFilename = Help::autoRename('Copy of ' . $this->getId());
            if(!empty($parentFolderId)) $newFilename = "{$parentFolderId}/" . $newFilename;
            $file = $this->app->service->copy('/' . $this->getId(), '/'. $newFilename);
            return $file->getName();

        }catch(DropboxClientException $e) {

            $this->app->addError($e->getMessage());

        }
        return false;
    }

    public function move(): bool
    {
        try {

            $destination = $this->getTmp('location');
            $this->app->service->move('/'. $destination, '/' . $this->getLocation());

            return true;

        }catch(DropboxClientException $e) {

            $this->app->addError($e->getMessage());
        }

        return false;
    }


    public function rename(string $name): bool
    {
        return $this->move();
    }


    public function check() : bool{

        if($file = $this->get()){

        }else{
            if(strpos($this->getError(), 'path/not_found/') !== false){
                return false;
            }
        }

        return true;
    }


}