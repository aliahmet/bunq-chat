<?php

include ROOT_PATH.'includes.php';
$app = new ChatApp;


$app->get('/test', [], "\Controller\AuthController:test");

/**
 * Auth Views
 */
$app->post('/auth/register', [], "\Controller\AuthController:register", [
    "username" => [
        "rules" => ["unique_username", "long_enough"],
    ],
    "password" => ["long_enough"],
]);
$app->post('/auth/login', [], "\Controller\AuthController:login", [
    "username" => [],
    "password" => [],
]);
$app->post('/auth/logout', ['auth'], "\Controller\AuthController:logout");
$app->post('/auth/logout/all', ['auth'], "\Controller\AuthController:logout_all");


/**
 * Group Views
 */
$app->post('/group', ['auth'], "\Controller\GroupController:create", [
    "name" => [],
    "users" => [],
]);
$app->get('/group', ['auth'], "\Controller\GroupController:list_groups");
$app->get('/group/{id}', ['auth'], "\Controller\GroupController:retrieve");
$app->post('/group/{group_id}/add', ['auth'], "\Controller\GroupController:add_user", [
    "user" => [],
]);
$app->post('/group/{group_id}/remove', ['auth'], "\Controller\GroupController:remove_user", [
    "user" => [],
]);


/**
 * Message Views
 */
$app->post('/message/new/group', ['auth'], "\Controller\MessageController:send_group_message",[
    "group" => ["rules" => ["numeric"]],
    "body" => [],
]);
$app->post('/message/new/user', ['auth'], "\Controller\MessageController:send_personal_message",[
    "receiver" => ["rules" => ["numeric"]],
    "body" => [],
]);


$app->serve_swagger();
$app->run();