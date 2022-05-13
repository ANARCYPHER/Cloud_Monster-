<?php


namespace CloudMonster\CPanel;


use CloudMonster\Core\Request;
use CloudMonster\CPanel;
use CloudMonster\Session\Visits;

/**
 * Class Analytics
 * @author John Anta
 * @package CloudMonster\CPanel
 */
class Analytics extends CPanel {


    /**
     * Analytics service
     * @var \CloudMonster\Services\Analytics
     */
    protected \CloudMonster\Services\Analytics $analytics;


    /**
     * Analytics constructor.
     * @param $app
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->analytics = new \CloudMonster\Services\Analytics();
    }


    /**
     * Default action
     * @return void
     */
    public function init(){

        $this->view->setTitle('Analytics');

        $fileId = Request::get('file');
        $fileNotFound = false;

        $file = new \CloudMonster\Models\Files();

        if(!empty($fileId) && is_numeric($fileId)){

            if(!$file->load($fileId)){
                $this->addAlert('Requested file ID not found');
                $fileNotFound = true;
            }
        }else{
            $fileId = 0;
        }


        $top10Visits = !$fileNotFound ? $this->analytics->getAllVisitors(10, $fileId) : [];


        $this->addData($top10Visits, 'top10Visits');
        $this->addData($fileNotFound, 'fileNotFound');
        $this->addData($file->getFileInfo(), 'fileInfo');
        $this->view->addJs('charts');
        $this->view->render('analytics');

    }


    /**
     * json data action
     */
    protected function json(){


        $chart = Request::get('chart');


        switch($chart){

            case 'visitorsByCountry':

                $d1 = Request::get('d1');
                $d2 = Request::get('d2');
                $fileId = is_numeric(Request::get('file')) ? Request::get('file') : 0;
                $isUnique = Request::get('unique') == 'true';

                $data = $this->analytics->getVisitorsByCountry($d1, $d2, $isUnique, $fileId);

                $this->jsonResponse($data);

                break;

            case 'visitorsByMonthly':


                $month = Request::get('month');
                $year = Request::get('year');
                $fileId = is_numeric(Request::get('file')) ? Request::get('file') : 0;

                if(!is_numeric($month)) $month = 0;
                if(!is_numeric($year)) $year = 0;

                $data = $this->analytics->getVisitorsByMonth($month, $year, $fileId);

                $this->jsonResponse($data);


                break;

            case 'liveVisitors':

                Visits::start();
                $sessData = Visits::getAll();

                $visits = 0;

                if(!empty($sessData)){

                    //count visits
                    $visits = count($sessData);

                    //remove sess items
                    $sessKeys = array_keys($sessData);
                    Visits::removeSessItems($sessKeys);

                }

                $this->jsonResponse(['visits'=>$visits]);

                break;

        }

    }



}