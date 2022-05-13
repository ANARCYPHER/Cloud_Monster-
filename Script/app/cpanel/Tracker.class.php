<?php


namespace CloudMonster\CPanel;


use CloudMonster\Core\Request;
use CloudMonster\CPanel;
use CloudMonster\Helpers\Help;
use CloudMonster\Models\ProcessTracker;

class Tracker extends CPanel {


    protected ProcessTracker $processTracker;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->processTracker = new ProcessTracker();
    }

    public function init(){

        $this->view->setTitle('Upload Process Tracker');

        $this->view->render('tracker', '',true);

    }

    protected function data(){

        $activeIds = Request::get('active');
        $lastId = Request::get('last');
        $bucketId = Request::get('bucket');


        if(!empty($activeIds)){
            $activeIds = Help::toArray($activeIds);
        }else{
            $activeIds = [];
        }
        if(!is_numeric($lastId)) $lastId = 0;
        if(!is_numeric($bucketId)) $bucketId = 0;

        $data = $this->processTracker->getActiveData($activeIds, $lastId, $bucketId);
        $this->addData($data, 'data');

        if(Request::get('withSummary') == 1){
            $summary = $this->processTracker->getProcessSummary();
            $this->addData($summary, 'summary');
        }


        $this->ajaxResponse([], true);

    }



}