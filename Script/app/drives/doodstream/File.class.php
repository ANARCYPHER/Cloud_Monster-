<?php

namespace CloudMonster\Drives\Doodstream;


use CloudMonster\Drives\BaseFile;
use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Uptobox
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

            if(isset($resp[0])){
                return $resp[0];
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







}