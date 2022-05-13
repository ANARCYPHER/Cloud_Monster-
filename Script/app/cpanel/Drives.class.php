<?php


namespace CloudMonster\CPanel;


use CloudMonster\Core\Request;
use CloudMonster\Core\Session;
use CloudMonster\Models\CloudDrives;
use CloudMonster\Helpers\CloudDriveStructure;
use CloudMonster\Helpers\Help;
use CloudMonster\CPanel;


/**
 * Class Drives
 * @author John Anta
 * @package CloudMonster\CPanel
 */
class Drives extends CPanel {

    /**
     * Cloud drive model
     * @var CloudDrives
     */
    protected CloudDrives $cloudDrive;


    /**
     * Drives constructor.
     * @param $app
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->cloudDrive = new CloudDrives();
    }

    /**
     * cd list action
     */
    protected function list(){
        $this->view->setTitle('Cloud Drives List');

        $this->addData($this->cloudDrive->getList(), 'driveList');
        $this->view->render('drives-list');
    }

    /**
     * cd edit action
     */
    protected function edit($id = ''){
        $this->new($id);
    }

    /**
     * cd new action
     */
    protected function new($id = ''){

        $this->view->setTitle('Add/ Edit Cloud Drive');

        $source = Request::get('source');
        $isEdit = $this->action == 'edit';
        $isDriveFound = true;

        if($isEdit){
            //attempt to load drive
            if(!empty($id)){
                if( $this->cloudDrive->load($id)){
                    $source =  $this->cloudDrive->getType();

                    //check is it one drive

                    if(Request::get('check') == 1 && $this->cloudDrive->loadCloudApp()){
                        if($source == 'onedrive'){

                            if(!$this->cloudDrive->cloudApp->isAuthenticated()){
                                $authUrl = $this->cloudDrive->cloudApp->getAuthUrl();
                                if(!empty($authUrl)){
                                    //set Drive ID in session
                                    Session::set('driveId', $id);
                                    Help::redirect($authUrl, true);

                                }else{
                                    $this->addAlert('Unable to prepare authentication URL');
                                }
                            }

                        }else{

                            if($this->cloudDrive->cloudApp->isActive()){
                                if($this->cloudDrive->isError()){
                                    $this->cloudDrive->active();
                                }
                                $this->addAlert('API credentials successfully verified', 'info');
                            }else{
                                $this->addAlert('Invalid API credentials');
                            }

                        }
                        Help::redirect('cpanel/drives/edit/' . $id);
                    }






                }else{
                    $this->addAlert('cloud drive not found');
                    $isDriveFound = false;
                }
            }else{
                //404
            }
        }


        $driveItem = null;

        $driveStructure = new CloudDriveStructure();
        $drives = $driveStructure->getDrives();

        if(!empty($source)){

            $driveItem = $driveStructure->getByDrive($source);
            $authData = $isEdit ?  $this->cloudDrive->getAuthData() : '';
            if(!empty($authData) && Help::isJson($authData)){
                $driveItem->bind(Help::toArray($authData));
            }


            if(Request::isPost()){

                $success = false;

                $driveName = Request::post('name');


                if(!$driveItem->isActive()){
                    $this->addAlert('Drive item is in-active');
                }else{
                    $formData = Request::getFormData(array_keys($driveItem->getUserInput()));
                    $driveItem->bind($formData);
                    //validate
                    foreach ($driveItem->getUserInput() as $key => $item){
                        $val = $item['val'] ?? '';
                        $label = $item['label'] ?? '';
                        if(empty($val)){
                            $this->addAlert($label . ' is required');
                        }
                    }
                }

                if(!$this->hasAlerts()){


                    $data = [
                        'name' => $driveName,
                        'type' => $source,
                        'authData' => Help::toJson($driveItem->getData())
                    ];

//dnd($data);
                    //attempt to save data
                    if( $this->cloudDrive->assign($data)->save()){


                        $this->addAlert('Cloud drive saved successfully', 'success');
                        if($source == 'onedrive'){
                            $this->cloudDrive->error();
                        }
                        Help::redirect('cpanel/drives/edit/' .  $this->cloudDrive->getID() . '?check=1');



                    }else{
                        $this->addAlert('Unable to save');
                    }

                }


                Help::redirect('self');


            }

        }

        $formData =  $this->cloudDrive->getObj();
        if(!empty($formData['status']))
            $formData['status'] = CloudDrives::getFormattedStatus($formData['status']);


        $this->addData($formData, 'formData');
        $this->addData($isDriveFound, 'isDriveFound');
        $this->addData($isEdit, 'isEdit');
        $this->addData($drives, 'drives');
        $this->addData($source, 'source');
        $this->addData($driveItem, 'drive');
        $this->view->render('drive');

    }



    /**
     * cd delete action
     */
    protected function delete($id = ''){
        if(!empty($id)){

            if($this->cloudDrive->load($id)){

                if(!$this->cloudDrive->hasBuckets()){

                    $this->cloudDrive->delete();
                    $this->addAlert('Drive deleted successfully', 'success');

                }else{

                    $this->addAlert('Unable to delete');
                    $this->addAlert('Firstly delete active buckets in this drive', 'warning');

                }

            }

        }

        Help::redirect('cpanel/drives/list');
    }

    /**
     * cd more info action
     */
    protected function moreInfo($id = ''){

        $accInfo = [];

        if(!empty($id)){

            if($this->cloudDrive->load($id)){

                if($this->cloudDrive->loadCloudApp()){

                    $accInfo = $this->cloudDrive->cloudApp->getAccountInfo();

                }

            }

        }
        $this->addData($accInfo, 'accInfo');

        $this->ajaxResponse([], true);

    }

    /**
     * cd pause action
     */
    protected function pause($id = ''){

        if(!empty($id)){

            if($this->cloudDrive->load($id)){

                if($this->cloudDrive->isActive()){
                    $this->cloudDrive->paused();
                }elseif($this->cloudDrive->isPaused()){
                    $this->cloudDrive->active();
                }

            }

        }

        Help::redirect('cpanel/drives/list');

    }

    /**
     * cd check action
     */
    protected function check(){

        $success = false;

        if(!empty($id)){

            if($this->cloudDrive->load($id) && $this->cloudDrive->loadCloudApp()){

                if($this->cloudDrive->cloudApp->isActive()){

                    if($this->cloudDrive->isError()){
                        $this->cloudDrive->active();
                    }

                    $success = true;

                }

            }

        }

        $this->ajaxResponse([],$success);

    }


}