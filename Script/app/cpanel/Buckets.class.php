<?php


namespace CloudMonster\CPanel;


use CloudMonster\Core\Cookie;
use CloudMonster\Core\CURL;
use CloudMonster\Core\Request;
use CloudMonster\Models\CloudDrives;
use CloudMonster\Models\Files;
use CloudMonster\Models\LocalFolders;
use CloudMonster\Services\CloudFile;
use CloudMonster\Services\ProcessManager;
use CloudMonster\Services\ReUpload;
use CloudMonster\Helpers\Help;
use CloudMonster\Helpers\UploadedTmpFile;
use CloudMonster\Helpers\Validation;
use CloudMonster\CPanel;
use CloudMonster\Services\UploadProgress;


/**
 * Class Buckets
 * @author John Anta
 * @package CloudMonster\CPanel
 */
class Buckets extends CPanel {


    /**
     * Buckets model
     * @var \CloudMonster\Models\Buckets
     */
    protected \CloudMonster\Models\Buckets $buckets;


    /**
     * Buckets constructor.
     * @param $app
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->buckets = new \CloudMonster\Models\Buckets();
    }

    /**
     * Buckets list action
     */
    public function list($id = ''){

        $this->view->setTitle('My Buckets');


        $driveId = Request::get('drive');
        if(!is_numeric($driveId)) $driveId = 0;

        $folders = new LocalFolders();
        $cloudDrives = new CloudDrives();

        if(empty($id)){
            $id = LocalFolders::ROOT_FOLDER;
            $folders->cleanGarbage();
            $this->buckets->cleanGarbage();
        }


        //attempt to check and load selected drive
        if(!empty($driveId)){
            if($cloudDrives->load($driveId)){

            }else{
                //404
            }
        }

        //get all cloud drive list
        $drives = $cloudDrives->get(['status'=>'']);
        $itemList = [];


        if($folders->load($id)){

            $itemList = $folders->withBuckets()->getByParentId($id, [], $driveId);

            foreach ($itemList as $key => $val){

                $isFolder = !isset($val['folderId']);
                $type = $isFolder ? 'folder' : 'bucket';

                if($isFolder){

                    $href =  getURIPath('cpanel/buckets/list/'.$val['id']);

                }else{

                    $href =  getURIPath('cpanel/buckets/view/'.$val['id']);

                }


                $itemList[$key]['isFolder'] = $isFolder;
                $itemList[$key]['type'] = $type;

                $itemList[$key]['href'] = $href . '?drive=' . $driveId;



                $itemList[$key]['createdAt'] = Help::formatDT($val['createdAt']);
                $itemList[$key]['updatedAt'] = Help::formatDT($val['updatedAt']);

            }
        }

        //move bucket
        $isMoveActive = Cookie::isset('move_fld_id');

        $activeDrive = $cloudDrives->getObj();
        $activeDrive['fstatus'] = $cloudDrives->isLoaded() ? CloudDrives::getFormattedStatus($activeDrive['status'])  : '';


        $parentFolderList = $folders->getParentList($id);

        $this->addData($itemList, 'list');
        $this->addData($activeDrive, 'activeDrive');
        $this->addData($activeDrive['id'], 'activeDriveId');
        $this->addData($drives, 'drives');
        $this->addData($folders->getObj(), 'currentFolder');
        $this->addData($parentFolderList, 'parentFolders');


        $this->view->render('bucket-list');

    }

