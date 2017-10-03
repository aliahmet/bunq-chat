<?php

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
        parent::__construct([
            'settings' => [
                'displayErrorDetails' => true,
            ]
        ]);
    }

    public function applyMiddleware($middleware, $request, $response, $args)
    {
        $all_middlewares = require 'http/middlewares.php';
        $mwproc = $all_middlewares[$middleware];
        return $mwproc($request, $response, $args);
    }

    public function get($pattern, $middleware_names, $view, $rules = [])
    {
        return $this->map(['GET'], $middleware_names, $pattern, $view, $rules);
    }

    public function post($pattern, $middleware_names, $view, $rules = [])
    {
        return $this->map(['POST'], $middleware_names, $pattern, $view, $rules);
    }

    public function generate_route_doc()
    {
        $docs = [];
        foreach ($this->routes as $route) {
            $pattern = $route[0];
            $method = $route[1];
            $rules = $route[2];
            $view = $route[3];

            $title = title_case(trim(str_replace("/", " ", $pattern)));
            if (!$view instanceof Closure) {
                $view = explode(":", $view);
                $class = $view[0];
                $fn = $view[1];
                $reflector = new ReflectionClass($class);
                $fn_doc = $reflector->getMethod($fn)->getDocComment();
                if ($fn_doc) {
                    $title = trim(explode("\n", $fn_doc)[1]);
                    $title = substr($title, 1);

                }
            }
            set_default($docs, $pattern, []);
            $docs[$pattern][strtolower($method)] = generate_path($method, $pattern, $title, generate_parameters($rules));


        }
        return $docs;
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
                    $app->applyMiddleware($middleware, $request, $response, $args, $validated_data);
                }
                if ($view instanceof Closure) {
                    return $view($request, $response, $args, $validated_data);
                } else {
                    $view = explode(":", $view);
                    $class = $view[0];
                    $method = $view[1];
                    return (new $class)->$method($request, $response, $args, $validated_data);
                }
            } catch (APIException $ae) {
                return $response->withJson($ae->payload, 403);
            }
        };

        return parent::map($methods, $pattern, $wrapper_view);
    }

    public function serve_swagger()
    {
        $app = $this;
        $route_docs = $this->generate_route_doc();
        $this->get("/swagger", [], function ($request, $response, $args, $validated_data) use ($route_docs) {
            return $response->withJson(generate_swagger($route_docs));
        });
    }


}