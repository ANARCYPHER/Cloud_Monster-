<?php

namespace CloudMonster\Drives\Streamlare;


use CloudMonster\Drives\BaseFile;
use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Streamlare
 */

class File extends BaseFile {


    /**
     * Get file
     */
    public function get() : array
    {
        try{

            $resp = $this->app->call(
                'get',
                '/file/get',
                [
                    'query' => [
                        'file' => $this->getId()
                    ]
                ]
            );

            if(!empty($resp)){

                return array_shift($resp);
            }

        }catch (Exception $e){

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

            $query = ['file'=>$this->getId()];
            if(!empty($destFldId)){
                $query['folder'] = $destFldId;
            }

            $resp = $this->app->call(
                'get',
                '/file/move',
                [
                    'query' => $query
                ]
            );

            return true;

        }catch(Exception $e){

            $this->app->addError($e->getMessage());

        }

        return false;

    }

    /**
     * Get file
     */
    public function check() : bool
    {
        try{

            $resp = $this->app->call(
                'get',
                '/file/poster/get',
                [
                    'query' => [
                        'file' => $this->getId()
                    ]
                ]
            );

        }catch (Exception $e){

            if($e->getMessage() == 'File not found'){
                return false;
            }

        }


        return true;

    }





}