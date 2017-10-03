<?php
namespace Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    public $timestamps = false;
    protected $hidden = ['pivot', 'password'];

    public  function set_password($password){
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }
    public  function check_password($password){
        return password_verify($password, $this->password);
    }

    public static function me(){
        $token = AccessToken::current();
        if($token)
            return $token->user();
    }

    public function accesstokens()
    {
        return $this->hasMany('\Model\Accesstoken');
    }

    public function groups()
    {
        return $this->belongsToMany('\Model\Group', "group_user");
    }



}