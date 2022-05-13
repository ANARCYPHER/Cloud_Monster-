<?php

/*
   +------------------------------------------------------------------------+
   | CloudMonster - Handle multi-Cloud Storage in parallel
   | Copyright (c) 2021 PHPCloudMonster. All rights reserved.
   +------------------------------------------------------------------------+
   | @author John Antonio
   | @author_url 1: https://phpcloudmonster.com
   | @author_url 2: https://www.codester.com/johnanta
   | @author_email: johnanta89@gmail.com
   +------------------------------------------------------------------------+
*/

namespace CloudMonster\Models;

use CloudMonster\App;
use CloudMonster\Core\Cookie;
use CloudMonster\Core\Session;
use CloudMonster\Helpers\Help;


/**
 * Class Login
 * @author John Antonio
 * @package CloudMonster\Models
 */
class Login{

    /**
     * cpanel login username
     * @var string
     */
    private string $username;

    /**
     * cpanel login password
     * @var string
     */
    private string $password;

    /**
     * logged user's active session token
     * @var string
     */
    private string $sessToken;

    /**
     * Remembered cookie expiring time
     * @var int
     */
    private int $loginCachedExpire = 60 * 60 * 24 * 7; // 7 days

    /**
     * Login remembered or not
     * @var bool
     */
    private bool $isRemembered = false;

    /**
     * Is valid login
     * @var bool
     */
    private bool $isValid = false;


    /**
     * Login constructor.
     * @param string $username cpanel login username
     * @param string $password cpanel login password
     */
    public function __construct( string $username = '', string $password = ''){

        $this->username = $username;
        $this->password = $password;

    }


    /**
     * Check login details
     * @return $this
     */
    public function check(): static
    {

        //check username
        if(
            $this::isValidUsername($this->username) &&
            ($this->isRemembered || $this::isValidPassword($this->password))
        ){

            $this->sessToken = Help::encrypt($this->username);
            $this->isValid = true;
        }

        return $this;

    }

    /**
     * Init logged session
     */
    public function initSession(){
        if($this->isValid){
            Session::set('login', 1);
            Session::set('loginToken', $this->sessToken);
        }
    }

    /**
     * Check login is valid or not
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * Remember user login
     * @param $remToken
     */
    public function remember($remToken){

        if(isset($this->sessToken)){

            //login token format (REMEMBER_TOKEN::USERNAME)
            $loginToken = Help::encrypt($remToken . '::' . $this->username);

            Cookie::set('login_rem', 1, $this->loginCachedExpire);
            Cookie::set('rem_token', $loginToken, $this->loginCachedExpire);

            $this->remembered();

        }

    }

    /**
     * login remembered
     */
    public function remembered(){

        $this->isRemembered = true;

    }

    /**
     * Attempt to check user login is remembered
     * @return bool
     */
    public static function isRemembered() : bool{
        $isRemembered = false;
        if(Cookie::get('login_rem') == 1){
            $loginToken = Help::decrypt(Cookie::get('rem_token'));
            if(!empty($loginToken)){
                $tokenData = explode('::', $loginToken);
                if(count($tokenData) == 2){
                    //verify remember token
                    $remToken = $tokenData[0];
                    if(APP::getConfig('login_remember_token') == $remToken){
                        //verify username
                        $username = $tokenData[1];
                        if(self::isValidUsername($username)){
                            $tmpLogin = new self($username);
                            $tmpLogin->remembered();
                            if($tmpLogin->check()->isValid()){
                                $tmpLogin->initSession();
                                $isRemembered = true;
                            }
                        }
                    }
                }
            }

        }

        return $isRemembered;

    }


    /**
     * Validate cpanel login username
     * @param $username
     * @return bool valid or invalid
     */
    public static function isValidUsername($username): bool
    {
        return App::getConfig('login_username') == $username;
    }

    /**
     * Validate cpanel login password
     * @param $password
     * @return bool valid or invalid
     */
    public static function isValidPassword($password): bool
    {
        return password_verify($password, App::getConfig('login_password'));
    }

    /**
     * Check user has logged successfully
     * @return bool
     */
    public static function isLogged(): bool
    {

        if(Session::get('login') === 1){

            $loginToken = Session::get('loginToken');
            $username = Help::decrypt($loginToken);

            if(self::isValidUsername($username)){

                return true;

            }

        }

        return false;

    }

    /**
     * Logout user
     */
    public static function logout(){

        if(self::isLogged()){

            Session::delete(['login', 'loginToken']);
            Cookie::delete('login_rem');
            Cookie::delete('rem_token');

        }

    }

}