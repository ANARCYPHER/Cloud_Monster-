<?php

/*
   +------------------------------------------------------------------------+
   | CloudMonster - Handle multi-Cloud Storage in parallel
   | Copyright (c) 2021 PHPCloudMonster. All rights reserved.
   +------------------------------------------------------------------------+
   | @author John Antonio
   | @author_url 1: https://phpcloudmonster.com
   | @author_url 2: https://www.codester.com/johnanta
   | @author_email: johnanta89@gmail.com
   +------------------------------------------------------------------------+
*/

namespace CloudMonster\Drives;



use CloudMonster\Models\CloudDrives;
use CloudMonster\Helpers\Logger;
use Google\Exception;
use ReflectionException;


/**
 * Class BaseController
 * @author John Antonio
 * @package CloudMonster\Drives
 */
class BaseController{

    /**
     * Authentication instance
     * @var
     */
    protected $auth;

    /**
     * Controller Error
     * @var string
     */
    protected string $error = '';

    /**
     * Base cloud drive instance
     * @var CloudDrives
     */
    protected $baseDrive;

    /**
     * Target cloud app child class name
     * @var string
     */
    protected string $childClassName = '';

    /**
     * Check drive app is connected or not
     * @var bool
     */
    protected bool $isConnected = false;

    /**
     * check base drive is ready or not
     * @var bool
     */
    protected bool $isBaseDriveReady = false;

    /**
     * Check base drive identity verified or not
     * @var bool
     */
    protected bool $identityVerified = false;

    /**
     * Check controller loaded or not
     * @var bool
     */
    protected bool $isLoaded = false;

    /**
     * Identity authentication error
     * @var array
     */
    protected array $authErrorIdentity = [];


    /**
     * BaseController constructor.
     * @param CloudDrives $baseDrive
     */
    public function __construct(CloudDrives $baseDrive){
        $this->baseDrive = $baseDrive;
        if($baseDrive->isLoaded()){
            $this->isBaseDriveReady = true;
            $this->setChildClassName();
        }else{
            $this->addError('base drive init failed');
        }
    }

    /**
     * Load parent cloud app
     * @throws Exception
     */
    protected function loadParent(){

        if(!$this->isLoaded){
            $success = false;

            //check base drive
            if($this->baseDrive->isLoaded()){
                $this->isBaseDriveReady = true;
                if($this->setChildClassName()){
                    if($this->verifyDriveIdentity()){
                        $success = true;
                    }else{
                        Logger::debug('Drive identity verification failed - ChildApp:: ' . $this->childClassName);
                    }
                }
            }else{
                Logger::debug('Base drive not loaded');
            }

            //parent loaded. (only onetime callable)
            $this->isLoaded = true;

            if(!$success){
                throw new Exception('DRIVES: Base Controller not loaded');
            }

        }else{
            throw new Exception('DRIVES: Base Controller already loaded');
        }

    }

    /**
     * Set child class name for load cloud app
     * @return bool success or failure
     */
    private function setChildClassName(): bool
    {
        $success = false;
        $class = get_class($this);
        $dirList = explode('\\',$class);
        if(count($dirList) === 4){
            $childClass = $dirList[2];
            $tmpClassName = __NAMESPACE__."\\{$childClass}\\App";
            if(class_exists($tmpClassName)){
                $this->childClassName = $childClass;
                $success = true;
            }else{
                Logger::debug("DRIVES: required drive class does not exist : {$tmpClassName}");
            }
        }else{
            Logger::debug("DRIVES: base controller unable to load child class");
        }
        return $success;
    }

    /**
     * Verify base drive identity
     * @return bool success or failure
     */
    private function verifyDriveIdentity(): bool
    {
        if(strtolower($this->baseDrive->getType()) == strtolower($this->childClassName)){
            $this->identityVerified = true;
        }
        return $this->identityVerified;
    }

    public function upload(){

    }

    /**
     * Initialize target child class
     * @throws ReflectionException
     */
    protected function initSubClass($class, array $args = []){

        $className = $this->getChildClass(ucwords($class));
        if(class_exists($className)){
            $reflection = new \ReflectionClass($className);
            array_unshift($args, $this);
            $this->{$class} = $reflection->newInstanceArgs($args);
        }else{
            die("Unable to init drive FILE class: " . $className);
        }

    }

    /**
     * Get target cloud drive's account info
     */
    public function getAccountInfo(){

    }

    /**
     * Add error
     * @param $e
     */
    public function addError($e){
        $this->error = $e;
        Logger::warn(
            $e,
            debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1)
        );
        if($this->isAuthError()){
            $this->authFailed();
        }
    }

    /**
     * Get error
     * @return string
     */
    public function getError(): string
    {
        return !empty($this->error) ? $this->error : 'NOP:: Unknown error occurred - (Check your logger for more details)';
    }

    /**
     * Check has auth error or not
     * @return bool
     */
    protected function isAuthError(): bool
    {
        $hasError = false;
        if(!empty($this->authErrorIdentity)){
            foreach ($this->authErrorIdentity as $val){
                if($val === $this->error){
                    $hasError = true;
                    break;
                }
            }
        }
        return $hasError;
    }

    /**
     * Get child class
     * @param $class
     * @return string
     */
    protected function getChildClass($class): string
    {
        return __NAMESPACE__ . "\\{$this->childClassName}\\" . $class;
    }

    /**
     * Initialize cloud app file
     * @param string $fileId
     * @throws ReflectionException
     */
    public function initFile($fileId = ''){
        $this->initSubClass('file', [$fileId]);
    }

    /**
     * Initialize cloud app folder
     * @param string $folderId
     * @throws ReflectionException
     */
    public function initFolder($folderId = ''){
        $this->initSubClass('folder', [$folderId]);
    }

    /**
     * Initialize cloud app uploader
     * @param $uniqId
     * @param $fileId
     * @throws ReflectionException
     */
    public function initUpload($uniqId, $fileId){
        $this->initSubClass('upload', [$uniqId,$fileId]);
    }

    /**
     * Initialize cloud app downloader
     * @param $bucket
     * @throws ReflectionException
     */
    public function initDownload($bucket){
        $this->initSubClass('download', [$bucket]);
    }

    /**
     * Check auth is failed or not
     */
    protected function authFailed(){
        $this->baseDrive->error();
    }

    /**
     * Check drive connection
     */
    protected function connected(){
        $this->isConnected = true;
    }

    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    /**
     * Connect to app
     * @throws \Exception
     */
    public function connect(){
        throw new \Exception('Invalid connection request');
    }

    public function isActive(): bool
    {
        $this->getAccountInfo();
        if($this->isAuthError()){
            return false;
        }
        return true;
    }

    public function __get($name){

        if(property_exists($this, $name)){
            return  $this->$name;
        }
        die($name . ':: Property does not exist.');
    }

    public function __call($method, $args)
    {
        if(isset($this->ObjService) && is_object($this->ObjService)){
            return  $this->ObjService->$method($args);
        }

        die($method . ':: Method does not exist');
    }

}