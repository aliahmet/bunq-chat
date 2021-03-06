<?php
/**
 * Created by PhpStorm.
 * User: aliahmet
 * Date: 10/2/17
 * Time: 9:27 PM
 */

namespace Controller;

use Model\AccessToken;
use Model\User;

class AuthController
{

    /**
     * Just a view too see if the servers here.
     *
     * $sample_response
     * {
     *    "message" : "Up and Running!"
     * }
     * sample_response$
     */
    public function test($request, $response, $attributes, $validated_data)
    {
        return $response->withJson([
            "message" => "Up and Running!"
        ]);
    }

    /**
     * Generates and returns a new accesstoken along eith user id
     *
     *
     * $sample_response
     * {
     *    "accesstoken" : "ABCDEABCDEABCDEABCDE",
     *    "id": 151
     * }
     * sample_response$
     */
    public function login($request, $response, $attributes, $validated_data)
    {
        $user = User::where("username", "=", $validated_data['username'])->first();
        if (!$user or !$user->check_password($validated_data['password']))
            return $response->withJson(
                ['message' => "Incorrect Username / Password"], 403
            );

        $accesstoken = AccessToken::generate($user);
        return $response->withJson(
            [
                'accesstoken' => $accesstoken->accesstoken,
                'id' => $user->id
            ]
        );
    }

    /**
     * Register New User and returns an accestoken aling wiht user id
     *
     * $sample_response
     * {
     *    "accesstoken" : "ABCDEABCDEABCDEABCDE",
     *    "id": 151
     * }
     * sample_response$
     */
    public function register($request, $response, $attributes, $validated_data)
    {
        $user = new User;
        $user->username = $validated_data['username'];
        $user->set_password($validated_data['password']);
        $user->save();
        $accesstoken = AccessToken::generate($user);
        return $response->withJson(
            [
                'accesstoken' => $accesstoken->accesstoken,
                'id' => $user->id
            ], 201
        );
    }

    /**
     *  Finish current session and Invalidate currently used accesstoken.
     *
     *
     * $sample_response
     * {
     *    "message" : "Sucessfully logged out"
     * }
     * sample_response$
     */
    public function logout($request, $response, $attributes, $validated_data)
    {

        AccessToken::current()->delete();
        return $response->withJson(
            ["message" => "Sucessfully logged out"]
        );
    }

    /**
     *  Logout from all devices.
     *
     * $sample_response
     * {
     *    "message" : "Sucessfully logged out"
     * }
     * sample_response$
     */
    public function logout_all($request, $response, $attributes, $validated_data)
    {

        User::me()->accesstokens()->delete();
        return $response->withJson(
            ["message" => "Sucessfully logged out"]
        );
    }

}