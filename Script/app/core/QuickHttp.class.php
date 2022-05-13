<?php

namespace CloudMonster\Core;


class QuickHttp{

    public static bool $isResponse = false;

    public static function request($method = 'get', $url = '', $data = []){

        $resp = [];

        $queryData = isset($data['query']) ? $data['query'] :  [];
        $postData = isset($data['postData']) ? $data['postData'] : [];
        $cookieFile = isset($data['cookie']) ? $data['cookie'] : '';
        $cookieJar = isset($data['cookieJar']) && $data['cookieJar'] == true;
        $timeout = isset($data['timeout']) && is_numeric($data['timeout']) ?  $data['timeout'] : 15;

        $url = strtok($url, '?') . '?' . http_build_query($queryData);

        $curl = new Curl;

        //set cookies
        if(!empty($cookieFile))
            $curl->setCookieFile($cookieFile);

        if($cookieJar)
            $curl->cooizJar = true;

        //set timeout
        $curl->timeout = $timeout;

        $curl->{$method}($url, $postData)->exec();

        if($curl->isOk()){
            $resp = !self::$isResponse ? $curl->getResults() : $curl->getResponse();
        }

        self::$isResponse = false;
        $curl->close();

        return $resp;

    }

    public static function isExist(){

    }


    public static function response(): QuickHttp
    {
        self::$isResponse = true;
        return new self;
    }


}