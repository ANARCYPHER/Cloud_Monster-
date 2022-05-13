<?php

namespace CloudMonster\Drives\Onedrive;


use CloudMonster\Helpers\Help;
use CloudMonster\Drives\BaseController;
use CloudMonster\Exception\DriveException;
use CloudMonster\models\CloudDrives;
use Google\Exception;
use GuzzleHttp\Exception\ClientException;
use Tsk\OneDrive\Client;
use Tsk\OneDrive\Services\OneDriveService;


/**
 * Class App
 * @package CloudMonster\Drives\Onedrive
 */
class App extends BaseController{

    /**
     * One drive client
     * @var Client
     */
    protected Client $client;
    protected OneDriveService $service;


    /**
     * App constructor.
     * @param CloudDrives $baseDrive
     */
    public function __construct(CloudDrives $baseDrive){
        parent::__construct($baseDrive);
        $this->authErrorIdentity = ['invalid_grant', 'invalid_client'];

        $this->client = new Client();
        $this->auth = new Auth($baseDrive->getAuthData(), $this->client);
    }


    /**
     * Attempt to connect onedrive
     * @throws DriveException
     * @throws Exception
     */
    public function connect()
    {
        //attempt to load parent controller
        $this->loadParent();

        $this->client->setAccessToken($this->auth->getConfig());
        $this->service = new OneDriveService($this->client);
        $this->connected();

    }

    public function getAccountInfo(): array
    {
        $data = [];

        try{

            $account = $this->service->about->get();

            $owner = $account->getOwner();
            $displayName = $owner['user']['displayName'] ?? '';
            $userId = $owner['user']['id'] ?? '';

            $quota = $account->getQuota();

            $data = [
                'displayName' => $displayName,
                'userId' => $userId,
                'driveType' => $account->getDriveType(),
                'storage' => [
                    'total' => Help::formatSizeUnits($quota->getTotal()),
                    'used' => Help::formatSizeUnits($quota->getTotal() - $quota->getRemaining()),
                    'deleted' => Help::formatSizeUnits($quota->getDeleted())
                ]
            ];



        }catch(ClientException | \Exception $e){
            $this->addError($this->decodeError($e));
        }

        return $data;
    }

    /**
     * Get auth url
     * @return mixed
     */
    public function getAuthUrl(): mixed
    {
        $result = $this->auth->createAuthUrl($this->client);
        return $result::composeComponents(
            $result->getScheme(),
            $result->getAuthority(),
            $result->getPath(),
            $result->getQuery(),
            $result->getfragment()
        );
    }

    /**
     * Verify app login
     * @param $code
     * @return bool
     */
    public function verifyLogin($code): bool
    {
        try{
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            $clientId = $this->auth->getConfig('client_id');
            $clientSecret = $this->auth->getConfig('client_secret');
            $clientData = [
                'client_id' => $clientId,
                'client_secret' => $clientSecret
            ];
            $token = array_merge($token, $clientData);
            $this->baseDrive->assign([
                'authData' => Help::toJson($token)
            ])->save();

            return true;
        }catch(ClientException $e){

            $this->addError($this->decodeError($e));

        }

        return false;

    }

    public function isAuthenticated(): bool
    {
        if(empty($this->auth->getConfig('access_token'))){
            return false;
        }
        return true;
    }

    /**
     * Decode response error msg
     * @param $e
     * @param bool $onlyMsg
     * @return mixed
     */
    public function decodeError($e, bool $onlyMsg = true): mixed
    {

        $response = $e->getResponse();
        $resp = $response->getBody()->getContents();
        if(Help::isJson($resp)){
            $resp = Help::toArray($resp);
            if($onlyMsg){
                if(isset($resp['error']['message'])){
                    return $resp['error']['message'];
                }else if(isset($resp['error']) && is_string($resp['error'])){
                    return $resp['error'];
                }
            }else{
                return $resp;
            }

        }


        return 'Onedrive: Unknown Error';
    }



}