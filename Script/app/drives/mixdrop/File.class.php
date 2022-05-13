<?php

namespace CloudMonster\Drives\Mixdrop;


use CloudMonster\Drives\BaseFile;
use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Mixdrop
 */

class File extends BaseFile {

    protected string $fileNotFoundError = 'file not found';

    /**
     * Get file
     * @throws Exception
     */
    public function get() : array
    {

        try{
            $resp = $this->app->call(
                'get',
                '/fileinfo',
                [
                    'query' => [
                        'ref[]' => $this->getId()
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
                '/filerename',
                [
                    'query' => [
                        'ref' => $this->getId(),
                        'title' => $name
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

            return true;

        }catch(Exception $e){

            $this->app->addError($e->getMessage());

        }

        return false;

    }




}