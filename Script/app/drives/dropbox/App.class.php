<?php

namespace CloudMonster\Drives\Dropbox;


use CloudMonster\Drives\BaseController;
use CloudMonster\Helpers\Help;
use CloudMonster\models\CloudDrives;
use Google\Exception;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\Exceptions\DropboxClientException;
use ReflectionException;

/**
 * Class App
 * @package CloudMonster\Drives\Dropbox
 */
class App extends BaseController{


    /**
     * Dropbox Service
     * @var Dropbox
     */
    protected \Kunnu\Dropbox\Dropbox $service;


    /**
     * App constructor.
     * @param CloudDrives $baseDrive
     */
    public function __construct(CloudDrives $baseDrive){
        parent::__construct($baseDrive);
    }


    /**
     * Connect to dropbox service
     * @throws Exception
     * @throws ReflectionException
     * @throws \Exception
     */
    public function connect()
    {
        //attempt to load parent controller
        $this->loadParent();

        $cardinals = $this->baseDrive->getAuthData();
        if(!empty($cardinals) && Help::isJson($cardinals)){

            $cardinals = Help::toArray($cardinals);
            $reflector = new \ReflectionClass('Kunnu\Dropbox\DropboxApp');
            $app = $reflector->newInstanceArgs(array_values($cardinals));
            $this->service = new Dropbox($app);
            $this->connected();

        }else{

            $this->addError('dropbox cardinals are missing');

        }

        if(!$this->isConnected)
            throw new \Exception('DriveId::['. $this->baseDrive->getID() .'] Unable to connect your dropbox account');

    }

    public function getAccountInfo(): array
    {

        $data = [];

        try{

            $account = $this->service->getCurrentAccount();
            $storage = $this->service->getSpaceUsage();

            $usedStorage = $storage['used'] ?? 0;
            $totalStorage = $storage['allocation']['allocated'] ?? 0;


            $data = [
                'accountId' => $account->getAccountId(),
                'accountType' => $account->getAccountType(),
                'email' => $account->getEmail(),
                'storage' => [
                    'limit' => Help::formatSizeUnits($totalStorage),
                    'used' => Help::formatSizeUnits($usedStorage)
                ]
            ];


        }catch(DropboxClientException $e){

            $this->addError($e->getMessage());

        }

        return $data;

    }




}