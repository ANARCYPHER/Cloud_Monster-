<?php

namespace CloudMonster\Drives\Uptobox;


use CloudMonster\Drives\BaseFile;
use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Uptobox
 */

class Folder extends BaseFile {


    public function setName($name){
        $this->name = $name;
    }

    public function setPath($name){
        $this->name = $name;
    }

    /**
     * Get current file ID
     * @return string
     */
    public function getId(): string
    {
        return !empty($this->id) ? $this->id : 'xxx';
    }


    /**
     */
    public function create($name)
    {


        try{

            $resp = $this->app->call(
                'put',
                '/user/files',
                [
                    'query' => [
                        'path' => '//' . $this->location,
                        'name' => $name
                    ]
                ]
            );

            return $this->getFldId($this->getLocation($name));

        }catch(Exception $e){

            $this->app->addError($e->getMessage());

        }

        return false;
    }




    public function getFldId($path = ''){


        try{
            $resp = $this->app->call(
                'get',
                '/user/files',
                [
                    'query' => [
                        'path' => '//' . $path,
                        'limit' => 1
                    ]
                ]
            );

            if(!empty($resp['currentFolder'])){

                return $resp['currentFolder']['fld_id'];

            }

        }catch(Exception $e){

            $this->app->addError($e->getMessage());

        }

        return '';

    }

    /**
     * Get file
     * @throws Exception
     */
    public function get() : array
    {
        return $this->app->call(
            'get',
            '/file/info',
            [
                'query' => [
                    'file_code' => $this->getId()
                ]
            ]
        );
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
                'patch',
                '/user/files',
                [
                    'query' => [
                        'fld_id' => $this->getId(),
                        'new_name' => $name
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
                'delete',
                '/user/files',
                [
                    'query' => [
                        'fld_id' => $this->getId()
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
                'patch',
                '/user/files',
                [
                    'query' => [
                        'fld_id' => $this->getId(),
                        'destination_fld_id' => $destFldId,
                        'action' => 'move'
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