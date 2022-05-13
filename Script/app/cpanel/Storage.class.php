<?php


namespace CloudMonster\CPanel;


use CloudMonster\CPanel;
use CloudMonster\Helpers\Help;


class Storage extends CPanel {



    public function clear($type = ''){

        if(!empty($type)){

            if(in_array($type, ['tmp','cache'])){

                $storageDir = Help::storagePath($type);
                if(file_exists($storageDir)){
                    Help::deleteDir($storageDir);
                }

                if($type == 'cache'){
                    $storageDir = Help::storagePath('session');
                    if(file_exists($storageDir)){
                        Help::deleteDir($storageDir);
                    }
                }

            }

        }

        $this->jsonResponse(['success'=>true]);

    }


}