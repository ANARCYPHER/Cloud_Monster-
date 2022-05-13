<?php


namespace CloudMonster\CPanel;


use CloudMonster\Core\Request;
use CloudMonster\File;
use CloudMonster\Helpers\Help;
use CloudMonster\CPanel;
use CloudMonster\Helpers\Validation;
use CloudMonster\Models\Login;

/**
 * Class Settings
 * @author John Anta
 * @package CloudMonster\CPanel
 */
class Settings extends CPanel {


    /**
     * Default action
     * @return void
     */
    public function init(){


        $this->view->setTitle('Settings');

        //application configuration
        $config = $this::$config;

        //decode custom slugs
        $customSlugs = $config['custom_slugs'];
        if(Help::isJson($customSlugs)){
            $customSlugs = Help::toArray($customSlugs);
            $config  = array_merge($config, $customSlugs);
        }

        //decode blacklisted ips
        if(!empty($config['blacklisted_ips'])){
            $config['blacklisted_ips'] = implode(', ', Help::toArray($config['blacklisted_ips']));
        }

        //decode blacklisted countries
        if(!empty($config['blacklisted_countries'])){
            $config['blacklisted_countries'] = implode(', ', Help::toArray($config['blacklisted_countries']));
        }

        $this->addData($config, 'config');
        $this->view->render('settings');

    }

    /**
     * General settings action
     */
    protected function general(){

        if(Request::isPost()){

            $fileLinkSlug         = Help::slugify(Request::post('file_link_slug'));
            $bucketLinkSlug       = Help::slugify(Request::post('bucket_link_slug'));
            $analyticSystem       = Request::post('analytics_system') == 'on' ? 1 : 0;
            $isVisitsInfoRequired = Request::post('is_visitor_info_required') == 'on' ? 1 : 0;
            $blacklistedIps       = Request::post('blacklisted_ips');
            $blacklistedCountries = Request::post('blacklisted_countries');


            //check file link slug
            if(!empty($fileLinkSlug) && $fileLinkSlug != 'file'){
                if(!Help::isUniqSystemAction($fileLinkSlug)){
                    $this->addAlert('You can not use this slug name for file link. plz try another one', 'warning');
                    $fileLinkSlug = '';
                }
            }

            //check bucket link slug
            if(!empty($bucketLinkSlug) && $bucketLinkSlug != 'bucket'){
                if(!Help::isUniqSystemAction($bucketLinkSlug)){
                    $this->addAlert('You can not use this slug name for bucket link. plz try another one', 'warning');
                    $bucketLinkSlug = '';
                }
            }

            //check blacklisted ips
            $validBlackListedIps = [];
            if(!empty($blacklistedIps)){
                $blacklistedIps = explode(',', str_replace(' ','',$blacklistedIps));
                if(!empty($blacklistedIps)){
                    foreach ($blacklistedIps as $ip){
                        if(Help::isValidIp($ip)){
                            array_push($validBlackListedIps, $ip);
                        }
                    }

                    $validBlackListedIps = array_unique($validBlackListedIps, SORT_REGULAR);
                }
            }

            //check blacklisted countries
            if(!empty($blacklistedCountries)){
                $blacklistedCountries = explode(',', str_replace(' ','',$blacklistedCountries));
            }else{
                $blacklistedCountries = [];
            }

            $this->updateCustomSlug('file', $fileLinkSlug);
            $this->updateCustomSlug('bucket', $bucketLinkSlug);

            $configData = [
                'analytics_system' => $analyticSystem,
                'is_visit_info_required' => $isVisitsInfoRequired,
                'blacklisted_ips' => Help::toJson($validBlackListedIps),
                'blacklisted_countries' => Help::toJson($blacklistedCountries)
            ];

            $this->updateConfig($configData);
            $this->addAlert('General settings updated successfully', 'success');
        }

        Help::redirect('cpanel/settings');

    }

