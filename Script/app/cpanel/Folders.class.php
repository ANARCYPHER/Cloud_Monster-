<?php


namespace CloudMonster\CPanel;


use CloudMonster\Core\Request;
use CloudMonster\CPanel;
use CloudMonster\Models\LocalFolders;

class Folders extends CPanel {


    protected LocalFolders $localFolder;
    protected \CloudMonster\Models\Buckets $bucket;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->localFolder = new LocalFolders();
        $this->bucket = new \CloudMonster\Models\Buckets();

    }


    protected function list($id = ''){

        if(empty($id)) $id = LocalFolders::ROOT_FOLDER;


        $itemList = [];


        if($this->localFolder->load($id)){
            $itemList = $this->localFolder->getByParentId($id, ['id','name']);
        }

        $parentFolderList = $this->localFolder->getParentList($id);

        sort($parentFolderList);

        $this->addData($itemList, 'folders');
        $this->addData($parentFolderList, 'parentList');

        $this->ajaxResponse([], true);

    }

    protected function new(){
        $name = Request::post('name');
        $parentId = Request::post('parent');

        $success = false;

        if(!empty($name)){

            if(!is_numeric($parentId)) $parentId = 0;

            if($this->localFolder->create($name, $parentId)){

                $this->addData($this->localFolder->getObj('id'), 'folderId');
                $this->addAlert('Folder created successfully', 'success');

                $success = true;

            }else{

                $this->addAlert($this->localFolder->getError());

            }

        }else{

            $this->addAlert('Folder name is required');

        }

        $this->ajaxResponse([],$success);
    }

    protected function move(){

        $reqId = Request::post('from');
        $destFolder = Request::post('to');
        $type = Request::post('type');

        $success = false;

        if(!empty($reqId) && !empty($destFolder)){

            if($type == 'folder'){
                if($this->localFolder->load($reqId)){
                    if($this->localFolder->move($destFolder)){
                        $success = true;
                        $this->addAlert('Folder moved successfully', 'success');
                    }else{
                        $this->addAlert($this->localFolder->getError());
                    }
                }else{
                    $this->addAlert('Folder not found');
                }
            }else{
                //bucket
                if($this->bucket->load($reqId)){
                    if($this->bucket->move($destFolder)){
                        $success = true;
                        $this->addAlert('Bucket moved successfully', 'success');
                    }else{
                        $this->addAlert($this->bucket->getError());
                    }
                }else{
                    $this->addAlert('Bucket not found');
                }
            }

        }else{
            $this->addAlert('Unknown error occurred');
        }

        $this->ajaxResponse([], $success);


    }

    protected function delete(){
        $reqId = Request::post('id');
        $type = Request::post('type');

        $success = false;

        if($type == 'folder'){

            $childList = $this->localFolder->getChildList($reqId);
            $childIds = $this->localFolder->extractChildIds($childList);

            if($this->localFolder->load($reqId)){
                if($this->localFolder->isEmpty($childIds)){
                    //delete all
                    $this->localFolder
                        ->parent()
                        ->soft(CLOUD_FOLDER_DELETE)
                        ->delete();

                    $this->addAlert('Folder deleted successfully', 'success');
                    $success = true;

                }else{

                    $this->addAlert('folder is not empty');

                }
            }

        }else{

            if($this->bucket->load($reqId)){

                if($this->bucket->soft(CLOUD_FILE_DELETE)->delete()){

                    $this->addAlert('Bucket deleted successfully', 'success');
                    $success = true;

                }else{

                    $this->addAlert('Bucket is not empty');

                }


            }

        }




        $this->ajaxResponse([],$success);
    }

    protected function rename(){

        $reqId = Request::post('folder');
        $name = Request::post('name');
        $type = Request::post('type');

        $success = false;

        if(!empty($name)){

            if($type === 'folder'){
                if($this->localFolder->load($reqId)){

                    if($this->localFolder->rename($name)){

                        $this->addAlert('Folder renamed successfully', 'success');
                        $success = true;

                    }else{

                        $this->addAlert($this->localFolder->getError());

                    }

                }else{
                    $this->addAlert('Folder not found');
                }
            }else{
                if($this->bucket->load($reqId)){

                    if($this->bucket->rename($name)){

                        $this->addAlert('Bucket renamed successfully', 'success');
                        $success = true;

                    }else{

                        $this->addAlert($this->localFolder->getError());

                    }

                }else{
                    $this->addAlert('Folder not found');
                }
            }



        }else{

            $this->addAlert('Folder name is required');

        }

        $this->ajaxResponse([],$success);

    }


}