<?php

/**
 * Created by PhpStorm.
 * User: aliahmet
 * Date: 10/5/17
 * Time: 1:02 AM
 */
class GroupTest extends BaseTestCase
{
    use UseUsersTrait;

    public function test_create_group()
    {
        $this->create_users();

        $data = [
            "name" => "Birthday",
            "users" => [2, 3]
        ];
        $reponse = $this->post("/group/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);
    }

    public function test_list_groups()
    {
        $this->create_users();

        $data = [
            "name" => "Birthday",
            "users" => [2, 3]
        ];
        $reponse = $this->post("/group/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);

        $reponse = $this->get("/group/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 200, $reponse->body);

        $groups = json_decode($reponse->body, true);
        self::assertEquals(count($groups), 1, $reponse->body);

    }

    public function test_add_new_user_to_group()
    {
        $this->create_users();

        $data = [
            "name" => "Birthday",
            "users" => [2]
        ];
        $reponse = $this->post("/group/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);

        $reponse = $this->get("/group/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 200, $reponse->body);

        $groups = json_decode($reponse->body, true);
        $num_of_members = count($groups[0]['members']);
        self::assertEquals(2, $num_of_members, $reponse->body);


        // Now add 3rd
        $data = [
            "user" => 3
        ];
        $reponse = $this->post("/group/1/add/", $this->user_headers_1, $data);
        $groups = json_decode($reponse->body, true);
        $num_of_members = count($groups['members']);
        self::assertEquals(3, $num_of_members, $reponse->body);

    }

    public function test_add_new_user_to_group_fail()
    {
        $this->create_users();

        $data = [
            "name" => "Birthday",
            "users" => [2]
        ];
        $reponse = $this->post("/group/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);

        $reponse = $this->get("/group/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 200, $reponse->body);

        $groups = json_decode($reponse->body, true);
        $num_of_members = count($groups[0]['members']);
        self::assertEquals(2, $num_of_members, $reponse->body);


        // Now add 3rd by 3rd user
        $data = [
            "user" => 3
        ];
        $reponse = $this->post("/group/1/add/", $this->user_headers_3, $data);
        self::assertEquals($reponse->status_code, 403, $reponse->body);

    }


    public function test_illegals()
    {
        $this->create_users();

        $data = [
            "name" => "Birthday",
            "users" => [2]
        ];
        $reponse = $this->post("/group/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 201, $reponse->body);

        // Now leave
        $data = [
            "user" => 1
        ];
        $reponse = $this->post("/group/1/remove/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 200, $reponse->body);

        // Now leave again ?
        $data = [
            "user" => 1
        ];
        $reponse = $this->post("/group/1/remove/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 403, $reponse->body);

        // Now remove 2 ?
        $data = [
            "user" => 2
        ];
        $reponse = $this->post("/group/1/remove/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 403, $reponse->body);

        // Now add 3 ?
        $data = [
            "user" => 3
        ];
        $reponse = $this->post("/group/1/add/", $this->user_headers_1, $data);
        self::assertEquals($reponse->status_code, 403, $reponse->body);

    }

}