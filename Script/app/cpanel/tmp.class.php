<?php


namespace CloudMonster\CPanel;


use CloudMonster\CPanel;


class tmp extends CPanel {


    public function init(){


        $this->view->setTitle('system process');

        $this->view->render('process');

    }

}