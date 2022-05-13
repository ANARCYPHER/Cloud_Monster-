<?php

namespace CloudMonster\Core;


class Cookie{


    public static function set($name, $value, int $expire = 0, $path = '/'){
        $expire = time() + $expire;
        setcookie($name, $value, $expire, $path);
    }

    public static function isset($name){
        if(isset($_COOKIE[$name])){
            return true;
        }
        return false;
    }

    public static function delete($name){
        if(isset($_COOKIE[$name])){
            setcookie($name, "", time() - 3600, '/');
        }
    }

    public static function get($name){
        if(isset($_COOKIE[$name])){
            return $_COOKIE[$name];
        }
        return '';
    }


}