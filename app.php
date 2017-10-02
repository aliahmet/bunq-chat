<?php

use Model\AccessToken;
use Model\Group;
use Model\Message;
use Model\User;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

include  'includes.php';
$app = new ChatApp;


$app->post('/register', [], function (Request $request, Response $response) {
    $validated_data = \Http\Validator::check([
        "username" => [
            "rules" => ["unique_username", "long_enough"],
        ],
        "password" => ["long_enough"],
    ]);
    $user = new User;
    $user->username = $validated_data['username'];
    $user->set_password($validated_data['password']);
    $user->save();
    $accesstoken = AccessToken::generate($user);
    return $response->withJson(
        ['accesstoken'=>$accesstoken->accesstoken], 200
    );
});


$app->post('/login', [], function (Request $request, Response $response) {
    /*
     * Generate and retrieve new acccesstoken.
     */
    $validated_data = \Http\Validator::check([
        "username" => [],
        "password" => [],
    ]);
    $user = User::where("username", "=", $validated_data['username'])->first();
    if(!$user or !$user->check_password($validated_data['password']))
        return $response->withJson(
            ['message'=>"Incorrect Username / Password"], 403
        );

    $accesstoken = AccessToken::generate($user);
    return $response->withJson(
        ['accesstoken'=>$accesstoken->accesstoken]
    );
});

$app->post('/logout', ['auth'], function (Request $request, Response $response) {
    /**
     *  Finish current session and Invalidate currently used accesstoken.
     */
    AccessToken::current()->delete();
    return $response->withJson(
        [ "message" => "Sucessfully logged out" ]
    );
});

$app->post('/logout/all', ['auth'], function (Request $request, Response $response) {
    /**
     *  Invalidate all access tokens.
     */
    User::me()->accesstokens()->delete();
    return $response->withJson(
        [ "message" => "Sucessfully logged out" ]
    );
});


$app->post('/group', ['auth'], function (Request $request, Response $response) {
    /**
     *  Send a new message
     */
    $validated_data = \Http\Validator::check([
        "name" => [ ],
        "users" =>  [ ],
    ]);

    $users = User::whereIn("id", $validated_data)->get();
    $group = new Group();
    $group->name = $validated_data['name'];
    $group->save();
    $group->users()->syncWithoutDetaching($users);
    $group->users()->syncWithoutDetaching(User::me());
    return $response->withJson($group, 201);
});


$app->get('/group', ['auth'], function (Request $request, Response $response) {
    /**
     * List all groups of the user
     */

    return $response->withJson(User::me()->groups()->get(), 201);
});

$app->get('/group/{id}', ['auth'], function (Request $request, Response $response, $attributes) {
    /**
     * Get details of a group
     */

    $id = $attributes['id'];
    $group = User::me()->groups()->find($id);
    if(!$group){
        throw new APIException("Group doesn't exist or you are not allowed to see it!");
    }

    return $response->withJson($group, 201);
});

$app->post('/group/{group_id}/add', ['auth'], function (Request $request, Response $response, $attributes) {
    /**
     * Add a new user to a group
     */

    $validated_data = \Http\Validator::check([
        "user" =>  [ ],
    ]);
    $user_id = $validated_data['user'];
    $group_id = $attributes['group_id'];

    $group = User::me()->groups()->find($group_id);
    $user = User::find($user_id);

    if(!$group){
        throw new APIException("Group doesn't exist or you are not allowed to see it!");
    }

    if($group->users->contains($user)){
        throw new APIException("User is already in group!");
    }

    $group->users()->syncWithoutDetaching($user);


    return $response->withJson($group, 201);
});
$app->post('/group/{group_id}/remove', ['auth'], function (Request $request, Response $response, $attributes) {
    /**
     * Remove a person from a group
     * or Leave a group
     */

    $validated_data = \Http\Validator::check([
        "user" =>  [ ],
    ]);
    $user_id = $validated_data['user'];
    $group_id = $attributes['group_id'];

    $group = User::me()->groups()->find($group_id);
    $user = User::find($user_id);

    if(!$group){
        throw new APIException("Group doesn't exist or you are not allowed to see it!");
    }

    if(! $group->users->contains($user)){
        throw new APIException("User is not in the group!");
    }

    $group->users()->syncWithoutDetaching($user);


    return $response->withJson($group, 201);
});



$app->post('/message/new/group', ['auth'], function (Request $request, Response $response) {
    /**
     *  Send a new message
     */
    $validated_data = \Http\Validator::check([
        "group" => [ "rules" =>  ["numeric"] ],
        "body" =>  [ ],
    ]);




    User::me()->accesstokens()->delete();
    return $response->withJson(
        [ "message" => "Sucessfully logged out" ]
    );
});

$app->post('/message/new/user', ['auth'], function (Request $request, Response $response) {
    /**
     *  Send a new message
     */
    $validated_data = \Http\Validator::check([
        "receiver" => [  "rules" =>  ["numeric"] ],
        "body" =>  [ ],
    ]);
    if(!key_exists("receiver", $validated_data) | !key_exists("group", $validated_data)){
        throw new APIException("message target must be specified via 'group' or 'reciever' ");
    }

    User::me()->accesstokens()->delete();
    return $response->withJson(
        [ "message" => "Sucessfully logged out" ]
    );
});



$app->run();