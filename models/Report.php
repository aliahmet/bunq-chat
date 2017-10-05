<?php
/**
 * Created by PhpStorm.
 * User: aliahmet
 * Date: 10/2/17
 * Time: 2:29 PM
 */

namespace Model;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $timestamps = false;
    protected $hidden = ['pivot'];


    public static function mark_as_delivered($messages, $user){
        $report_ids = $messages->cloneWithout([])
            ->whereNull("delivered_date")
            ->where("reports.user_id", "=", $user->id)
            ->pluck('reports.id')
            ->toArray();
        $messages = $messages->get();
        Report::whereIn("id", $report_ids)->update(["delivered_date" => Carbon::now()]);
        return $messages;
    }


}