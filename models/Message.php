<?php
/**
 * Created by PhpStorm.
 * User: aliahmet
 * Date: 10/2/17
 * Time: 2:28 PM
 */

namespace Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class Message extends Model
{
    public $timestamps = false;
    protected $hidden = ['pivot'];

    public static function betweenUsers($user, $other_user){
        return DB::table('messages')
            ->whereRaw(" ( messages.sender={$other_user->id} AND messages.receiver={$user->id} ) OR ( messages.sender={$user->id} AND messages.receiver={$other_user->id} )")
            ->leftJoin('reports', 'messages.id', '=', 'reports.message_id')
            ->orderBy("messages.id","DESC");
    }


    public function group()
    {
        return $this->belongsTo('\Model\Group');
    }

    public function toArray($options = 0)
    {
        $reports = Report::where("message_id", "=", $this->id);
        return [
            "sender" => $this->sender,
            "receiver" => $this->receiver,
            "reports" => $reports,
            "date_sent" => $this->date_sent,
            "body" => $this->body,
            "group" => $this->group,
        ];
    }


}