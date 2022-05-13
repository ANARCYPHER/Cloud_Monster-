<?php

namespace CloudMonster\Drives\Dropapk;


use CloudMonster\Drives\BaseFile;
use CloudMonster\Helpers\Logger;
use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Uptobox
 */

class Folder extends BaseFile {

    /**
     */
    public function create($name)
    {


        try{

            $parentId = $this->getParentId();

            $resp = $this->app->call(
                'get',
                '/folder/create',
                [
                    'query' => [
                        'name' => $name,
                        'parent_id' => $parentId
                    ]
                ]
            );

            if(isset($resp['fld_id'])){
                return $resp['fld_id'];
            }

        }catch(Exception $e){

            Logger::debug('UpToBoxFile: ' . $e->getMessage());

        }

        return false;
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
     * @return bool
     */
    public function rename($name): bool
    {
        try{
            $resp = $this->app->call(
                'get',
                '/folder/rename',
                [
                    'query' => [
                        'fld_id' => $this->getId(),
                        'name' => $name
                    ]
                ]
            );

          return true;

        }catch(Exception $e){

            Logger::debug('UpToBoxFile: ' . $e->getMessage());

        }

        return '';
    }

    /**
     * Delete file
     * @return bool
     */
    public function delete(): bool
    {
       //not available
        return false;
    }


    /**
     * Move file
     * @param string $destFldId
     * @return bool
     */
    public function move(): bool
    {
       //unavailable
        return false;
    }


}