<?php


namespace CloudMonster\CPanel;


use CloudMonster\Core\Request;
use CloudMonster\Core\TmpFile;
use CloudMonster\CPanel;
use CloudMonster\Helpers\Help;
use CloudMonster\Services\LocalUpload;

class Upload extends CPanel {


    protected \CloudMonster\Models\Buckets $bucket;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->bucket = new \CloudMonster\Models\Buckets();
    }


    protected function chunk(){

        session_write_close();
        $success = false;
        $error = '';

        if(Request::isPost() && TmpFile::issetFile('file')){

            $fileId = Request::post('dzuuid');
            $totalFileSize = Request::post('dztotalfilesize');
            $chunkIndex = Request::post('dzchunkindex') + 1;

            //verify min file size
            if($totalFileSize < 1048576){
                $error = 'File is too min. Min filesize: 1MB.';
            }

            if(empty($fileId) || empty($chunkIndex)){
                $error = 'Invalid Request';
            }

            if(LocalUpload::isCanceled($fileId)){
                $error = 'Upload canceled';
            }

            if(empty($error)){
                $localUpload = new LocalUpload($fileId);
                if($localUpload->chunk('file', $chunkIndex)->upload()){
                    $success = true;
                }else{
                    $error = $localUpload->getError();
                }
            }


        }

        if(!$success){
            header("HTTP/1.0 400 Bad Request");
            echo $error;exit;
        }else{
            $this->ajaxResponse([],true);
        }

    }

    protected function concat(){

        $success = false;

        if(Request::isPost()){

            $uniqId = Request::post('dzuuid');
            $chunkTotal = Request::post('dztotalchunkcount');

            if(!empty($uniqId) && is_numeric($chunkTotal)){

                $localUpload = new LocalUpload($uniqId);
                if($localUpload->chunkConcat($chunkTotal)){

                    $success = true;

                }

            }


        }

        $this->ajaxResponse([], $success);

    }

    protected function del(){

        $success = false;

        if(Request::isDelete()){


            $fileId = Request::get('dzuuid');

            if(!empty($fileId)){

                if(LocalUpload::cancel($fileId)){
                    $success = true;
                }

            }

        }

        $this->ajaxResponse([], $success);

    }

    protected function checkFile(): bool
    {
        $success = false;
        $file = Request::get('link');
//        if(!empty($file)){
//            $dl = \CloudMonster\Services\RemoteUpload::getDl($file);
//            if(! empty($dl)){
//                $success = true;
//            }
//        }
        $success = true;
        $this->ajaxResponse([], $success);
    }



}