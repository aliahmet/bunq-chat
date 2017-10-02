<?php
/**
 * Created by PhpStorm.
 * User: aliahmet
 * Date: 10/1/17
 * Time: 11:59 PM
 */

namespace Model;


use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    public $timestamps = false;

    public function random_token()
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $max = strlen($codeAlphabet); // edited

        for ($i=0; $i < 32; $i++) {
            $token .= $codeAlphabet[random_int(0, $max-1)];
        }

        $this->accesstoken =  $token;
        return $token;
    }

    public static function generate($user)
    {
        $token = new AccessToken();
        $token->user_id = $user->id;
        $token->random_token();
        $token->save();
        return $token;
    }

    public function user()
    {
        return $this->belongsTo('\Model\User', 'user_id', 'id')->first();
    }

    public static  function current(){
        $accesstoken = get_or_default($_SERVER['HTTP_AUTHORIZATION'], "-");

        return AccessToken::where("accesstoken", "=", $accesstoken)->first();
    }

}