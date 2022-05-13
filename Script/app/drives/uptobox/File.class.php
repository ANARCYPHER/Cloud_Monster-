<?php

namespace CloudMonster\Drives\Uptobox;


use CloudMonster\Drives\BaseFile;
use CloudMonster\Helpers\Logger;


/**
 * Class File
 * @package CloudMonster\Drives\Uptobox
 */

class File extends BaseFile {

    /**
     * file not found error
     * @var string
     */
    protected string $fileNotFoundError = 'File not found';

    /**
     * Get file
     * @throws \Exception
     */
    public function get() : array
    {
        $resp = $this->app->call(
            'get',
            '/link/info',
            [
                'query' => [
                    'fileCodes' => $this->getId()
                ]
            ]
        );

        if(!empty($resp['list'][0])){
            $resp =  $resp['list'][0];

            if(isset($resp['error']['message'])){
                $this->app->addError($resp['error']['message']);
            }else{
                return $resp;
            }

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

            $destFldId = $this->getParentId();

            $resp = $this->app->call(
                'patch',
                '/user/files',
                [
                    'query' => [
                        'file_code' => $this->getId(),
                        'new_name' => $name
                    ]
                ]
            );

            return true;

        }catch(\Exception $e){

            Logger::debug('UpToBoxFile: ' . $e->getMessage());

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
                        'file_codes' => $this->getId(),
                    ]
                ]
            );

            return true;

        }catch(\Exception $e){

            Logger::debug('UpToBoxFile: ' . $e->getMessage());

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

            if(empty($destFldId)) $destFldId = 'root';

            $resp = $this->app->call(
                'patch',
                '/user/files',
                [
                    'query' => [
                        'file_codes' => $this->getId(),
                        'destination_fld_id' => $destFldId,
                        'action' => 'move'
                    ]
                ]
            );

            return true;

        }catch(\Exception $e){

            Logger::debug('UpToBoxFile: ' . $e->getMessage());

        }

        return false;

    }




}