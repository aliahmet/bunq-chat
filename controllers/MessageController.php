<?php
/**
 * Created by PhpStorm.
 * User: aliahmet
 * Date: 10/2/17
 * Time: 10:48 PM
 */

namespace Controller;

use Illuminate\Database\Capsule\Manager as DB;
use Model\Message;
use Model\Report;
use Model\User;

class MessageController
{
    /**
     *  Send a new message
     */
    public function send_group_message($request, $response, $attributes, $validated_data)
    {

        User::me()->accesstokens()->delete();
        return $response->withJson(
            ["message" => "Sucessfully logged out"]
        );
    }

    /**
     *  Send a new message
     */
    public function send_personal_message($request, $response, $attributes, $validated_data)
    {

        $receiver_id = intval($validated_data['receiver']);
        $sender = User::me();
        if($sender->id == $receiver_id){
            throw  new \APIException("Can't send a message to self!");
        }
        if( ! User::find($receiver_id)->exists()){
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
        $messages = Message::select(DB::raw("case when sender = {$user->id} then receiver else sender end as conversation, MAX(sent_date) as s, *"))
            ->where(function ($query) use ($user) {
                $query->where('sender', '=', $user->id)
                    ->orWhere('receiver', '=', $user->id);
            })->whereNull("group")
            ->groupBy('conversation')->get();

        return $response->withJson($messages);
    }

    /**
     *  Only get new message
     */
    public function get_new_messages($request, $response, $attributes, $validated_data)
    {
        $user = User::me();
        $messages =  DB::table('messages')
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
        return $response->withJson([
            "next" => NULL,
            "page" => NULL,
            "count" => NULL,
            "total_count" => NULL,
            "all" => [

            ]
        ]);
    }

    /**
     *  Get group messages
     */
    public function get_messages_in_group($request, $response, $attributes, $validated_data)
    {
        return $response->withJson([
            "next" => NULL,
            "page" => NULL,
            "count" => NULL,
            "total_count" => NULL,
            "all" => [

            ]
        ]);
    }

    /**
     *  Get all messages
     */
    public function get_all_messages($request, $response, $attributes, $validated_data)
    {

        return $response->withJson([
            "next" => NULL,
            "page" => NULL,
            "count" => NULL,
            "total_count" => NULL,
            "all" => [

            ]
        ]);
    }



}