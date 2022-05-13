<?php


namespace CloudMonster\CPanel;


use CloudMonster\CPanel;
use CloudMonster\Helpers\Help;
use CloudMonster\Models\Login;


class Logout extends CPanel {


    public function init(){


        Login::logout();

        $this->addAlert('You\'re now successfully logged out', 'success');
        Help::redirect('cplogin');


    }

}