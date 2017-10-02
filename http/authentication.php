<?php
namespace  Http;
use  Model\AccessToken;

class Auth {
    public static function mewwww(){
        $token = AccessToken::current();
        if($token)
            return $token->user();
    }

}