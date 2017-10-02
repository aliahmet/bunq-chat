<?php
/**
 * Created by PhpStorm.
 * User: aliahmet
 * Date: 10/2/17
 * Time: 10:48 PM
 */

namespace Controller;

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

        $validated_data = \Http\Validator::check([
            "receiver" => ["rules" => ["numeric"]],
            "body" => [],
        ]);
        if (!key_exists("receiver", $validated_data) | !key_exists("group", $validated_data)) {
            throw new APIException("message target must be specified via 'group' or 'reciever' ");
        }

        User::me()->accesstokens()->delete();
        return $response->withJson(
            ["message" => "Sucessfully logged out"]
        );
    }

}