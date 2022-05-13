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

namespace CloudMonster\Services;


use CloudMonster\App;
use CloudMonster\Core\Database;
use CloudMonster\Models\CloudDrives;
use CloudMonster\Models\Files;
use CloudMonster\Models\ProcessTracker;
use CloudMonster\Models\Visitors;
use CloudMonster\Helpers\Help;


/**
 * Class Analytics
 * @author John Antonio
 * @package CloudMonster\Services
 */
class Analytics{


    /**
     * Application Database
     * @var Database database instance;
     */
    protected Database $db;

    /**
     * Current Date
     * @var string current date
     */
    protected string $dateTime = '';


    /**
     * Analytics constructor.
     */
    public function __construct(){

        $this->db = Database::getInstance();
        $this->dateTime = Help::timeNow('Y-m-d');

    }

    /**
     * Get visitors (group by country code)
     * @param string $startDate (optional) target start date for get results
     * @param string $endDate (optional) target end date for get results
     * @param bool $isUnique (optional) about total or unique visits
     * @param int $fileId (optional) target file ID
     * @return array
     */
    public function getVisitorsByCountry(string $startDate = '', string $endDate = '',bool $isUnique = false, int $fileId = 0): array
    {

        $results = [];

        //The order in which the results should be processed
        $orderBy = !$isUnique ? 'totalVisits' : 'uniqVisits';

        if(!empty($startDate) && !empty($endDate)){

            $startDate = Help::formatDT($startDate, 'Y-m-d');
            $endDate = Help::formatDT($endDate, 'Y-m-d');

            if($startDate !== $endDate){

                $this->db->where('DATE_FORMAT(createdAt, "%Y-%m-%d")', [$startDate,$endDate], 'BETWEEN');

            }else{

                $this->db->where('DATE_FORMAT(createdAt, "%Y-%m-%d")', $startDate);

            }

        }else{

            $this->db->where('DATE_FORMAT(createdAt, "%Y-%m")', Help::formatDT($this->dateTime, "Y-m"));

        }

        if(!empty($fileId)){
            //filter by file ID
            $this->db->where('fileId', $fileId);
        }

        $this->db->where('countryCode', '', '!=');
        $this->db->orderBy($orderBy);
        $this->db->groupBy('countryCode');

        //attempt to get results
        $data = $this->db->get(
            Visitors::getTbl(),
            null,
            'countryCode,  sum(visit) as totalVisits, count(id) as uniqVisits'
        );

        if(!empty($data)){

            foreach ($data as $val){

                $visits = !$isUnique ? $val['totalVisits'] : $val['uniqVisits'];
                $results[$val['countryCode']] = $visits;

            }

        }

        return $results;

    }


    /**
     * Get visitors by month (group by date)
     * @param int $month
     * @param int $year
     * @param int $fileId
     * @return array
     */
    public function getVisitorsByMonth(int $month = 0, int $year = 0, int $fileId = 0) : array{

        $results = [];

        if(empty($month)) $month = Help::formatDT($this->dateTime, "m");
        if(empty($year)) $year = Help::formatDT($this->dateTime, "Y");

        $filterDate = "$year-$month";

        if(!empty($fileId))
            $this->db->where('fileId', $fileId);


        $this->db->where('DATE_FORMAT(createdAt, "%Y-%m")', $filterDate);
        $this->db->groupBy('DAY(createdAt)');
        $data = $this->db->get(Visitors::getTbl(), null, 'DATE(createdAt) as date, sum(visit) as totalVisits, count(id) as uniqVisits');

        if($this->db->count > 0){

            $lastDay = date("t", strtotime($data[0]['date']));
            $tmpData = [
                'date' => '',
                'visits' => 0,
                'uniqVisits' => 0
            ];

            //init temporary data
            for($i = 1; $i <=  $lastDay; $i++)
            {
                $day = str_pad($i, 2, '0', STR_PAD_LEFT);
                $tmpData['date'] = $year . "-" . $month . "-" . $day;
                $results[$day] = $tmpData;
            }

            foreach ($data as $val){

                $cDay = date("d", strtotime($val['date']));

                if(array_key_exists($cDay, $results)){
                    $results[$cDay]['visits'] = $val['totalVisits'];
                    $results[$cDay]['uniqVisits'] = $val['uniqVisits'];
                }

            }

            return $results;

        }

        return [];

    }

