<?php


namespace CloudMonster\Drives\Dropapk;


use CloudMonster\Core\QuickHttp;
use CloudMonster\Drives\BaseController;
use CloudMonster\Helpers\Help;
use CloudMonster\Models\CloudDrives;
use Google\Exception;


/**
 * Class App
 * @package CloudMonster\Drives\Dropapk
 */

class App extends BaseController {


    /**
     * Base api url
     * @var string
     */
    protected string $baseUrl = 'https://dropapk.to/api';


    /**
     * App constructor.
     * @param CloudDrives $baseDrive
     */
    public function __construct(CloudDrives $baseDrive){
        parent::__construct($baseDrive);
        $this->authErrorIdentity = ['Wrong auth'];
    }


    /**
     * @throws Exception
     */
    public function connect()
    {
        //attempt to load parent controller
        $this->loadParent();

    }


    /**
     * Get Account info
     * @return array
     */
    public function getAccountInfo() : array
    {
        $data = [];
        try{

            $data = $this->call('get', '/account/info');

            if(isset($data['storage_left'])){

                $storageLeft = $data['storage_left'];
                $storageUsed = $data['storage_used'];
                $storageTotal = $storageUsed + $storageLeft;

                $storage = [
                    'total' => Help::formatSizeUnits($storageTotal),
                    'used' => Help::formatSizeUnits($storageUsed),
                    'left' => Help::formatSizeUnits($storageLeft)
                ];

                unset($data['storage_left']);
                unset($data['storage_used']);

                $data['storage'] = $storage;

            }

        }catch (\Exception $e){

            $this->addError($e->getMessage());

        }

        return $data;

    }

    /**
     * Append auth data
     * @param array $params
     * @return array
     */
    protected function appendAuthData($params = []): array
    {
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

    /**
     * Get final api url
     * @param string $path
     * @return string
     */
    protected function getApiUrl(string $path = ''): string
    {
        return $this->baseUrl. $path;
    }


    /**
     * Call API
     * @param string $method
     * @param string $path
     * @param array $data
     * @param bool $isFilter
     * @return mixed
     * @throws \Exception
     */
    public function call(string $method = 'get', string $path = '', array $data = [], bool $isFilter = true): mixed
    {

        $results = [];

        $url = $this->getApiUrl($path);
        $data = $this->appendAuthData($data);
        $resp =  QuickHttp::request($method, $url, $data);

        if(!empty($resp)){
            if(!empty($resp['result'])){
                $results = $isFilter ? $resp['result'] : $resp;
            }
            if($resp['msg'] != 'OK'){
                throw new \Exception($resp['msg']);
            }
        }

        return $results;

    }



}