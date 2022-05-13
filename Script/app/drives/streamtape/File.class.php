<?php

namespace CloudMonster\Drives\Streamtape;


use CloudMonster\Drives\BaseFile;
use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Streamtape
 */

class File extends BaseFile {

    /**
     * File not found error message
     * @var string
     */
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
                '/file/info',
                [
                    'query' => [
                        'file' => $this->getId()
                    ]
                ]
            );

            if(!empty($resp)){
                $file = array_shift($resp);
                if($file['status'] == 200){
                    return $file ;
                }else{
                    $this->app->addError($this->fileNotFoundError);
                }

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

            if($resp == 1){

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

            return true;

        }catch(Exception $e){

            $this->app->addError($e->getMessage());

        }

        return false;

    }




}