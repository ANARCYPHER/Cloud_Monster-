<?php


namespace CloudMonster\CPanel;


use CloudMonster\Core\Request;
use CloudMonster\CPanel;

class Logger extends CPanel {



    public function __construct($app)
    {
        parent::__construct($app);

    }

    protected function getLog(){

        $offset = Request::get('offset');

        $data = \CloudMonster\Helpers\Logger::getLogData();

        $data = array_slice($data, $offset);

        $this->ajaxResponse($data, true);

    }

    protected function console(){

        $this->view->render('logger/console', '',true);

    }


}