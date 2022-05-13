<?php

namespace CloudMonster\Drives\Streamsb;


use CloudMonster\Drives\BaseFile;
use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Streamsb
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
                        'title' => $name
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
     * Move file
     * @return bool
     */
    public function move(): bool
    {
        try{

            $destFldId = $this->getParentId();

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