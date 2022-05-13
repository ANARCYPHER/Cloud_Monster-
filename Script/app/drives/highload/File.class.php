<?php

namespace CloudMonster\Drives\Highload;


use CloudMonster\Drives\BaseFile;
use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Highload
 */

class File extends BaseFile {


    /**
     * Get file
     * @throws Exception
     */
    public function get() : array
    {

        try{

            $resp = $this->app->call(
                'get',
                '/file/info',
                [
                    'query' => [
                        'file' => $this->getId()
                    ]
                ]
            );

            if(!empty($resp)){
                return array_shift($resp);
            }

        }catch(Exception $e){

            $this->app->addError($e->getMessage());

        }


        return [];

    }

    /**
     * Rename file
     * @param string $name
     * @return bool
     */
    public function rename(string $name): bool
    {
        try{


            $resp = $this->app->call(
                'get',
                '/file/rename',
                [
                    'query' => [
                        'file' => $this->getId(),
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
                '/file/delete',
                [
                    'query' => [
                        'file' => $this->getId(),
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

            if(empty($destFldId)) $destFldId = '0';

            $resp = $this->app->call(
                'get',
                '/file/move',
                [
                    'query' => [
                        'file' => $this->getId(),
                        'folder' => $destFldId
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




}