    /**
     * Bucket view action
     */
    public function view($id = ''){

        if(isset($_GET['file'])){

            $tmpFile = new Files;
            if($tmpFile->load(Request::get('file'))){
                Help::redirect('cpanel/buckets/view/' . $tmpFile->getBucketId());
            }else{
                //error
                die('file not found');
            }


        }

        if(!empty($id)){

            if($this->buckets->load($id)){

                $files = new Files;
                $cloudDrives = new CloudDrives();

                $newDrives = [];

                $filesList = $files->getByBucketId($id);

                if(!empty($filesList)){

                    $activeCloudDriveIds = Help::extractData($filesList, 'cloudDriveId');
                    $newDrives = $cloudDrives->get([
                        'id' => [
                            $activeCloudDriveIds,
                            'NOT IN'
                        ]
                    ],[],['id','name','type']);

                }


                $bucket = $this->buckets->getObj();
                if($this->buckets->isLoaded()){
                    $bucket['link'] = Help::getBucketLink($id) ;
                    $reUpSess = $this->buckets->getAutoReUploadSession();
                    if($reUpSess === \CloudMonster\Models\Buckets::ACTIVE_RE_UPLOAD_SESSION){
                        $this->addAlert('Auto re-upload  will take a few minutes  for prepare session', 'warning');
                    }else if($reUpSess === \CloudMonster\Models\Buckets::FAILED_RE_UPLOAD_SESSION){
                        $this->buckets->updateReUploadSession(\CloudMonster\Models\Buckets::INACTIVE_RE_UPLOAD_SESSION);
                        $this->addAlert('Unable to prepare auto re-upload session');
                    }
                }

                $this->addData($newDrives, 'newDrives');
                $this->addData($bucket, 'bucket');
                $this->addData($filesList, 'files');

            }else{

                $this->addAlert('bucket not found');

            }

            $this->view->render('view-bucket');

        }else{
            //404
        }
    }

    /**
     * New bucket action
     */
    public function new(){

        $this->view->setTitle('New Bucket');

        $tmpFolderId = Request::get('folder');
        $fLocation = '/home';
        $folders = new LocalFolders();


        if(Request::isPost()){

            $success = false;
            $respData = [];

            //requested post data
            $uniqId = Request::post('dzuuid');
            $driveIds = Request::post('drives');
            $folderId = Request::post('folder');
            $bucketName = Request::post('bucketName');
            $link = Request::post('link');

            //set default values
            if(empty($folderId)) $folderId = LocalFolders::ROOT_FOLDER;
            $driveIds = Help::toArray($driveIds);

            $isRemoteUpload = !empty($link);

            if($isRemoteUpload) $uniqId = Help::random(15);

            //attempt to validate input data
            $validation = new Validation();

            if(! $isRemoteUpload){
                $validation
                    ->name('dzuuid')
                    ->value($uniqId)
                    ->pattern('text')
                    ->required();
            }else{
                $validation
                    ->name('link')
                    ->value($link)
                    ->pattern('url')
                    ->required();
            }

            $validation
                ->name('drives')
                ->value($driveIds)
                ->pattern('array')
                ->required();

            $validation
                ->name('folder Id')
                ->value($folderId)
                ->pattern('int');

            $validation
                ->name('bucket name')
                ->value($bucketName)
                ->pattern('text')
                ->required();

            //check validation
            if($validation->isSuccess()){

                //get uploaded temporary file
                $tmpFile = new UploadedTmpFile($uniqId);

                if($tmpFile->isExist() || $isRemoteUpload){

                    //check requested folder is exist
                    if($folders->isExist($folderId)){

                        //get temporary file information
                        $fileInfo = $tmpFile->get();

                        //init bucket data
                        $data = [
                            'name' => $bucketName,
                            'folderId' => $folderId,
                            'ext' => $fileInfo->extension,
                            'mime' => $fileInfo->mime,
                            'size' => $fileInfo->size,
                            'uniqId' => $uniqId,
                            'link' => $link
                        ];

                        //attempt to create new bucket
                        if($this->buckets->assign($data)->save()){

                            //attempt to update buckets with cloud drive ids
                            if($this->buckets->update($driveIds)){

                                $this->addAlert('Bucket created successfully', 'success');
                                $success = true;

                            }

                            //If is it remote upload, bucket is not ready yet
                            if($isRemoteUpload) {
                                $this->buckets->processing();
                                $this->buckets->filesNotReady();
                            }

                        }


                    }else{

                        $this->addAlert('folder not found');

                    }


                }else{

                    $this->addAlert('Uploaded temporary file does not exist');

                }




            }else{

                $this->addAlert($validation->getErrors());

            }


            if($success){
                $respData = $this->buckets->getObj();

                $processType = ! $isRemoteUpload  ? 'upload' : 'remote-upload';
                $processData = [
                    'bucket_id' => $this->buckets->getID()
                ];

                //update process manager
                $processManager = new ProcessManager($processType, $processData);
                $processManager->run(true)->await();

                if(!$processManager->isOk()){
                    $this->addAlert('Cloud uploader not started', 'warning');
                }

            }else{
                if(!$this->hasAlerts()){
                    $this->addAlert('Unable to create bucket');
                }
            }



            $this->ajaxResponse($respData, $success);


        }

        if(!empty($tmpFolderId)){
            if($folders->load($tmpFolderId, true) && !$folders->isRoot()){
                $fLocation .= '/' . $folders->getLocation();
            }
        }


        $cloudDrives = new CloudDrives();

        $drives = $cloudDrives->get();

        $this->addData($drives, 'drives');
        $this->addData($tmpFolderId, 'tmpFolderId');
        $this->addData($fLocation, 'fLocation');
        $this->view->render('bucket');

    }

