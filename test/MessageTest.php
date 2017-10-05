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

    function test_send_personal_message()
    {
        $this->create_users();

        $data = [
            "body" => "Hello!",
            "receiver" => 2
        ];
        $reponse = $this->post("/message/user/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);

    }

    function test_send_personal_message_to_self()
    {
        $this->create_users();

        $data = [
            "body" => "Hello!",
            "receiver" => 1
        ];
        $reponse = $this->post("/message/user/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 403, $reponse->body);

    }

    function test_get_new_messages()
    {
        $this->create_users();


        # Send From 2 to 1
        $data = [
            "body" => "Hello!",
            "receiver" => 1
        ];
        $reponse = $this->post("/message/user/", $this->user_headers_2, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);


        # Send From 1 to 2
        $data = [
            "body" => "Hello!",
            "receiver" => 2
        ];
        $reponse = $this->post("/message/user/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);


        # 1 new message
        $reponse = $this->get("/message/all/?only_new=true", $this->user_headers_2, $data);
        $messages = json_decode($reponse->body, true)['result'];
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(1, count($messages), $reponse->body);

        # No new message
        $reponse = $this->get("/message/all/?only_new=true", $this->user_headers_2, $data);
        $messages = json_decode($reponse->body, true)['result'];
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(0, count($messages), $reponse->body);


        # 1 total message
        $reponse = $this->get("/message/all/", $this->user_headers_2, $data);
        $messages = json_decode($reponse->body, true)['result'];
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(1, count($messages), $reponse->body);
    }

    function test_get_personal_messages()
    {
        $this->create_users();


        # Send From 2 to 1
        $data = [
            "body" => "Hello!",
            "receiver" => 1
        ];
        $reponse = $this->post("/message/user/", $this->user_headers_2, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);


        # Send From 1 to 2
        $data = [
            "body" => "Hello!",
            "receiver" => 2
        ];
        $reponse = $this->post("/message/user/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);

        # Send From 3 to 2
        $data = [
            "body" => "Hello!",
            "receiver" => 2
        ];
        $reponse = $this->post("/message/user/", $this->user_headers_3, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);


        # 2 messages
        $reponse = $this->get("/message/user/1/", $this->user_headers_2, $data);
        $messages = json_decode($reponse->body, true)['result'];
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(2, count($messages), $reponse->body);


        # still 2 messages
        $reponse = $this->get("/message/user/1/", $this->user_headers_2, $data);
        $messages = json_decode($reponse->body, true)['result'];
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(2, count($messages), $reponse->body);


        # No messags
        $reponse = $this->get("/message/user/1/", $this->user_headers_3, $data);
        $messages = json_decode($reponse->body, true)['result'];
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(0, count($messages), $reponse->body);
    }

    function test_get_cover_messages()
    {
        $this->create_users();


        # Send From 2 to 1
        $data = [
            "body" => "Hello!",
            "receiver" => 1
        ];
        $reponse = $this->post("/message/user/", $this->user_headers_2, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);


        # Send From 1 to 2
        $data = [
            "body" => "Hello!",
            "receiver" => 2
        ];
        $reponse = $this->post("/message/user/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);


        # 1 message
        $reponse = $this->get("/message/cover/", $this->user_headers_2, $data);
        $messages = json_decode($reponse->body, true);
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(1, count($messages), $reponse->body);


        # Send From 3 to 2
        $data = [
            "body" => "Hello!",
            "receiver" => 2
        ];
        $reponse = $this->post("/message/user/", $this->user_headers_3, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);


        # 2 messages
        $reponse = $this->get("/message/cover/", $this->user_headers_2, $data);
        $messages = json_decode($reponse->body, true);
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(2, count($messages), $reponse->body);

        # 2 messages
        $reponse = $this->get("/message/cover/", $this->user_headers_2, $data);
        $messages = json_decode($reponse->body, true);
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(2, count($messages), $reponse->body);

    }

    public function test_group_functions()
    {
        $this->create_users();


        # Create birthday group
        $data = [
            "name" => "Birthday",
            "users" => [2]
        ];
        $reponse = $this->post("/group/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);

        $data = [
            "name" => "Non-Birthday",
            "users" => [2]
        ];
        $reponse = $this->post("/group/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);


        # Send hello message
        $data = [
            "body" => "Hello!",
            "group" => 1
        ];
        $reponse = $this->post("/message/group/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);


        # 1 Message
        $reponse = $this->get("/message/group/1/", $this->user_headers_2, $data);
        $messages = json_decode($reponse->body, true)['result'];
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(1, count($messages), $reponse->body);


        # Still 1 Message
        $reponse = $this->get("/message/group/1/", $this->user_headers_2, $data);
        $messages = json_decode($reponse->body, true)['result'];
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(1, count($messages), $reponse->body);


        # No new message
        $reponse = $this->get("/message/group/1/?only_new=true", $this->user_headers_2, $data);
        $messages = json_decode($reponse->body, true)['result'];
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(0, count($messages), $reponse->body);


        # Not allowed to get messages
        $reponse = $this->get("/message/group/1/", $this->user_headers_3, $data);
        $messages = json_decode($reponse->body, true);
        self::assertEquals(403, $reponse->status_code, $reponse->body);



        # No messages here
        $reponse = $this->get("/message/group/2/", $this->user_headers_2, $data);
        $messages = json_decode($reponse->body, true)['result'];
        self::assertEquals(200, $reponse->status_code, $reponse->body);
        self::assertEquals(0, count($messages), $reponse->body);
    }
}