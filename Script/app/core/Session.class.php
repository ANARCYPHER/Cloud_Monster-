<?php


namespace CloudMonster\Core;


class Session{

    public static function create(){
        
    }

    public static function delete($key){
        if(is_array($key)){
            foreach ($key as $k){
                if(isset($_SESSION[$k])){
                    unset($_SESSION[$k]);
                }
            }
        }else{
            if(isset($_SESSION[$key])){
                unset($_SESSION[$key]);
            }
        }
    }

    public static function isset(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function set($key, $val = ''){
        if(is_array($key)){
            foreach ($key as $k => $v){
                $_SESSION[$k] = $v;
            }
        }else{
            $_SESSION[$key] = $val;
        }

    }

    public static function get(string $key){
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }
        return '';
    }

}