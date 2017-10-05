<?php
/**
 * Created by PhpStorm.
 * User: aliahmet
 * Date: 10/2/17
 * Time: 10:48 PM
 */

namespace Controller;

use \APIException;
use \Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Model\Message;
use Model\Report;
use Model\User;

class MessageController
{
    /**
     *  Send a new group message
     *
     * $sample_response
     * {
     *    "message" : "No sample message"
     * }
     * sample_response$
     *
     */
    public function send_group_message($request, $response, $attributes, $validated_data)
    {
        throw new APIException("Not Implemented");
    }

    /**
     *  Send a new personal message
     *
     * $sample_response
     * {
     *    "message" : "ok"
     * }
     * sample_response$
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

        return $response->withJson(["message" => "ok"], 201);

    }

    /**
     *  Get cover messages
     *
     * $sample_response
     * [
     *     {
     *       "sender": "1",
     *       "receiver": "2",
     *       "reports": {},
     *       "date_sent": "2017-10-03 12:54:31",
     *       "body": "🦄",
     *       "group": null
     * },
     * {
     *       "sender": "3",
     *       "receiver": "1",
     *       "reports": {},
     *       "date_sent": "2017-10-03 12:54:31",
     *      "body": "FROM selimm 4",
     *      "group": null
     *   }
     * ]
     * sample_response$
     */
    public function get_last_messages($request, $response, $attributes, $validated_data)
    {
        $user = User::me();
        $messages = Message::selectRaw("case when sender = {$user->id} then receiver else sender end as conversation, MAX(sent_date) as s, *")
            ->where(function ($query) use ($user) {
                $query->where('sender', '=', $user->id)
                    ->orWhere('receiver', '=', $user->id);
            })->whereNull("group")
            ->groupBy('conversation')->get();

        return $response->withJson($messages);
    }

    /**
     *  Get messages that weren't already delivered
     *
     * $sample_response
     * [
     *     {
     *       "id": "6",
     *       "sender": "3",
     *       "receiver": "1",
     *       "group": null,
     *       "sent_date": "2017-10-03 12:54:31",
     *       "body": "FROM selimm",
     *       "delivered_date": null,
     *       "seen_date": null,
     *       "user_id": "1",
     *       "message_id": "6"
     *     },
     *     {
     *       "id": "9",
     *       "sender": "3",
     *       "receiver": "1",
     *       "group": null,
     *       "sent_date": "2017-10-03 12:56:59",
     *       "body": "FROM selimm 3",
     *       "delivered_date": null,
     *       "seen_date": null,
     *       "user_id": "1",
     *       "message_id": "9"
     *     },
     * ]
     * sample_response$
     */
    public function get_new_messages($request, $response, $attributes, $validated_data)
    {
        $user = User::me();
        $messages = DB::table('messages')
            ->leftJoin('reports', 'messages.id', '=', 'reports.message_id')
            ->where("reports.user_id", "=", $user->id)
            ->whereNull("reports.delivered_date");

        Report::mark_as_delivered($messages, $user);
        return $response->withJson($messages);
    }


