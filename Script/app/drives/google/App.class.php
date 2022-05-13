<?php

namespace CloudMonster\Drives\Google;

use CloudMonster\Drives\BaseController;
use CloudMonster\Exception\DriveException;
use CloudMonster\Helpers\Help;
use CloudMonster\models\CloudDrives;
use Google\Exception;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Exception;


/**
 * Class App
 * @package CloudMonster\Drives\Google
 */
class App extends BaseController{


    /**
     * Google client
     * @var Google_Client
     */
    protected Google_Client $client;

    /**
     * Google drive service
     * @var Google_Service_Drive
     */
    protected Google_Service_Drive $service;





    /**
     * App constructor.
     * @param CloudDrives $baseDrive
     */
    public function __construct(CloudDrives $baseDrive){
        parent::__construct($baseDrive);
        $this->authErrorIdentity  = ['Invalid Credentials'];
    }


    /**
     * Attempt to connect to your google drive
     * @throws DriveException
     * @throws Exception
     */
    public function connect(){

        //attempt to load parent controller
        $this->loadParent();

        //attempt to authentication
        $this->auth = new Auth($this->baseDrive->getAuthData());

        try{

            $this->auth->authenticate();
            $this->connected();
            $this->init();

        }catch(DriveException $e){

            throw $e;

        } finally {

            //save updated cardinals
            if($this->auth->hasUpdated()){
                $this->baseDrive->assign([
                    'authData' => Help::toJson($this->auth->getConfig())
                ])->save();
            }

        }

        if(!$this->isConnected)
            throw new DriveException('DriveId::[#' . $this->baseDrive->getID() . '] Unable to connect your google drive account');

    }

    public function getAccountInfo(): array
    {

        try {

            $params = [
                'fields' => ["user, storageQuota"]
            ];

            $about = $this->service->about->get($params);

            $user = $about->getUser();
            $storage = $about->getStorageQuota();


            return [
                'displayName' => $user->displayName,
                'emailAddress' => $user->emailAddress,
                'permissionId' => $user->permissionId,
                'storage' => [
                    'limit' => Help::formatSizeUnits($storage->limit),
                    'usage' => Help::formatSizeUnits($storage->usage),
                    'usageInDrive' => Help::formatSizeUnits($storage->usageInDrive),
                    'usageInDriveTrash' => Help::formatSizeUnits($storage->usageInDriveTrash)
                ]
            ];

        } catch(Google_Service_Exception $e) {

            $this->addError($e->getErrors() [0]['message']);

        }

        return [];

    }

    /**
     * init drive app
     */
    private function init(){
        //init client
        $this->client = new Google_Client();
        $this->client->setAccessToken($this->auth->getAccessToken());
        //init client service
        $this->service = new Google_Service_Drive($this->client);
    }









}