    /**
     * Analytics buckets info
     * @return array
     */
    public function getBucketsInfo() : array{

        $tbl = \CloudMonster\Models\Buckets::getTbl();

        //get total buckets
        $totalBuckets = $this->db->getValue($tbl, "count(*)");

        //get today buckets
        $this->db->where('DATE_FORMAT(createdAt, "%Y-%m-%d")', $this->dateTime);
        $todayBuckets = $this->db->getValue($tbl, "count(*)");

        //get total buckets size
        $totalBucketsSize = (int) $this->db->getValue($tbl, "sum(size)");

        //get today bucket size
        $this->db->where('DATE_FORMAT(createdAt, "%Y-%m-%d")', $this->dateTime);
        $todayBucketsSize = (int) $this->db->getValue($tbl, "sum(size)");

        //get broken buckets
        $this->db->join(Files::getTbl(). ' f', 'b.id = f.bucketId', 'LEFT' );
        $this->db->where('f.pstatus', Files::INACTIVE);
        $this->db->groupBy('b.id');
        $this->db->withTotalCount()->get($tbl . ' b',null, "b.id");
        $brokenBuckets = $this->db->totalCount;


        $this->db->join(Files::getTbl(). ' f', 'f.bucketId = b.id', 'LEFT' );
        $this->db->join(Visitors::getTbl(). ' v', 'v.fileId = f.id', 'RIGHT' );
        $this->db->orderBy('sum(v.visit)');
        $this->db->groupBy('b.id');
        $mostVisitedBuckets= $this->db->get($tbl . ' b',10,"
            b.* , sum(v.visit) as totalVisits, count(v.visit) as uniqVisits,
            count(f.id) as files
        ");


        $errorRate = 0;
        if($totalBuckets > 0){

            $errorRate = round($brokenBuckets / $totalBuckets * 100);

        }


        return [
            'total' => [
                'count' => $totalBuckets,
                'size' => $totalBucketsSize
            ],
            'today' => [
                'count' => $todayBuckets,
                'size' => $todayBucketsSize
            ],
            'broken' => $brokenBuckets,
            'errorRate' => $errorRate,
            'mostVisited' => $mostVisitedBuckets
        ];


    }

    /**
     * Analytics visits info
     * @return array
     */
    public function getVisitsInfo() : array{

        $tbl = Visitors::getTbl();

        //get total visits
        $totalVisits = (int) $this->db->getValue($tbl, "sum(visit)");

        //get today visits
        $this->db->where('DATE_FORMAT(createdAt, "%Y-%m-%d")', $this->dateTime);
        $todayVisits = (int) $this->db->getValue($tbl, "sum(visit)");

        //get unique visits
        $uniqVisits = $this->db->getValue($tbl, "count(*)");

        //get unique visits
        $this->db->where('DATE_FORMAT(createdAt, "%Y-%m-%d")', $this->dateTime);
        $todayUniqVisits =  $this->db->getValue($tbl, "count(visit)");


        return [
            'total' => [
                'count' => $totalVisits,
                'today' => $todayVisits
            ],
            'unique' => [
                'count' => $uniqVisits,
                'today' => $todayUniqVisits
            ]
        ];


    }

    /**
     * Get all visitors (group by country code)
     * @param int $limit (optional) results limit
     * @param int $fileId (optional) target file ID
     * @return array
     */
    public function getAllVisitors(int $limit = 0, int $fileId = 0) : array{

        $this->db->where('countryCode', '', '!=');
        $this->db->orderBy('sum(visit)');
        $this->db->groupBy('countryCode');
        if(empty($limit)) $limit = null;
        if(!empty($fileId))
            $this->db->where('fileId', $fileId);

        $data = $this->db->get(Visitors::getTbl(), $limit, 'countryCode,  sum(visit) as totalVisits, count(id) as uniqVisits');

        if(!empty($data)){

            foreach ($data as $key => $val){

                $countryName = Help::getCountryByCode($val['countryCode']);
                $data[$key]['countryName'] = $countryName;

            }

        }

        return $data;

    }

    /**
     * Analytics cloud file info
     * @return array
     */
    public function getCloudFileInfo() : array{

        $tbl = Files::getTbl();

        //get total cloud files
        $totalFiles = $this->db->getValue($tbl, "count(*)");

        //get today cloud files
        $this->db->where('DATE_FORMAT(createdAt, "%Y-%m-%d")', $this->dateTime);
        $todayFiles = (int) $this->db->getValue($tbl, "count(*)");

        //get total files size
        $this->db->join(\CloudMonster\Models\Buckets::getTbl(). ' b', 'b.id = f.bucketId', 'LEFT' );
        $totalFileSize = $this->db->getValue($tbl . ' f',"sum(b.size)");

        $this->db->join(Visitors::getTbl(). ' v', 'v.fileId = f.id', 'RIGHT' );
        $this->db->join(\CloudMonster\Models\Buckets::getTbl(). ' b', 'b.id = f.bucketId', 'LEFT' );
        $this->db->orderBy('sum(v.visit)');
        $this->db->groupBy('f.id');
        $mostVisitedFiles = $this->db->get($tbl . ' f',10 ,"
            f.* , sum(v.visit) as totalVisits, count(v.visit) as uniqVisits,
            b.name as fileName
        ");


       $tracker = new ProcessTracker();
       $summary = $tracker->getProcessSummary();
       unset($tracker);



       return [
           'total' => $totalFiles,
           'today' => $todayFiles,
           'totalSize' => $totalFileSize,
           'summary' => $summary,
           'mostVisited' => $mostVisitedFiles
       ];


    }

    /**
     * Analytics storage directory info
     * @return array
     */
    public function getStorageDirInfo() : array{

        //get cache dir size
        $cacheDir = Help::storagePath('cache');
        $cacheSize = Help::GetDirectorySize($cacheDir);

        //get session dir size
        $sessDir = Help::storagePath('session');
        $sessSize = Help::GetDirectorySize($sessDir);

        //get tmp dir size
        $tmpDir = Help::storagePath('tmp');
        $tmpSize = Help::GetDirectorySize($tmpDir);

        return [
            'total' => $cacheSize + $sessSize + $tmpSize,
            'cache' => $cacheSize + $sessSize,
            'tmp' => $tmpSize
        ];


    }

    /**
     * Analytics cloud drive info
     * @return array
     */
    public function getCloudDrivesInfo() : array{

        $tbl = CloudDrives::getTbl();

        //get total cloud files
        $totalDrives = $this->db->getValue($tbl, "count(*)");

        $this->db->where('status', CloudDrives::DRIVE_ACTIVE);
        $activeDrives = $this->db->getValue($tbl, "count(*)");

        $this->db->where('status', CloudDrives::DRIVE_ERROR);
        $errorDrives = $this->db->getValue($tbl, "count(*)");

        return [
            'total' => $totalDrives,
            'active' => $activeDrives,
            'error' => $errorDrives
        ];

    }

    /**
     * Get blacklisted data info
     * @return array
     */
    public function getBlacklistedInfo() : array{

        $blacklistedIps = App::getConfig('blacklisted_ips');
        $blacklistedIps = is_array($blacklistedIps) ? count($blacklistedIps) : 0;

        $blacklistedCountries = App::getConfig('blacklisted_countries');
        $blacklistedCountries = is_array($blacklistedCountries) ? count($blacklistedCountries) : 0;

        $blockedRequests = App::getConfig('blocked_requests');
        if(!is_numeric($blockedRequests)) $blockedRequests = 0;

        return [
            'ips' => $blacklistedIps,
            'countries' => $blacklistedCountries,
            'blockedRequests' => $blockedRequests
        ];

    }

    /**
     * Check analytics system is enabled
     * @return bool
     */
    public static function isSystemEnabled(): bool
    {
        return App::getConfig('analytics_system') == 1;
    }

    public function __debugInfo() {
        $vars = get_object_vars($this);
        if(isset($vars['db'])){
            unset($vars['db']);
        }
        return $vars;
    }

}