    /**
     *  Get all the messages with a user (This response is paginated)
     *
     * $sample_response
     *    {
     *      "page": 1,
     *      "count": 3,
     *      "total_count": 26,
     *      "result": [
     *        {
     *          "sender": "1",
     *          "receiver": "2",
     *          "date_sent": "2017-10-03 12:54:31",
     *          "delivered_date": null,
     *          "body": "🦄",
     *          "group": null
     *        },
     *        {
     *          "sender": "1",
     *          "receiver": "2",
     *          "date_sent": "2017-10-03 12:54:31",
     *          "delivered_date": null,
     *          "body": "This is some message5",
     *          "group": null
     *        },
     *        {
     *          "sender": "1",
     *          "receiver": "2",
     *          "date_sent": "2017-10-03 12:54:31",
     *          "delivered_date": null,
     *          "body": "This is some message4",
     *          "group": null
     *        }
     *      ]
     *    }
     * sample_response$
     */
    public function get_messages_with_user($request, $response, $attributes, $validated_data)
    {
        $other_user = User::find(intval($attributes['user_id']));
        $user = User::me();

        // Simple checks
        if (!$other_user) {
            throw new \APIException("There is no such user!");
        }
        if ($other_user->id == $user->id) {
            throw new \APIException("No chat with self!");
        }

        // Retrieve conversation
        $all_messages = Message::betweenUsers($user, $other_user);

        if ($validated_data['only_new'])
            $all_messages->whereNull("reports.delivered_date");

        $total = $all_messages->count();

        // Paginate
        $page = intval(get_or_default($validated_data['page'], 1));
        $messages = $all_messages->limit(10)->offset(($page - 1) * 10);
        $count = $messages->cloneWithout([])->count();

        // Set messages delivered
        Report::mark_as_delivered($messages, $user);


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
     *  Get all the messages with in a Group (This response is paginated)
     *
     * $sample_response
     *    {
     *      "page": 1,
     *      "count": 3,
     *      "total_count": 26,
     *      "result": [
     *        {
     *          "sender": "1",
     *          "receiver": "",
     *          "date_sent": "2017-10-03 12:54:31",
     *          "delivered_date": null,
     *          "body": "Hi guys!",
     *          "group": 2
     *        },
     *        {
     *          "sender": "1",
     *          "receiver": "",
     *          "date_sent": "2017-10-03 12:54:31",
     *          "delivered_date": null,
     *          "body": "Hi !",
     *          "group": 2
     *        },
     *        {
     *          "sender": "1",
     *          "receiver": "",
     *          "date_sent": "2017-10-03 12:54:31",
     *          "delivered_date": null,
     *          "body": "How are you ?",
     *          "group": 3
     *        }
     *      ]
     *    }
     * sample_response$
     */
    public function get_messages_in_group($request, $response, $attributes, $validated_data)
    {

        $group_id = $attributes['group_id'];
        $user = User::me();
        $group = $user->groups->find($group_id);

        if (!$group) {
            throw new APIException("Group doesn't exist or you are not allowed to see it!");
        }

        // Retrieve conversation

        $all_messages = Message::inGroup($user, $group);

        if ($validated_data['only_new'])
            $all_messages->whereNull("reports.delivered_date");

        $total = $all_messages->count();

        // Paginate
        $page = intval(get_or_default($validated_data['page'], 1));
        $messages = $all_messages->limit(10)->offset(($page - 1) * 10);
        $count = $messages->cloneWithout([])->count();

        // Set messages delivered
        Report::mark_as_delivered($messages, $user);


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
     *  Get all the messages (This response is paginated)
     *
     * $sample_response
     *    {
     *      "page": 1,
     *      "count": 3,
     *      "total_count": 26,
     *      "result": [
     *        {
     *          "sender": "1",
     *          "receiver": "2",
     *          "date_sent": "2017-10-03 12:54:31",
     *          "delivered_date": null,
     *          "body": "🦄",
     *          "group": null
     *        },
     *        {
     *          "sender": "1",
     *          "receiver": "2",
     *          "date_sent": "2017-10-03 12:54:31",
     *          "delivered_date": null,
     *          "body": "This is some message 5",
     *          "group": null
     *        },
     *        {
     *          "sender": "1",
     *          "receiver": "2",
     *          "date_sent": "2017-10-03 12:54:31",
     *          "delivered_date": null,
     *          "body": "This is some message 4",
     *          "group": null
     *        }
     *      ]
     *    }
     * sample_response$
     */
    public function get_all_messages($request, $response, $attributes, $validated_data)
    {
        $user = User::me();

        // Get retrieve conversation

        $all_messages = DB::table('messages')
            ->leftJoin('reports', 'messages.id', '=', 'reports.message_id')
            ->where("reports.user_id", "=", $user->id);

        if ($validated_data['only_new'])
            $all_messages->whereNull("reports.delivered_date");

        $total = $all_messages->count();

        // Paginate
        $page = intval(get_or_default($validated_data['page'], 1));
        $messages = $all_messages->limit(10)->offset(($page - 1) * 10);
        $count = $messages->cloneWithout([])->count();

        // Set messages delivered
        Report::mark_as_delivered($messages, $user);


        return $response->withJson([
            "page" => $page,
            "count" => $count,
            "total_count" => $total,
            "result" => array_map(function ($message) {
                return [
                    "id" => $message->id,
                    "sender" => $message->sender,
                    "receiver" => $message->receiver,
                    "date_sent" => $message->date_sent,
                    "delivered_date" => $message->delivered_date,
                    "seen_date" => $message->seen_date,
                    "body" => $message->body,
                    "group" => $message->group,
                ];
            }, $messages->get()->toArray())
        ]);

    }


    /**
     *  Mark message as read
     *
     * $sample_response
     * {
     *    "message" : "ok"
     * }
     * sample_response$
     */
    public function mark_message_as_read($request, $response, $attributes, $validated_data)
    {
        $message_id = $validated_data['message_id'];
        $user = User::me();
        $message = DB::table('messages')
            ->selectRaw("messages.*, reports.*, reports.id as r_id")
            ->leftJoin('reports', 'messages.id', '=', 'reports.message_id')
            ->where("reports.user_id", "=", $user->id)
            ->where("messages.id", "=", $message_id)
            ->whereNull("reports.seen_date")
            ->first();


        if (!$message) {
            throw new \APIException([
                "message" => "You cant perform this action because because of one or more reasons below.",
                "reasons" => [
                    "Message doesn't exist",
                    "Message is not meant for you",
                    "Message is already seen"
                ]
            ]);
        }
        $report = Report::find($message->r_id);
        $report->seen_date = Carbon::now();
        $report->save();


        return $response->withJson(["message" => "ok"]);
    }


}