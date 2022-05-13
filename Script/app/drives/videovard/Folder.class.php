<?php

namespace CloudMonster\Drives\Videovard;


use CloudMonster\Drives\BaseFile;
use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Videovard
 */

class Folder extends BaseFile {




    /**
     */
    public function create($name)
    {

        try{

            $parentFolderId = $this->getParentId();
            $query = ['name'=>$name];
            if(!empty($parentFolderId)) {
                $query['parent_id'] = $parentFolderId;
            }

            $resp = $this->app->call(
                'get',
                '/folder/create',
                [
                    'query' => $query
                ]
            );

            if(isset($resp['fld_id'])){
                return $resp['fld_id'];
            }

        }catch(Exception $e){

            $this->app->addError($e->getMessage());

        }

        return false;


    }

    /**
     * Rename file
     * @param $name
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

            $this->app->addError($e->getMessage());

        }

        return false;
    }


}