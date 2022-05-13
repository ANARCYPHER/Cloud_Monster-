<?php


namespace CloudMonster\Drives\Google;


use Google_Service_Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Google
 */

class Folder extends File {

    public function create($name): bool | string
    {
        try {
            $parentFolderId = $this->getParentId();
            $params = !empty($parentFolderId) ? ['parents'=>[$parentFolderId]] : [];
            $file = new \Google_Service_Drive_DriveFile($params);
            $file->setName($name);
            $file->setMimeType('application/vnd.google-apps.folder');
            $resp = $this->app->service->files->create($file);
            return $resp->getId();
        }
        catch(Google_Service_Exception $e) {

            $this->app->addError($e->getErrors() [0]['message']);

        }

        return false;
    }



}