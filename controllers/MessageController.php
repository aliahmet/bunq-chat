<?php
/**
 * Created by PhpStorm.
 * User: aliahmet
 * Date: 10/2/17
 * Time: 10:48 PM
 */

namespace Controller;

use \APIException;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Model\Message;
use Model\Report;
use Model\User;

class MessageController
{
    /**
     *  Send a new group message
     */
    public function send_group_message($request, $response, $attributes, $validated_data)
    {
        throw new APIException("Not Implemented");
    }

    /**
     *  Send a new personal message
     */
    public function send_personal_message($request, $response, $attributes, $validated_data)
    {
        $receiver_id = intval($validated_data['receiver']);
        $sender = User::me();

        if ($sender->id == $receiver_id) {
            throw  new \APIException("Can't send a message to self!");
        }
        if (!User::find($receiver_id)->exists()) {
            throw  new \APIException("User doesn't exist!");

        }

        $message = new Message;
        $message->sender = $sender->id;
        $message->receiver = $receiver_id;
        $message->body = $validated_data['body'];
        $message->save();

        $report = new Report;
        $report->message_id = $message->id;
        $report->user_id = $receiver_id;
        $report->save();

        return $response->withJson(["message" => "ok"]);

    }

    /**
     *  Get last message of all personal conversations
     */
    public function get_last_messages($request, $response, $attributes, $validated_data)
    {
        $user = User::me();
        $messages = MessageDB::raw("case when sender = {$user->id} then receiver else sender end as conversation, MAX(sent_date) as s, *")
            ->where(function ($query) use ($user) {
                $query->where('sender', '=', $user->id)
                    ->orWhere('receiver', '=', $user->id);
            })->whereNull("group")
            ->groupBy('conversation')->get();

        return $response->withJson($messages);
    }

    /**
     *  Only get new messages
     */
    public function get_new_messages($request, $response, $attributes, $validated_data)
    {
        $user = User::me();
        $messages = DB::table('messages')
            ->leftJoin('reports', 'messages.id', '=', 'reports.message_id')
            ->where("reports.user_id", "=", $user->id)
            ->whereNull("reports.delivered_date")
            ->get();

        return $response->withJson($messages);
    }

    /**
     *  Get user conversations
     */
    public function get_messages_with_user($request, $response, $attributes, $validated_data)
    {
        $other_user = User::find(intval($attributes['id']));
        $user = User::me();

        // Simple checks
        if (!$other_user) {
            throw new \APIException("There is no such user!");
        }
        if ($other_user->id == $user->id) {
            throw new \APIException("No chat with self!");
        }

        // Get retrieve conversation
        $all_messages = Message::betweenUsers($user, $other_user);
        $total = $all_messages->count();

        // Paginate
        $page = intval(get_or_default($validated_data['page'], 1));
        $messages = $all_messages->limit(10)->offset(($page - 1) * 10);
        $count = $messages->cloneWithout([])->count();

        // Set messages delivered
        $report_ids = $messages->cloneWithout([])
            ->whereNull("delivered_date")
            ->where("reports.user_id", "=", $user->id)
            ->pluck('reports.id')
            ->toArray();
        Report::whereIn("id", $report_ids)->update(["delivered_date" => Carbon::now()]);


        return $response->withJson([
            "page" => $page,
            "count" => $count,
            "total_count" => $total,
            "result" => array_map(function ($message) {
                return [
                    "sender" => $message->sender,
                    "receiver" => $message->receiver,
                    "date_sent" => $message->date_sent,
                    "delivered_date" => $message->delivered_date,
                    "body" => $message->body,
                    "group" => $message->group,
                ];
            }, $messages->get()->toArray())
        ]);
    }

    /**
     *  Get group messages
     */
    public function get_messages_in_group($request, $response, $attributes, $validated_data)
    {
        throw new APIException("Not Implemented");
    }

    /**
     *  Get all messages
     */
    public function get_all_messages($request, $response, $attributes, $validated_data)
    {
        throw new APIException("Not Implemented");
    }


}