<?php


namespace CloudMonster\Drives\Onedrive;

use CloudMonster\Drives\BaseFile;
use Exception;
use GuzzleHttp\Exception\ClientException;



/**
 * Class File
 * @package CloudMonster\Drives\Onedrive
 */

class File extends BaseFile {

    /**
     * File not found error message
     * @var string
     */
    protected string $fileNotFoundError = 'Item does not exist';


    /**
     * @throws ClientException
     */
    public function get(){
        try{

            return $this->app->service->items->get($this->getId());

        }catch(ClientException $e){

            $this->app->addError($this->app->decodeError($e));

        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function create($name){
        try{
            $folderId = !empty($this->getParentId()) ? $this->getParentId() : null;
            $resp = $this->app->service->items->createFolder($name, $folderId);
            return $resp->getId();
        }catch (Exception $e){
            $this->app->addError($this->app->decodeError($e));
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function delete() : bool{

        try{
            $this->app->service->items->delete($this->getId());
            return true;
        }catch (ClientException $e){
            $this->app->addError($this->app->decodeError($e));
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function move() : bool{

        try{
            $parentId = $this->getParentId();
            if(empty($parentId)) $parentId = 'root';
            $this->app->service->items->move($this->getId(), $parentId);
            return true;
        }catch (ClientException $e){
            $this->app->addError($this->app->decodeError($e));
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function rename($name) : bool{
        try{
            $this->app->service->items->update($this->getId(), ['name'=>$name]);
            return true;
        }catch (ClientException $e){
            $this->app->addError($this->app->decodeError($e));
        }

        return false;
    }




}