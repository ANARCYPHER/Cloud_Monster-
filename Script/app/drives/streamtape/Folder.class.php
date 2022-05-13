<?php

namespace CloudMonster\Drives\Streamtape;


use CloudMonster\Drives\BaseFile;
use Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Streamtape
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
                $query['pid'] = $parentFolderId;
            }

            $resp = $this->app->call(
                'get',
                '/file/createfolder',
                [
                    'query' => $query
                ]
            );

            if(isset($resp['folderid'])){
                return $resp['folderid'];
            }

        }catch(Exception $e){

            $this->app->addError($e->getMessage());

        }

        return false;


    }




}