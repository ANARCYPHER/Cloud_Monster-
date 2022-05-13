<?php

namespace CloudMonster\Drives\Google;


use CloudMonster\Core\CURL;
use CloudMonster\Helpers\Help;
use CloudMonster\Helpers\Logger;
use CloudMonster\Exception\DriveException;



/**
 * Class Auth
 * @package CloudMonster\Drives\Google
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
     * Auth error
     * @var string
     */
    private string $error = '';

    /**
     * Auth updated or not
     * @var bool
     */
    private bool $hasUpdated = false;


    /**
     * Auth constructor.
     * @param $cardinals
     */
    public function __construct($cardinals){

        if(Help::isJson($cardinals)){
            $this->cardinals = Help::toArray($cardinals);
        }

    }

    /**
     * Authenticate
     * @throws \Exception
     */
    public function authenticate(){
        if(!empty($this->cardinals)){
            $this->init();
        }else{
            Logger::debug('GAuth: authentication cardinals are missing');
        }
        if(!$this->isOk){
            throw new DriveException('GAuth: Token authentication failed');
        }
    }

    /**
     * Initialize authentication
     */
    private function init() : void
    {
        if(!$this->isTokenValid()){
            if($this->refreshToken()){
                $this->isOk = true;
            }
        }else{
            $this->isOk = true;
        }
    }

    /**
     * Check access token validation
     * @return bool
     */
    private function isTokenValid(): bool
    {
        $accessToken = $this->getConfig('access_token');
        $lastUpdated = $this->getConfig("token_last_updated");
        if(!empty($accessToken) && !empty($lastUpdated)){
            $timeFirst = strtotime($lastUpdated);
            $timeSecond = strtotime(Help::timeNow());
            $differenceInSeconds = $timeSecond - $timeFirst;
            if ($differenceInSeconds < 3500 && $differenceInSeconds > 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Attempt to refresh access token
     * @return bool
     */
    private function refreshToken(): bool
    {
        $success = false;
        $this->hasUpdated = true;
        $authData = [
            "client_id" => $this->getConfig("client_id"),
            "client_secret" => $this->getConfig("client_secret"),
            "refresh_token" => $this->getConfig("refresh_token"),
            "grant_type" => "refresh_token",
        ];
        $curl = new CURL;
        $curl->post('https://www.googleapis.com/oauth2/v4/token', $authData)->exec();
        if($curl->isOk()){
            $results = $curl->getResults();
            if(!empty($results)){
                if(!isset($results['error'])){
                    if(isset($results['access_token'])){
                        $this->setConfig('access_token', $results['access_token']);
                        $this->setConfig('token_last_updated', Help::timeNow());
                        $success = true;
                        Logger::info('GAuth: Access token refreshed successfully');
                    }
                }else{
                    Logger::debug($results['error']);
                }
            }else{
                Logger::debug('GAuth: empty response');
            }
        }else{
            Logger::debug($curl->getError());
        }
        $curl->close();

        if(!$success) {
            $this->setConfig('access_token', '');
        }

        return $success;

    }

    /**
     * Get access token
     * @return string
     */
    public function getAccessToken() : string{
        return $this->getConfig('access_token');
    }

    /**
     * Set auth config
     * @param $c
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

    /**
     * Add error
     * @param string $e
     */
    private function addError(string $e) : void{
        $this->error = $e;
    }

    /**
     * Get error
     * @return string
     */
    public function getError(): string
    {
        return !empty($this->error) ? $this->error : 'google drive authentication: unknown error';
    }



}