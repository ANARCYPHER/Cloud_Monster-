<?php

namespace CloudMonster\Core;

use CloudMonster\Helpers\Help;

/**
 * Class Request
 * @author John Anta
 * @package CloudMonster\Core
 */
class Request{

    /**
     * Get request method
     * @return mixed
     */
    public static function getMethod() : string{
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get data from GET request
     * @param $var
     * @return mixed|string
     */
    public static function get($var){
        return self::getData($var, 'get');
    }

    /**
     * Get data from POST request
     * @param $var
     * @return mixed|string
     */
    public static function post($var){
        return self::getData($var, 'post');
    }


    /**
     * Get requested data
     * @param $var
     * @param $type
     * @return mixed|string
     */
    protected static function getData($var, $type){
        $data = '';
        $type = "_". strtoupper($type);
        global ${$type};
        if(isset(${$type}[$var])){
           if(!is_array($$type[$var])){
            $data = Help::clean($$type[$var]);
           }else{
            $data = $$type[$var];
           }
        }
        return $data;
    }

    /**
     * Check POST request
     * @return bool
     */
    public static function isPost(){
        return self::getMethod() == 'POST';
    }



    /**
     * Check DELETE request
     * @return bool
     */
    public static function isDelete(){
        return self::getMethod() == 'DELETE';
    }

    /**
     * Get form data
     * @param array $allowed
     * @return array
     */
    public static function getFormData(array $allowed = []): array
    {
        $data = [];
        if(!empty($allowed)){
            foreach ($allowed as $val){
                $data[$val]  = self::post($val);
            }
        }
        return $data;
    }


    /**
     * Get header authentication data
     * @param string $var
     * @return string
     */
    public static function getAuth(string $var = ''): string
    {
        $headers = apache_request_headers();
        if(isset($headers[$var])){
            return trim($headers[$var]);
        }
        return '';
    }

}