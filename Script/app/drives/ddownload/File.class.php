<?php

namespace CloudMonster\Drives\Ddownload;


use CloudMonster\Drives\BaseFile;
use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Ddownload
 */

class File extends BaseFile {

    /**
     * File not found error message
     * @var string
     */
    protected string $fileNotFoundError = 'Invalid file codes';


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
                        'file_code' => $this->getId()
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
                        'file_code' => $this->getId(),
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

            //to move root folder , does not supported
            if(empty($destFldId)) $destFldId = 0;

            $resp = $this->app->call(
                'get',
                '/file/set_folder',
                [
                    'query' => [
                        'file_code' => $this->getId(),
                        'fld_id' => $destFldId
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