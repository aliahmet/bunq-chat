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
    public function test($request, $response, $attributes, $validated_data)
    {
        throw  new \ErrorException("ds");
        return $response->withJson(["ok" => $r]);
    }

    /**
     * Generate new accesstoken.
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
     * Register New User
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
     */
    public function logout_all($request, $response, $attributes, $validated_data)
    {

        User::me()->accesstokens()->delete();
        return $response->withJson(
            ["message" => "Sucessfully logged out"]
        );
    }

}