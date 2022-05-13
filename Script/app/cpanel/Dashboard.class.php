<?php


namespace CloudMonster\CPanel;


use CloudMonster\CPanel;



class Dashboard extends CPanel {



    public function init(){


        $this->view->setTitle('dashboard');



        $analytics = new \CloudMonster\Services\Analytics();

        $analyticsData = [

            'buckets' => $analytics->getBucketsInfo(),
            'visits' => $analytics->getVisitsInfo(),
            'files' => $analytics->getCloudFileInfo(),
            'drives' => $analytics->getCloudDrivesInfo(),
            'storage' => $analytics->getStorageDirInfo(),
            'blacklisted' => $analytics->getBlacklistedInfo()

        ];


        $this->addData($analyticsData, 'analyticsData');

        $this->view->addJs('charts');

        $this->view->render('dashboard');

    }


}