<?php

namespace CloudMonster\Drives\Dropbox;

use Kunnu\Dropbox\Exceptions\DropboxClientException;


/**
 * Class File
 * @package CloudMonster\Drives\Dropbox
 */

class Folder extends File{


    public function create(string $name): bool|string
    {
        try {


            $file = $this->app->service->createFolder('/' . $this->getLocation($name));

            return $file->getId();

        }catch(DropboxClientException $e) {

            $this->app->addError($e->getMessage());

        }

        return false;
    }





}