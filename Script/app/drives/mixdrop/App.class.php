<?php



namespace CloudMonster\Drives\Mixdrop;




use CloudMonster\Core\QuickHttp;
use CloudMonster\Drives\BaseController;
use CloudMonster\Helpers\Help;
use CloudMonster\models\CloudDrives;
use Exception;


class App extends BaseController {

    protected string $baseUrl = 'https://api.mixdrop.co';
    private array $authData = [];

    public function __construct(CloudDrives $baseDrive){
        parent::__construct($baseDrive);
        $this->authErrorIdentity = ['Invalid login'];
    }

    public function connect()
    {
        //attempt to load parent controller
        $this->loadParent();
        $this->setAuthData();
    }


    protected function setAuthData($params = []){
        $authData = $this->baseDrive->getAuthData();
        if(!empty($authData) && Help::isJson($authData)){
            $this->authData = Help::toArray($authData);

        }
    }

    public function getAuthData($t = ''){
        if(!empty($t)){
            return $this->authData[$t] ?? '';
        }
        return $this->authData;
    }

    protected function appendAuthData($params = []){
        if(!empty($this->authData)){
            if(!isset($params['query'])) $params['query'] = [];
            $params['query'] = array_merge($params['query'], $this->getAuthData());
            if(isset($params['postData'])){
                $params['postData'] = array_merge($params['postData'], $this->getAuthData());
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

        if(!empty($resp)){

            if($resp['success'] != '1'){
                $e = $resp['result']['msg'] ?? 'Unknown error';
                throw new Exception($e);
            }

            $results = $resp['result'];



        }else{
            throw new Exception('Empty response');
        }


        return $results;

    }



}