<?php

namespace CloudMonster\Drives\Streamlare;


use CloudMonster\Drives\BaseFile;
use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Streamlare
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
                $query['parent'] = $parentFolderId;
            }

            $resp = $this->app->call(
                'get',
                '/folder/create',
                [
                    'query' => $query
                ]
            );

            if(isset($resp['id'])){
                return $resp['id'];
            }

        }catch(Exception $e){

            $this->app->addError($e->getMessage());

        }

        return false;


    }

    public function rename(string $name) : bool
    {

        try{

            $resp = $this->app->call(
                'get',
                '/folder/rename',
                [
                    'query' => [
                        'folder' => $this->getId(),
                        'name' => $name
                    ]
                ]
            );

           return true;

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
                '/folder/delete',
                [
                    'query' => [
                        'folder' => $this->getId()
                    ]
                ]
            );

            return true;

        }catch(Exception $e){

            $this->app->addError($e->getMessage());

        }

        return false;
    }


    /**
     * Move file
     * @return bool
     */
    public function move(): bool
    {
        try{

            $destFldId = $this->getParentId();

            $resp = $this->app->call(
                'get',
                '/folder/move',
                [
                    'query' => [
                        'folderFrom' => $this->getId(),
                        'folderTo' => $destFldId
                    ]
                ]
            );

            return true;

        }catch(Exception $e){

            $this->app->addError($e->getMessage());

        }

        return false;
    }









}