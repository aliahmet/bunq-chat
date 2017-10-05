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

    public static function betweenUsers($user, $other_user)
    {
        return DB::table('messages')
            ->whereRaw(" ( messages.sender={$other_user->id} AND messages.receiver={$user->id} ) OR ( messages.sender={$user->id} AND messages.receiver={$other_user->id} )")
            ->leftJoin('reports', 'messages.id', '=', 'reports.message_id')
            ->orderBy("messages.id", "DESC");
    }

    public static function inGroup($user, $group)
    {
        return DB::table('messages')
            ->leftJoin('reports', 'messages.id', '=', 'reports.message_id')
            ->where("reports.user_id", "=", $user->id)
            ->where("messages.group", "=", $group->id);
    }


    public function group()
    {
        return $this->belongsTo('\Model\Group');
    }

    public function toArray($options = 0)
    {
        $reports = Report::where("message_id", "=", $this->id);
        return [
            "id" => $this->id,
            "sender" => $this->sender,
            "receiver" => $this->receiver,
            "reports" => $reports,
            "date_sent" => $this->date_sent,
            "seen_date" => $this->seen_date,
            "body" => $this->body,
            "group" => $this->group,
        ];
    }


}