<?php

namespace CloudMonster\Drives\Onedrive;



use CloudMonster\Helpers\Help;
use Tsk\OneDrive\Services\OneDriveService;

/**
 * Class Auth
 * @package CloudMonster\Drives\Onedrive
 */

class Auth{

    /**
     * Auth cardinals
     * @var array
     */
    private array $cardinals = [];

    /**
     * Is everything is fine
     * @var bool
     */
    private bool $isOk = false;

    /**
     * Auth updated or not
     * @var bool
     */
    private bool $hasUpdated = false;


    /**
     * Auth constructor.
     * @param $cardinals
     * @param $client
     */
    public function __construct($cardinals, $client){

        if(Help::isJson($cardinals)){
            $this->cardinals = Help::toArray($cardinals);
            $this->setClient($client);
        }

    }


    public function createAuthUrl($client){
        return $client->createAuthUrl();
    }

    /**
     * Set client
     * @param $client
     */
    private function setClient($client){

        $clientId = $this->getConfig('client_id');
        $clientSecret = $this->getConfig('client_secret');
        $redirectUri = siteurl() . '/oauth/drive/onedrive';

        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri($redirectUri);
        $client->setScopes([
            OneDriveService::ONEDRIVE_OFFLINE_ACCESS,
            OneDriveService::ONEDRIVE_FILE_READ,
            OneDriveService::ONEDRIVE_FILE_READ_ALL,
            OneDriveService::ONEDRIVE_FILE_READ_WRITE,
            OneDriveService::ONEDRIVE_FILE_READ_WRITE_ALL
        ]);

    }


    /**
     * Set auth config
     * @param string $c
     * @param string $d
     */
    private function setConfig(string $c, string $d = '') : void{
        if(isset($this->cardinals[$c])){
            $this->cardinals[$c] = $d;
        }
    }

    /**
     * Get auth config
     * @param string $c
     * @return mixed
     */
    public function getConfig(string $c = ''): mixed
    {
        if(!empty($c)) {
            return $this->cardinals[$c] ?? '';
        }else{
            return $this->cardinals;
        }
    }

    /**
     * Check auth config has updated or not
     * @return bool
     */
    public function hasUpdated(): bool
    {
        return $this->hasUpdated;
    }

    /**
     * Check auth connected or not
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->isOk;
    }





}