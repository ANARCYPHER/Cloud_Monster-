<?php


namespace CloudMonster\CPanel;


use CloudMonster\Core\Request;
use CloudMonster\CPanel;
use CloudMonster\Models\CloudDrives;
use CloudMonster\Services\CloudFile;


class Files extends CPanel {

    protected \CloudMonster\Models\Files $file;

    public function __construct($app)
    {
        $this->file = new \CloudMonster\Models\Files();
        parent::__construct($app);
    }

    public function list(){


        $this->view->setTitle('my cloud files');

        $status = Request::get('status');
        $driveId = Request::get('drive');

        $cloudDrives = new CloudDrives();
        //attempt to check and load selected drive
        if(!empty($driveId)){
            if($cloudDrives->load($driveId)){

            }else{
                //404
            }
        }



        //get all cloud drive list
        $drives = $cloudDrives->get(['status'=>'']);

        $statusCode = '';
        if(!empty($status)){

            if(in_array($status, ['active','process','waiting','failed'])){
                $statusCode = \CloudMonster\Models\Files::getStatusCode($status);
            }else{
                //404
            }
        }

        if(!is_numeric($driveId)) $driveId = 0;
        $files = $this->file->getAllFiles(0,$statusCode, $driveId);

        $activeDrive = $cloudDrives->getObj();
        $activeDrive['fstatus'] = $cloudDrives->isLoaded() ? CloudDrives::getFormattedStatus($activeDrive['status'])  : '';


        $this->addData($files, 'files');
        $this->addData($status, 'status');
        $this->addData($drives, 'drives');
        $this->addData($activeDrive, 'activeDrive');
        $this->addData($driveId, 'activeDriveId');
        $this->view->render('files');

    }

    protected function delete(){
        $success = false;

        if(Request::isPost()){

            $bucketId = Request::post('bucketId');
            $fileId = Request::post('fileId');

            $bucket = new \CloudMonster\Models\Buckets();

            if($bucket->load($bucketId)){

                if($this->file->load([
                    'bucketId' => $bucketId,
                    'id' => $fileId
                ])){

                    $this->file->soft()->delete();
                    CloudFile::process('delete', $bucket, [$fileId]);
                    $this->addAlert('File deleted successfully', 'success');
                    $success = true;

                }

            }


        }

        if(!$success){
            $this->addAlert('Unable to delete');
        }

        $this->ajaxResponse([], $success);


    }


}