    /**
     * Cloud upload settings action
     */
    protected function cloudUpload(){

        if(Request::isPost()){

            $maxUploadProcess = Request::post('max_upload_processes');
            $uploadChunkSize = Request::post('upload_chunk_size');
            $autoReUpload = Request::post('auto_re_upload') == 'on' ? 1 : 0;


            //check maximum num of upload process
            if(is_numeric($maxUploadProcess)){
                if($maxUploadProcess < 1 || $maxUploadProcess > 5){
                    $this->addAlert('Maximum upload process must be between 1 and 5', 'warning');
                    $maxUploadProcess = 2;
                }
            }else{
                $maxUploadProcess = 2;
            }

            //check upload chunk size
            if(is_numeric($uploadChunkSize)){
                if($uploadChunkSize < 1 || $uploadChunkSize > 25){
                    $this->addAlert('chunk size must be between 1 - 25', 'warning');
                    $uploadChunkSize = 15;
                }
            }else{
                $uploadChunkSize = 15;
            }

            $configData = [
                'max_upload_process' => $maxUploadProcess,
                'upload_chunk_size' => $uploadChunkSize,
                'file_auto_re_upload' => $autoReUpload
            ];

             //finally update config
            $this->updateConfig($configData);

            $this->addAlert('Cloud upload settings updated successfully', 'success');

        }

        Help::redirect('cpanel/settings');


    }

    /**
     * Cloud file action
     */
    protected function cloudFile(){

        if(Request::isPost()){

            $fileRename = Request::post('file_op_rename') == 'on' ? 1 : 0;
            $fileMove = Request::post('file_op_move') == 'on' ? 1 : 0;

            $folderCreate = Request::post('folder_op_create') == 'on' ? 1 : 0;
            $folderRename = Request::post('folder_op_rename') == 'on' ? 1 : 0;
            $folderMove = Request::post('folder_op_move') == 'on' ? 1 : 0;

            $fileCheckTime = Request::post('file_check_time');

            if(!is_numeric($fileCheckTime) || empty($fileCheckTime)){
                $fileCheckTime = 24;
            }

            $configData = [
                'file_op_rename' => $fileRename,
                'file_op_move' => $fileMove,
                'folder_op_create' => $folderCreate,
                'folder_op_rename' => $folderRename,
                'folder_op_move' => $folderMove,
                'file_check_time' => $fileCheckTime,
            ];

            //finally update config
            $this->updateConfig($configData);

            $this->addAlert('Cloud file settings updated successfully', 'success');

        }

        Help::redirect('cpanel/settings');

    }

    protected function account(){

        if(Request::isPost()){


            $adminName = Request::post('admin_name');
            $username = Request::post('login_username');
            $oldPassword = Request::post('old_login_password');
            $newPassword = Request::post('new_login_password');
            $confirmPassword = Request::post('confirm_password');


            $validation = new Validation();
            $validation
                ->name('admin name')
                ->value($adminName)
                ->min(4)
                ->pattern('text')
                ->required();

            $validation
                ->name('username')
                ->value($username)
                ->pattern('text')
                ->required();

            if(!empty($newPassword)){

                $validation
                    ->name('password')
                    ->value($newPassword)
                    ->min(4)
                    ->required();

                $validation
                    ->name('confirm password')
                    ->value($confirmPassword)
                    ->equal($newPassword)
                    ->required();

                $validation
                    ->name('old password')
                    ->value($oldPassword)
                    ->required();

            }


            if($validation->isSuccess()){

                $configData = [
                    'real_monster_name' => $adminName,
                    'login_username' => $username
                ];

                $isUsernameUpdated = !Login::isValidUsername($username);

                //validate password
                if(!empty($newPassword)){

                    if(Login::isValidPassword($oldPassword)){

                        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                        $configData['login_password'] = $hashedPassword;

                    }else{
                        $this->addAlert('The old password is not correct');
                    }

                }

                $this->updateConfig($configData);

                //update username
                if($isUsernameUpdated){
                    $login = new Login($username);
                    $login->remembered();
                    if($login->check()->isValid()){
                        $login->initSession();
                    }
                }

                $this->addAlert('Account settings updated successfully', 'success');


            }else{

                $this->addAlert($validation->getErrors()[0]);

            }






        }

        Help::redirect('cpanel/settings');

    }


}