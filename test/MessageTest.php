<?php

/**
 * Created by PhpStorm.
 * User: aliahmet
 * Date: 10/5/17
 * Time: 1:27 AM
 */
class MessageTest extends BaseTestCase
{

    use UseUsersTrait;

    function test_send_personal_message(){
        $this->create_users();

        $data = [
            "body" => "Hello!",
            "receiver" => 2
        ];
        $reponse = $this->post("/message/new/user/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);

    }

    function test_send_personal_message_to_self(){
        $this->create_users();

        $data = [
            "body" => "Hello!",
            "receiver" => 1
        ];
        $reponse = $this->post("/message/new/user/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 403, $reponse->body);

    }

    function test_get_new_messages(){
        $this->create_users();


        # Send From 2 to 1
        $data = [
            "body" => "Hello!",
            "receiver" => 1
        ];
        $reponse = $this->post("/message/new/user/", $this->user_headers_2, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);


        # Send From 1 to 2
        $data = [
            "body" => "Hello!",
            "receiver" => 2
        ];
        $reponse = $this->post("/message/new/user/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);

        # 1 new message
        $reponse = $this->get("/message/new/", $this->user_headers_2, $data);
        $messages = json_decode($reponse->body, true);
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(1, count($messages), $reponse->body);

        # No new message
        $reponse = $this->get("/message/new/", $this->user_headers_2, $data);
        $messages = json_decode($reponse->body, true);
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(0, count($messages), $reponse->body);

    }
}