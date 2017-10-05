<?php

include ROOT_PATH . 'includes.php';
$app = new ChatApp;

/***
 *  Routing Spec:
 *  $app->{METHOD}(
 *                  { PATTERN },
 *                  { MIDDLEWARES },
 *                  { VIEW (CLOSURE or METHOD) },
 *                  [, { INPUT RULE SET }]
 *                )
 *
 * See HTTP\Validator for rule set specs
 */

$app->get('/test/', [], "\Controller\AuthController:test");

/**
 * Auth Views
 */
$app->post('/auth/register/', [], "\Controller\AuthController:register", [
    "username" => [
        "description" => "At least 6 chars.",
        "rules" => ["unique_username", "long_enough"],
    ],
    "password" => [
        "description" => "At least 6 chars.",
        "rules" => ["long_enough"],
    ],
]);
$app->post('/auth/login/', [], "\Controller\AuthController:login", [
    "username" => [],
    "password" => [],
]);
$app->post('/auth/logout/', ['auth'], "\Controller\AuthController:logout");
$app->post('/auth/logout/all/', ['auth'], "\Controller\AuthController:logout_all");


/**
 * Group Views
 */
$app->post('/group/', ['auth'], "\Controller\GroupController:create", [
    "name" => [
        "description" => "Name of the new group"
    ],
    "users" => [
        "description" => "Users to add",
        "parser" => "list"
    ],
]);
$app->get('/group/', ['auth'], "\Controller\GroupController:list_groups");
$app->get('/group/{id}/', ['auth'], "\Controller\GroupController:retrieve");
$app->post('/group/{group_id}/add/', ['auth'], "\Controller\GroupController:add_user", [
    "user" => [
        "description" => "id of the user to add",
        "rules" => ["numeric"],
    ],
]);
$app->post('/group/{group_id}/remove/', ['auth'], "\Controller\GroupController:remove_user", [
    "user" => [
        "description" => "id of the user to remove",
        "rules" => ["numeric"],
    ],
]);


/**
 * Message Views
 */
$app->post('/message/group/', ['auth'], "\Controller\MessageController:send_group_message", [
    "group" => [
        "description" => "Group ID of the group",
        "rules" => ["numeric"]
    ],
    "body" => [
        "description" => "Message text",
    ],
]);
$app->post('/message/user/', ['auth'], "\Controller\MessageController:send_personal_message", [
    "receiver" => [
        "description" => "ID of the receiver user",
        "rules" => ["numeric"]
    ],
    "body" => [
        "description" => "Message text",
    ],
]);
$app->get('/message/cover/', ['auth'], "\Controller\MessageController:get_last_messages");
$app->get('/message/new/', ['auth'], "\Controller\MessageController:get_new_messages");
$app->get('/message/user/{user_id}/', ['auth'], "\Controller\MessageController:get_messages_with_user",
    [
        "page" => [
            "required" => false,
            "description" => "default: 1",
            "type" => "GET",
            "rules" => ["numeric"]
        ],
        "only_new" => [
            "description" => "Only non-delivered messages. (true or false) default:false",
            "required" => false,
            "type" => "GET",
            "rules" => ["boolean"]
        ],
    ]);
$app->get('/message/group/{group_id}/', ['auth'], "\Controller\MessageController:get_messages_in_group",
    [
        "page" => [
            "required" => false,
            "description" => "default: 1",
            "type" => "GET",
            "rules" => ["numeric"]
        ],
        "only_new" => [
            "description" => "Only non-delivered messages. (true or false) default:false",
            "required" => false,
            "type" => "GET",
            "rules" => ["boolean"]
        ],
    ]);
$app->get('/message/all/', ['auth'], "\Controller\MessageController:get_all_messages",
    [
        "page" => [
            "required" => false,
            "description" => "default: 1",
            "type" => "GET",
            "rules" => ["numeric"]
        ],
        "only_new" => [
            "description" => "Only non-delivered messages. (true or false) default:false",
            "required" => false,
            "type" => "GET",
            "rules" => ["boolean"]
        ],
    ]);

$app->post('/message/mark-read/', ['auth'], "\Controller\MessageController:mark_message_as_read",
    [
        "message_id" => [
            "required" => true,
            "description" => "Message id",
            "rules" => ["numeric"]
        ]
    ]);



$app->serve_swagger();
if (getenv("DONT_RUN"))
    $app->run();