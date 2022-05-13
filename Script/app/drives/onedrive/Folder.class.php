<?php


namespace CloudMonster\Drives\Onedrive;



use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Onedrive
 */

class Folder extends File {


    /**
     * @throws Exception
     */
    public function create($name){
        try{
            $folderId = !empty($this->getParentId()) ? $this->getParentId() : null;
            $resp = $this->app->service->items->createFolder($name, $folderId);
            return $resp->getId();
        }catch (Exception $e){
            $this->app->addError($this->app->decodeError($e));
        }

        return false;
    }



}