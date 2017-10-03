<?php
return [
    'settings' => [
        'displayErrorDetails' => true,
    ],

    'notFoundHandler' => function ($c) {
        return function ($request, $response) use ($c) {
            return $response
                ->withStatus(404)
                ->withJson(["message" => "Page Not Found"]);
        };
    },
    'errorHandler' => function ($c) {
        return function ($request, $response, $exception) use ($c) {
            $f = fopen(ROOT_PATH . "logs/app.log", "a+");
            fwrite($f, $exception->getTraceAsString());
            fclose($f);

            return $c['response']->withStatus(500)
                ->withJson(["message" => "Houston we have a problem!"]);
        };
    }
];