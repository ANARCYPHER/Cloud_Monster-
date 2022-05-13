<?php


namespace CloudMonster\Drives\Google;

use CloudMonster\Drives\BaseFile;
use Google\Service\Drive\DriveFile;
use Google_Service_Exception;


/**
 * Class File
 * @package CloudMonster\Drives\Google
 */

class File extends BaseFile {


    /**
     * Get file Info
     * @param string|array $fields
     * @return DriveFile|array
     */
    public function get(string|array $fields = "*"): DriveFile|array
    {
        try {

            $params = [
                'fields' => $fields,
                'supportsAllDrives' => true
            ];
            return $this->app->service->files->get($this->getId(), $params);

        } catch(Google_Service_Exception $e) {

            $this->app->addError($e->getErrors() [0]['message']);

        }

        return [];

    }

    /**
     * Rename file
     * @param string $newName
     * @return bool
     */
    public function rename(string $newName): bool
    {
        try {

            $file = new \Google_Service_Drive_DriveFile();
            $file->setName($newName);
            $resp = $this->app->service->files->update($this->getId(), $file);
            return true;

        }
        catch(Google_Service_Exception $e) {

            $this->app->addError($e->getErrors() [0]['message']);

        }
        return false;
    }

    /**
     * Delete file
     * @return bool
     */
    public function delete(): bool
    {
        try {
            $this->app->service->files->delete($this->getId());
            return true;
        }
        catch(Google_Service_Exception $e) {

            $this->app->addError($e->getErrors() [0]['message']);

        }
        return false;
    }

    /**
     * Make a copy of file
     * @param string $parentFolderId
     * @return bool|string
     */
    public function copy(string $parentFolderId = ''): bool|string
    {
        try {

            $params = !empty($parentFolderId) ? ['parents'=>[$parentFolderId]] : [];
            $file = new \Google_Service_Drive_DriveFile($params);
            $params = [ 'fields' => 'id' , 'supportsAllDrives' => true];
            $response = $this->app->service->files->copy($this->getId(), $file,$params);
            return $response->getId();

        }
        catch(Google_Service_Exception $e) {

            $this->app->addError($e->getErrors() [0]['message']);

        }
        return false;
    }

    public function move(): bool
    {

        try {

            $parentFolderId = $this->getParentId();

            if(empty($parentFolderId)) $parentFolderId = 'root';

            $file = new \Google_Service_Drive_DriveFile();
            $resp = $this->get(['fields'=>'parents']);
            $previousParents = join(',', $resp->parents);
            // Move the file to the new folder
            $file = $this->app->service->files->update($this->getId(), $file, [
                'addParents' => $parentFolderId,
                'removeParents' => $previousParents,
                'fields' => 'id, parents']);

            return true;

        }
        catch(Google_Service_Exception $e) {

            $this->app->addError($e->getErrors() [0]['message']);

        }

        return false;
    }

    public function check() : bool{

        $file = $this->get("trashed");

        if(!empty($file)){
            if($file->trashed){
                return false;
            }

        }else{
            $errorMsg = strtolower($this->app->getError());
            if(strpos($errorMsg, 'file not found') !== false){
                return false;
            }
        }
        return true;
    }



}