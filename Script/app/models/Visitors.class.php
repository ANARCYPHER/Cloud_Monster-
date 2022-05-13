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

namespace CloudMonster\Models;

use CloudMonster\App;
use CloudMonster\Core\Model;
use CloudMonster\Helpers\Help;
use CloudMonster\Helpers\VisitorInfo;


/**
 * Class Visitors
 * @author John Antonio
 * @package CloudMonster\Models
 */
class Visitors extends Model
{

    /**
     * Database table
     * @var string table name
     */
    protected static string $tmpTbl = "visitors";


    /**
     * CloudDrives constructor.
     */
    public function __construct()
    {
        parent::__construct($this::$tmpTbl);
    }

    /**
     * Get visit by the relevant file
     * @param int $fileId target file ID
     * @param string $ip IP address
     * @return array
     */
    public function getVisit(int $fileId, string $ip) : array{
        $filters = [
            "fileId" => $fileId,
            "ip" => $ip,
            "status" => "",
        ];

        $order = [
            "created_at" => "DESC",
        ];

        return $this->getOne($filters, $order);

    }

    /**
     * Consider whether it is a new or old visit
     * @param $visitTime
     * @return bool
     */
    public function isNewVisit($visitTime): bool
    {

        if(!empty($visitTime)){

            //validate
            $expiredTime = $visitTime;
            $expiredHours = "24";
            $expired = strtotime($expiredTime . "+{$expiredHours}hours");
            if ($expired > strtotime(Help::timeNow())) {

                return false;

            }

        }

        return true;


    }


    /**
     * Add visit for the relevant file
     * @param Files $file
     */
    public function addVisit(Files $file){

        //get visitor info
        $visitorInfo = new VisitorInfo();

        $isRequestBlocked = false;

        //attempt to init visitor info
        if($visitorInfo->init() && !empty($visitorInfo->getCountryCode())){

            $visit = $this->getVisit($file->getID(), $visitorInfo->getIp());
            $visitTime = $visit['createdAt'] ??  '';
            $countryCode = strtolower($visitorInfo->getCountryCode());
            $ip = $visitorInfo->getIp();

            //check ip is blacklisted
            if($this::isBlacklistedIp($ip)){
                $isRequestBlocked = true;
            }

            //check country is blacklisted
            if($this::isBlacklistedCountry($countryCode)){
                $isRequestBlocked = true;
            }

            if(!$isRequestBlocked){

                //check is new visit or not
                if($this->isNewVisit($visitTime)){

                    $data = [
                            'fileId' => $file->getID(),
                            'ip' => $ip,
                            'countryCode' => strtolower($countryCode)
                        ];

                    $this->assign($data)->save();

                }else{

                    //increment exist visit
                    if($this->load($visit['id'])){
                        $this->assign(['visit'=>$visit['visit']+1])->save();
                    }

                }
                $this->clean();
            }

        }else{

            if($this::isVisitorInfoRequired()){
                $isRequestBlocked = true;
            }

        }

        if($isRequestBlocked){
            $this->requestBlocked();
            die('Blacklisted');
        }


    }

    /**
     * Visits request blocked
     */
    private function requestBlocked(){
        $blockedRequests =  (int) App::getConfig('blocked_requests');
        $blockedRequests += 1;
        $this->db->where("config", 'blocked_requests');
        $this->db->update('settings', ["var" => $blockedRequests], 1);
    }

    /**
     * Delete visits by file ID
     * @param int $fileId
     * @return bool
     */
    public function delByFileId(int $fileId): bool
    {
        $this->db->where('fileId', $fileId);
        return $this->db->delete($this->tbl);
    }

    /**
     * Check blacklisted countries
     * @param $cc
     * @return bool
     */
    public static function isBlacklistedCountry($cc): bool
    {
        $blacklistedCountries = App::getConfig('blacklisted_countries');
        if(!empty($blacklistedCountries) && is_array($blacklistedCountries)){
            if(in_array($cc, $blacklistedCountries)){
                return true;
            }
        }
        return false;
    }

    /**
     * Check blacklisted ips
     * @param $ip
     * @return bool
     */
    public static function isBlacklistedIp($ip): bool
    {
        $blacklistedIps = App::getConfig('blacklisted_ips');
        if(!empty($blacklistedIps) && is_array($blacklistedIps)){
            if(in_array($ip, $blacklistedIps)){
                return true;
            }
        }
        return false;
    }

    /**
     * Check visitor info is required or not
     * @return bool
     */
    public static function isVisitorInfoRequired(): bool
    {
        return App::getConfig('is_visit_info_required') == 1;
    }









}