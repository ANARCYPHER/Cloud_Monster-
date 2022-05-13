<?php


namespace CloudMonster\CPanel;


use CloudMonster\Core\Request;
use CloudMonster\CPanel;


class Download extends CPanel {

    public function getProgress(){

        $bucketId = Request::get('bucket_id');
        $progressData = [];
        $success = false;

        if(!empty($bucketId)){

            $bucket = new \CloudMonster\Models\Buckets();
            if($bucket->load($bucketId) && ! $bucket->isDone()){

                $progressData = \CloudMonster\Helpers\RemoteDownload::getProgress($bucketId);

            }

            $progressData['isDone'] = $bucket->isDone();
            $success = true;

        }

        $this->ajaxResponse($progressData, $success);

    }



}