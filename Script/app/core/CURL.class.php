<?php

namespace CloudMonster\Core;

use CloudMonster\Helpers\Help;

class CURL{


    protected $ch = null;
    protected $cookizFile = '';
    protected $httpCode = 404;

    protected string $content = '';
    protected string $cookizPath = '';
    public int $timeout = 15;
    public bool $cooizJar = false;
    protected string $url;
    public bool $proxy = false;
    protected $progressObj = null;
    public bool $noBody = false;
    public bool $header = false;
    public int $bufferSize = 200 * 1024;
    public  $file = null;
    protected string $progressCallback;
    protected array $customOptions = [];



    public function __construct(){
        $this->ch = curl_init();
        $this->cookizPath = Help::storagePath('cookiz');
    }

    public function get($url): static
    {
        $this->url = $url;
       return $this;
    }

    public function post($url, $data=[]): static
    {
      
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        $this->url = $url;
        return $this;
    }

    public function put($url): static
    {
        $this->url = $url;
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        return $this;
    }

    public function patch($url): static
    {
        $this->url = $url;
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        return $this;
    }

    public function delete($url): static
    {
        $this->url = $url;
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this;
    }

    public function setOpt($opt, $val){
        array_push($this->customOptions, [
            'opt' => $opt,
            'val' => $val
        ]);
    }

    public function beforeExec(){

        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->ch, CURLOPT_CAINFO, NULL);
        curl_setopt($this->ch, CURLOPT_CAPATH, NULL);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($this->ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->getUserAgent());
        curl_setopt($this->ch, CURLOPT_BUFFERSIZE , $this->bufferSize);

        if($this->proxy){
            if($proxy = '23.254.91.105:9146'){
                curl_setopt($this->ch, CURLOPT_PROXY, $proxy);
                curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, 'oedulrvd:eeb7jsudnu4a');
                curl_setopt($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            }

        }

        if($this->header){
            curl_setopt($this->ch, CURLOPT_HEADER, true);
        }

        if($this->noBody){
            curl_setopt($this->ch, CURLOPT_NOBODY, true);
        }

        if(!empty($this->cookizFile)){
            if($this->cooizJar) {
                curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookizFile);
            }else{
                curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookizFile);
            }
        }

        if($this->progressObj !== null){
            curl_setopt($this->ch, CURLOPT_NOPROGRESS, false);
            curl_setopt($this->ch, CURLOPT_PROGRESSFUNCTION, [$this->progressObj, $this->progressCallback]);
            if(method_exists($this->progressObj, 'start')){
                $this->progressObj->start();
            }
        }

        if(!empty($this->file)){
            curl_setopt($this->ch, CURLOPT_FILE, $this->file);
        }

        if(!empty($this->customOptions)){
            foreach ($this->customOptions as $opt){
                curl_setopt($this->ch, $opt['opt'], $opt['val']);
            }
        }


    }



    public function setCookieFile($filename){
        $this->cookizFile = $this->cookizPath . '/' . $filename . '.txt' ;
    }

    public function setHeaders($headers = []){
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
    }

    public function setProxy(){
        
    }

    
    public function getCookie(){

    }

    public function getCookieFile(){
        return $this->cookizFile;
    }

    public function getUserAgent(){
        return 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36 Edg/90.0.818.49';
    }

    public function exec(){
        $this->beforeExec();


        $this->content = curl_exec($this->ch);
        $this->afterExec();
    }

    public function afterExec(){
        $this->httpCode =  $this->getInfo(CURLINFO_HTTP_CODE);

        //clear progress obj
        if($this->progressObj !== null){
            if(method_exists($this->progressObj, 'close')){
                $this->progressObj->close();
                unset($this->progressObj);
            }
        }
    }

    public function getInfo($opt = ''){
        return empty($opt) ? curl_getinfo($this->ch) : curl_getinfo($this->ch,$opt);
    }

    public function getHtppCode(){
        return $this->httpCode;
    }

    public function getError(){
        $httpCodes = Help::getHttpStatusCodes();
        if(array_key_exists($this->httpCode, $httpCodes)){
            return 'CURL ERROR : ' . $httpCodes[$this->httpCode] . ' -> ' . $this->url;
        }
        return 'CURL Unknow Error ! ' . $this->url ;
    }

    public function isOk(){
        return $this->httpCode == 200 && !empty($this->getResponse());
    }

    public function getResults(){
        $content = $this->content;
        if(!empty($content) && Help::isJson($content)){
            $content = Help::toArray($content);
        }else{
            $content = [];
        }
        return $content;
    }

    public function getResponse(){
        return $this->content;
    }

    public function progress($obj, $callback = 'CurlProgressCallback'){
        $this->progressObj = null;
        if(is_object($obj)){
            if(method_exists($obj, $callback)){
                $this->progressCallback = $callback;
                if(method_exists($obj, 'wait')) $obj->wait();
                $this->progressObj = $obj;
            }
        }
    }



    public function close(){
        curl_close($this->ch);
        $this->ch = null;
    }
    

    public function __destruct(){
        if($this->ch !== null){
            curl_close($this->ch);
        }
    }
    


}