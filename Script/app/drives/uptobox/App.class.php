<?php



namespace CloudMonster\Drives\Uptobox;

use CloudMonster\Core\QuickHttp;
use CloudMonster\Drives\BaseController;
use CloudMonster\Helpers\Help;
use CloudMonster\models\CloudDrives;
use Exception;


class App extends BaseController {

    protected string $baseUrl = 'https://uptobox.com/api';

    public function __construct(CloudDrives $baseDrive){
        parent::__construct($baseDrive);
        $this->authErrorIdentity = ['Invalid token'];
    }

    public function connect()
    {
        //attempt to load parent controller
        $this->loadParent();

    }


    /**
     * @throws Exception
     */
    public function getAccountInfo()
    {

      try{

         return $this->call('get', '/user/me');

      }catch(Exception $e){

          $this->addError($e->getMessage());

      }

      return [];

    }

    protected function appendAuthData($params = []){
        $authData = $this->baseDrive->getAuthData();
        if(!empty($authData) && Help::isJson($authData)){
            $authData = Help::toArray($authData);
            if(!isset($params['query'])) $params['query'] = [];
            $params['query'] = array_merge($params['query'], $authData);
            if(isset($params['postData'])){
                $params['postData'] = array_merge($params['postData'], $authData);
            }
        }
        return $params;
    }

    protected function getApiUrl($path = ''): string
    {
        return $this->baseUrl. $path;
    }


    /**
     * @throws Exception
     */
    public function call($method = 'get', $path = '', $data = [], $isFilter = true){

        $results = [];

        $url = $this->getApiUrl($path);
        $data = $this->appendAuthData($data);
        $resp =  QuickHttp::request($method, $url, $data);
        $error = '';

        if(!empty($resp)){

            if(!empty($resp['data'])){
                $results = $isFilter ? $resp['data'] : $resp;
            }
            if(isset($resp['message']) && $resp['message'] != 'Success'){
                $error = !empty($resp['data']) ? $resp['data'] : $resp['message'];
            }
            if(isset($resp['success']) && empty($resp['success'])){
                $error = $resp['data'];
            }

        }else{
            $error = 'Empty response';
        }

        if(!empty($error)){
            throw new Exception($error);
        }

        return $results;

    }



}