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

use CloudMonster\Core\Model;
use CloudMonster\Services\UploadProgress;
use CloudMonster\Helpers\Help;
use Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface;
use Phpfastcache\Exceptions\PhpfastcacheSimpleCacheException;
use Exception;


/**
 * Class ProcessTracker
 * @author John Antonio
 * @package CloudMonster\Models
 */
class ProcessTracker extends Model {

    /**
     * Cache instance
     * @var ExtendedCacheItemPoolInterface
     */
    protected ExtendedCacheItemPoolInterface $cache;

    /**
     * Tracker Caller ID
     * @var string
     */
    protected string $callerId = '';

    /**
     * Is caller session started
     * @var bool
     */
    protected bool $isSessionStarted = false;

    /**
     * Process started time
     * @var float
     */
    protected float $processStartedTime;

    /**
     * Database table
     * @var string
     */
    protected static string $tmpTbl = "process_tracker";


    /**
     * ProcessTracker constructor.
     */
    public function __construct() {
        parent::__construct($this::$tmpTbl);
    }

    /**
     * Create caller ID
     * @throws Exception
     */
    protected function createCallerId() {
        $this->callerId = Help::random(8);
    }


    /**
     * Call
     * @throws Exception
     */
    public function call() {
        //set caller ID
        $this->createCallerId();
        $this->isSessionStarted = true;
    }

    /**
     * @param $fileId
     * @return bool
     * @throws Exception
     */
    public function add($fileId): bool {
        $success = false;
        if ($this->isSessionStarted) {
            $success = $this->assign(['fileId' => $fileId, 'callerId' => $this->callerId, 'isTracking' => 1])->save();
            $this->processStartedTime = microtime(true);
        }
        return $success;
    }

    protected function beforeSave()
    {

        $mustNumeric = [
            'progress',
            'processTime',
            'remainingTime'
        ];

        foreach ($mustNumeric as $field){

            if(!is_numeric($this->getObj($field))){
                $this->addVal([$field=> 0]);
            }

        }

        parent::beforeSave();

    }

    /**
     * Get tmp data
     * @throws PhpfastcacheSimpleCacheException
     */
    public function getTmpData(): array {
        $tmpProgressData = UploadProgress::call($this->getFileId())->get();
        if (is_array($tmpProgressData)) {
            return $tmpProgressData;
        }
        return [];
    }

    /**
     * Load tmp data
     * @throws PhpfastcacheSimpleCacheException
     */
    protected function loadTmpData() {
        $tmpProgressData = $this->getTmpData();
        if (!empty($tmpProgressData)) {
            foreach ($tmpProgressData as $key => $val) {
                $this->addVal([$key => $val]);
            }
        }
    }

    /**
     * Get caller ID
     * @return string
     */
    public function getCallerId(): string
    {
        return $this->callerId;
    }

    /**
     * @throws PhpfastcacheSimpleCacheException
     */
    public function end() {
        if ($this->isEdit()) {
            //close current tracker
            $endTime = microtime(true);
            $executionTime = round(($endTime - $this->processStartedTime));
            $this->loadTmpData();
            $this->addVal(['processTime' => $executionTime]);
            $this->addVal(['isTracking' => 0]);
            $this->save();
            $this->clean();
        }
    }

    /**
     * Get new data IDs
     * @param int $lastId
     * @param int $bucketId
     * @return array
     */
    public function getNewDataIds(int $lastId = 0, int $bucketId = 0): array {
        $Ids = [];
        $this->db->join(Files::getTbl() . ' f', 'p.fileId = f.id', 'LEFT');
        if (!empty($bucketId)) {
            $this->db->where('p.fileId', $lastId, '>');
            $this->db->where('f.pstatus', [Files::WAITING, Files::PROCESSING], 'IN');
            $this->db->where('f.bucketId', $bucketId);
            $cols = 'f.id';
        } else {
            $this->db->where('p.id', $lastId, '>');
            $cols = 'p.id';
        }
        $results = $this->db->get($this->tbl . ' p', null, $cols);
        if ($this->db->count > 0) {
            foreach ($results as $k => $v) {
                array_push($Ids, $v['id']);
            }
        }
        return $Ids;
    }

    /**
     * Get tracking data by ids
     * @param array $ids
     * @param int $bucketId
     * @return array
     */
    public function getTrackingData(array $ids = [], int $bucketId = 0): array {
        $this->db->join(Files::getTbl() . ' f', 'p.fileId = f.id', 'LEFT');
        $this->db->join(CloudDrives::getTbl() . ' c', 'f.cloudDriveId = c.id', 'LEFT');
        if (empty($ids)) $ids = ['#1'];
        if (!empty($bucketId)) {
            $this->db->where('f.bucketId', $bucketId);
            $this->db->where('p.fileId', $ids, 'IN');
        } else {
            $this->db->where('p.id', $ids, 'IN');
        }
        //        $this->db->where('p.isTracking', 1);
        $data = $this->db->get($this->tbl . ' p', null, 'p.*, f.pstatus, f.msg,f.code, f.cloudDriveId, c.type');
        if ($this->db->count > 0) {
            return $data;
        }
        return [];
    }

    /**
     * Get active tracking data
     * @param array $reqIds
     * @param int $lastId
     * @param int $bucketId
     * @return array
     * @throws PhpfastcacheSimpleCacheException
     */
    public function getActiveData(array $reqIds = [], int $lastId = 0, int $bucketId = 0): array {

        $results = [];
        $newData = is_numeric($lastId) ? $this->getNewDataIds($lastId, $bucketId) : [];
        $activeIds = array_merge($reqIds, $newData);
        $data = $this->getTrackingData($activeIds, $bucketId);

        if (!empty($data)) {

            foreach ($data as $key => $val) {
                if ($this->load($val['id'])) {
                    //bind data
                    $this->bindObjVal($val);
                    if ($val['isTracking'] == 1) $this->loadTmpData();
                    $this->addVal(['processTime' => Help::formatSec($val['processTime']) ]);
                    $this->addVal(['remainingTime' => Help::formatSec($this->getRemainingTime()) ]);
                    $this->addVal(['pstatus' => Files::formatStatus($val['pstatus']) ]);
                    array_push($results, $this->getObj());
                }
            }

        }

        return $results;

    }

    public function getProcessSummary(): array
    {

        $files = new Files;

        $waiting = $files->count([ 'pstatus' => Files::WAITING ]);
        $processing = $files->count([ 'pstatus' => Files::PROCESSING ]);
        $failed = $files->count([ 'pstatus' => Files::INACTIVE ]);
        $active = $files->count([ 'pstatus' => Files::ACTIVE ]);
        $total = $files->count();

        unset($files);

        return [

            'total' => $total,
            'waiting' => $waiting,
            'active' => $active,
            'processing' => $processing,
            'failed' => $failed

        ];

    }

    /**
     * Delete by file ID
     * @param int $id
     * @return bool
     */
    public function delByFileId(int $id): bool {
        if (!empty($id)) {
            $this->db->where('fileId', $id);
            return $this->db->delete($this->tbl);
        }
        return false;
    }


}
