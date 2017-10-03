<?php
/**
 * Created by PhpStorm.
 * User: aliahmet
 * Date: 10/2/17
 * Time: 2:30 PM
 */

namespace Model;


use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public $timestamps = false;

    protected $hidden = ['pivot'];



    public function users()
    {
        return $this->belongsToMany('\Model\User', "group_user");
    }

    public function  toArray($options=0){
        return [
            "name" => $this->name,
            "id" => $this->id,
            "members" =>$this->users()->select(["users.username", "users.id"])->get()
        ];
    }

    public function  messages($options=0)
    {
        return $this->hasMany('\Model\Message');
    }
}