<?php

use Slim\Container;
use Swagger\SwaggerGenerator;

class ChatApp extends Slim\App
{
    /**
     * Chat App is a wrapper of Slim\App. It adds some functionality
     * created for this application. Such as authenticationmiddlewares.
     *
     */
    public $routes = [];

    public function __construct($container = [])
    {
        parent::__construct(array_merge($container, require "config/container.php"));
    }

    public function applyMiddleware($middleware, $request)
    {
        $all_middlewares = require 'http/middlewares.php';
        $mwproc = $all_middlewares[$middleware];
        return $mwproc($request);
    }

    public function get($pattern, $middleware_names, $view, $rules = [])
    {
        return $this->map(['GET'], $middleware_names, $pattern, $view, $rules);
    }

    public function post($pattern, $middleware_names, $view, $rules = [])
    {
        return $this->map(['POST'], $middleware_names, $pattern, $view, $rules);
    }


    public function map(array $methods, array $middleware_names, $pattern, $view, $rules = [])
    {
        $app = $this;


        $this->routes[] = [$pattern, $methods[0], $rules, $view];

        $wrapper_view = function ($request, $response, $args) use ($methods, $middleware_names, $view, $app, $rules) {
            $response = $response->withHeader('Access-Control-Allow-Origin', '*');
            try {
                $validated_data = \Http\Validator::check($rules);

                foreach ($middleware_names as $middleware) {
                    $app->applyMiddleware($middleware, $request);
                }

                if ($view instanceof Closure) {
                    return $view($request, $response, $args, $validated_data);
                } else {
                    $view = explode(":", $view);
                    $class = $view[0];
                    $method = $view[1];
                    return (new $class)->$method($request, $response, $args, $validated_data);
                }
            } catch (APIException $api_exception) {
                return $response->withJson($api_exception->payload, $api_exception->status);
            }
        };

        return parent::map($methods, $pattern, $wrapper_view);
    }

    /**
     * Generate Openapi Specs and serve
     */
    public function serve_swagger()
    {
        $swagger = new SwaggerGenerator($this->routes);
        $this->get("/swagger", [], function ($request, $response, $args, $validated_data) use ($swagger) {
            return $response->withJson($swagger->openapi());
        });
    }


}