<?php
return [

    'auth' => function ($request) {
        if(\Model\User::me())
                return;
        throw new APIException(['message' => 'You are not allowed to see this page!']);

    },

    'is_admin' => function ($request, $response, $args) {

    }

];
