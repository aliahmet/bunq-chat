<?php
use Slim\App;

class ChatApp extends Slim\App
{
    /**
     * Chat App is a wrapper of Slim\App. It adds some functionality
     * created for this application. Such as authenticationmiddlewares.
     *
     */
    public static $routes = [];
    public $app;

    public function applyMiddleware($middleware, $request, $response, $args)
    {
        $all_middlewares = require 'http/middlewares.php';
        $mwproc = $all_middlewares[$middleware];
        return $mwproc($request, $response, $args);
    }

    public function get($pattern, $middleware_names, $view)
    {
        return $this->map(['GET'], $middleware_names, $pattern, $view);
    }

    public function post($pattern, $middleware_names, $view)
    {
        return $this->map(['POST'], $middleware_names, $pattern, $view);
    }

    public function map(array $methods, array $middleware_names, $pattern, $view)
    {
        $app = $this;
        $wrapper_view = function ($request, $response, $args) use ($middleware_names, $view, $app) {
            try {
                foreach ($middleware_names as $middleware) {
                    $app->applyMiddleware($middleware, $request, $response, $args);
                }
                return $view($request, $response, $args);
            } catch (APIException $ae) {
                return $response->withJson($ae->payload);
            }
        };

        return parent::map($methods, $pattern, $wrapper_view);
    }


}