<?php

namespace CloudMonster\Drives\Dropapk;


use CloudMonster\Drives\BaseFile;


/**
 * Class File
 * @package CloudMonster\Drives\Dropapk
 */

class File extends BaseFile {

    /**
     * File not found error message
     * @var string
     */
    protected string $fileNotFoundError = 'Invalid file codes';

    /**
     * Get file
     */
    public function get() : array
    {
        try{
            return $this->app->call(
                'get',
                '/file/info',
                [
                    'query' => [
                        'file_code' => $this->getId()
                    ]
                ]
            );
        }catch (\Exception $e){
            $this->app->addError($e->getMessage());
        }
        return [];
    }



}