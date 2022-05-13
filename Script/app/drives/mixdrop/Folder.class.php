<?php

namespace CloudMonster\Drives\Mixdrop;


use CloudMonster\Drives\BaseFile;
use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Mixdrop
 */

class Folder extends BaseFile {




    /**
     */
    public function create($name)
    {

        try{

            $parentFolderId = $this->getParentId();
            $query = ['title'=>$name];
            if(!empty($parentFolderId)) {
                $query['parent'] = $parentFolderId;
            }

            $resp = $this->app->call(
                'get',
                '/foldercreate',
                [
                    'query' => $query
                ]
            );

            if(isset($resp['id'])){
                return $resp['id'];
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
                '/folderrename',
                [
                    'query' => [
                        'id' => $this->getId(),
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


}