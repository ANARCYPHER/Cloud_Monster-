<?php

namespace CloudMonster\Helpers;


/**
 * Class VisitorInfo
 * @package CloudMonster\Helpers
 */
class VisitorInfo {

    /**
     * Visitor IP Address
     * @var string
     */
    private string $ipAddress;

    /**
     * Geo plugin data
     * @var array
     */
    private array $geoData;


    /**
     * VisitorInfo constructor.
     */
    public function __construct(){



    }

    /**
     * init visitor info
     * @return bool
     */
    public function init(): bool
    {

        if($this->loadUserIp()){
            return $this->loadGeoData();
        }

        return false;

    }

    /**
     * Get user ip address
     * @return string
     */
    public function getIp(): string
    {
        return $this->ipAddress;
    }

    /**
     * get user country code
     * @return string
     */
    public function getCountryCode() : string{
       return $this->getGeoData('country');
    }


    /**
     * Get user timezone
     * @return string
     */
    public function getTimezone() : string{
        return $this->getGeoData('timezone');
    }


    /**
     * Get geo plugin data
     * @param string $field
     * @return string
     */
    private function getGeoData(string $field) : string{
        return $this->geoData[$field] ?? '';
    }

    /**
     * Load geo plugin data
     * @return bool
     */
    private function loadGeoData(): bool
    {

        $response = @file_get_contents("http://ipinfo.io/" . $this->ipAddress);

        if(!empty($response) && Help::isJson($response)){

            $ipData = Help::toArray($response);
            $this->geoData = $ipData;
            return true;

        }

        return false;

    }

    /**
     * Load user IP
     * @return bool
     */
    private function loadUserIp(): bool
    {

        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {

            $ip = $_SERVER["HTTP_CLIENT_IP"];

        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {

            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];

        } else {

            $ip = $_SERVER["REMOTE_ADDR"];

        }


         if(Help::isValidIp($ip)){
             $this->ipAddress = $ip;
         }

        return !empty($this->ipAddress);

    }



}