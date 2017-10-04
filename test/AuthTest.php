<?php

/**
 * Created by PhpStorm.
 * User: aliahmet
 * Date: 10/5/17
 * Time: 12:17 AM
 */
define(ROOT_PATH, dirname(__DIR__) . "/");
include_once dirname(__DIR__) . "/test/BaseTestCase.php";

class AuthTest extends BaseTestCase
{

    public function test_register_ok()
    {
        $headers = [];
        $data = [
            "username" => "aliahmet",
            "password" => "123456"
        ];
        $reponse = $this->post("/auth/register/", $headers, $data);
        self::assertEquals($reponse->status_code, 201);

    }

    public function test_register_short_password()
    {
        $headers = [];
        $data = [
            "username" => "aliahmet",
            "password" => "123"
        ];
        $reponse = $this->post("/auth/register/", $headers, $data);
        self::assertEquals($reponse->status_code, 403, $reponse->body);

    }

    public function test_register_used_username()
    {
        $headers = [];
        $data = [
            "username" => "aliahmet",
            "password" => "123456"
        ];
        $reponse = $this->post("/auth/register/", $headers, $data);
        $reponse = $this->post("/auth/register/", $headers, $data);
        self::assertEquals($reponse->status_code, 403, $reponse->body);


    }

    public function test_login()
    {
        $headers = [];
        $data = [
            "username" => "aliahmet",
            "password" => "123456"
        ];
        $reponse = $this->post("/auth/register/", $headers, $data);
        $reponse = $this->post("/auth/login/", $headers, $data);
        self::assertEquals($reponse->status_code, 200, $reponse->body);


    }

    public function test_login_fail()
    {
        $headers = [];
        $data = [
            "username" => "aliahmet",
            "password" => "123456"
        ];
        $reponse = $this->post("/auth/register/", $headers, $data);
        $data = [
            "username" => "aliahmet",
            "password" => "1234561"
        ];
        $reponse = $this->post("/auth/login/", $headers, $data);
        self::assertEquals($reponse->status_code, 403, $reponse->body);


    }

    public function test_logout()
    {
        $headers = [];
        $data = [
            "username" => "aliahmet",
            "password" => "123456"
        ];
        $reponse = $this->post("/auth/register/", $headers, $data);
        $accesstoken = json_decode($reponse->body)->accesstoken;

        $headers = [
            "Authorization" => $accesstoken
        ];
        $reponse = $this->post("/auth/logout/", $headers, $data);
        self::assertEquals($reponse->status_code, 200, $reponse->body);


    }

    public function test_logout_fail()
    {
        $headers = [];
        $data = [
            "username" => "aliahmet",
            "password" => "123456"
        ];
        $reponse = $this->post("/auth/register/", $headers, $data);
        $accesstoken = json_decode($reponse->body)->accesstoken;

        $headers = [
            "Authorization" => $accesstoken
        ];
        $reponse = $this->post("/auth/logout/", $headers, $data);
        self::assertEquals($reponse->status_code, 200, $reponse->body);
        // Already logged out
        $reponse = $this->post("/auth/logout/", $headers, $data);
        self::assertEquals($reponse->status_code, 403, $reponse->body);


    }

    public function test_logout_all_fail()
    {
        $headers = [];
        $data = [
            "username" => "aliahmet",
            "password" => "123456"
        ];
        $reponse = $this->post("/auth/register/", $headers, $data);
        $accesstoken = json_decode($reponse->body)->accesstoken;

        $headers = [
            "Authorization" => $accesstoken
        ];
        $reponse = $this->post("/auth/logout/all/", $headers, $data);
        self::assertEquals($reponse->status_code, 200, $reponse->body);
        // Already logged out
        $reponse = $this->post("/auth/logout/all/", $headers, $data);
        self::assertEquals($reponse->status_code, 403, $reponse->body);


    }


}