    /**
     * Bucket update action
     */
    public function update(){
        if(Request::isPost()){


            $files = new Files();
            $success = false;

            $uniqId = Request::post('dzuuid');
            $type = Request::post('type');
            $driveIds = Request::post('drives');
            $bucketId = Request::post('bucketId');
            $dest = Request::post('dest');

            $driveIds = Help::toArray($driveIds);


            $validation = new Validation();

            if($type === 'manually'){
                $validation
                    ->name('dzuuid')
                    ->value($uniqId)
                    ->pattern('text')
                    ->required();
            }

            $validation
                ->name('drives')
                ->value($driveIds)
                ->pattern('array')
                ->required();

            $validation
                ->name('folder Id')
                ->value($bucketId)
                ->pattern('int')
                ->required();

            //validate data
            if(!$validation->isSuccess()){
                $this->addAlert($validation->getErrors());
            }else if(!$this->buckets->load($bucketId)){
                $this->addAlert('Bucket not found');
            }else if($type === 'auto'){
                //init auto re-upload
                $reUpload = new ReUpload($this->buckets);
                if(!$reUpload->isOk()){
                    $this->addAlert('Auto re-upload is unavailable');
                }
            }else if($type === 'manually'){
                //attempt to get uploaded temporary file
                $tmpFile = new UploadedTmpFile($uniqId);
                if($tmpFile->isExist()){
                    //update uniq ID
                    $this->buckets->assign(['uniqId'=>$uniqId])->save();
                }else{
                    $this->addAlert('Uploaded temporary file not found');
                }
            }


            if(!$this->hasAlerts()){



                if($dest === 'exist-file'){
                    //update exist file
                    foreach ($driveIds as $k => $fId){
                        if($files->load([
                            'id' => $fId,
                            'bucketId' => $bucketId
                        ])){
                            //update exist file status
                            $files->await();
                            $files->removeTracker();
                            $success = true;
                        }else{
                            unset($driveIds[$k]);
                        }
                    }

                    //delete cloud file
                    CloudFile::process('delete', $this->buckets, $driveIds);

                }else{

                    if($this->buckets->update($driveIds)){
                        $success = true;
                    }

                }


                if($success){
                    if($type === 'manually'){
                        ProcessManager::update();
                    }else{
                        if(isset($reUpload))
                            $reUpload->process();
                    }
                }


            }

            if($success){
                $this->addAlert('bucket updated successfully', 'success');
            }else{
                $this->addAlert('unable to update bucket');
            }


            $this->ajaxResponse([],$success);

        }
    }

    /**
     * bucket shared action
     */
    public function shared(){

        $success = false;

        if(Request::isPost()){

            $bucketId = Request::post('bucketId');
            $status = Request::post('st');

            if($this->buckets->load($bucketId)){
                if($this->buckets->getShared() == $status){
                    if($this->buckets->toggleShared()){
                        $success = true;
                    }
                }
            }

        }

        $this->ajaxResponse([],$success);

    }

    /**
     * bucket delete action
     */
    public function delete(){
        $success = false;

        if(Request::isPost()){

            $bucketId = Request::post('bucketId');
            $fileId = Request::post('fileId');

            $files = new Files;

            if($this->buckets->load($bucketId)){

                if($files->load([
                    'bucketId' => $bucketId,
                    'id' => $fileId
                ])){

                    CloudFile::process('delete', $this->buckets, [$fileId]);
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