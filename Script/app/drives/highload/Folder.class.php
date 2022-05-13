<?php

namespace CloudMonster\Drives\Highload;


use CloudMonster\Drives\BaseFile;
use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Highload
 */

class Folder extends BaseFile {




    /**
     */
    public function create($name)
    {

        try{

            $parentFolderId = $this->getParentId();
            $query = ['name'=>$name];
            if(!empty($parentFolderId)) {
                $query['pid'] = $parentFolderId;
            }

            $resp = $this->app->call(
                'get',
                '/file/createfolder',
                [
                    'query' => $query
                ]
            );

            if(isset($resp['folderid'])){
                return $resp['folderid'];
            }

        }catch(Exception $e){

            $this->app->addError($e->getMessage());

        }

        return false;


    }

    /**
     * Rename file
     * @param $name
     * @return bool
     */
    public function rename($name): bool
    {

        try{

            $resp = $this->app->call(
                'get',
                '/file/renamefolder',
                [
                    'query' => [
                        'folder' => $this->getId(),
                        'name' => $name
                    ]
                ]
            );

            if($resp == 'true'){
                return true;
            }



        }catch(Exception $e){

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
        try{

            $resp = $this->app->call(
                'get',
                '/file/deletefolder',
                [
                    'query' => [
                        'folder' => $this->getId()
                    ]
                ]
            );

            if($resp == 'true'){
                return true;
            }

        }catch(Exception $e){

            $this->app->addError($e->getMessage());

        }

        return false;
    }


    public function move(): bool
    {
        return false;
    }


}