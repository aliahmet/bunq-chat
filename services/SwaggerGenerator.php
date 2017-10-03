<?php

namespace Swagger;

use Closure;
use ReflectionClass;

class SwaggerGenerator
{
    public $routes;

    /**
     * SwaggerGenerator constructor.
     * @param $routes
     */
    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    function generate_route_doc()
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
            $docs[$pattern][strtolower($method)] = $this->generate_path($method, $pattern, $title, $this->generate_parameters($rules));


        }
        return $docs;
    }


    /**
     *  Generates parameters for a path
     */
    function generate_parameters($rules)
    {
        $parameters = [];
        foreach ($rules as $name => $rule) {
            $rules = get_or_default($rule['rules'], []);
            $required = get_or_default($rule['required'], true);
            $type = get_or_default($rule['type'], "POST");
            $parser = get_or_default($rule['parser'], "string");
            $map = [
                "GET" => "query",
                "POST" => "formData",
                "HEADER" => "query",
                "URL" => "path"
            ];
            $parameters[] = [
                "name" => $name,
                "in" => $map[$type],
                "required" => $required,
                "explode" => ($parser == "list")
            ];

        }


        $parameters[] = [
            "name" => "Authorization",
            "in" => "header",
            "required" => false,
        ];

        return $parameters;
    }

    /**
     *  Generates A Path from an endpoint
     */
    function generate_path($method, $path, $title, $parameters)
    {
        preg_match_all('/{(.*?)}/', $path, $matches);
        $group = explode("/", $path)[1];
        foreach ($matches[1] as $match) {
            $parameters[] = [
                "name" => $match,
                "in" => "path",
                "required" => true,
            ];
        }
        return [
            "summary" => $title,
            "parameters" => $parameters,
            "tags" => [$group],
            "responses" => ["200" => ["description" => ""]]
        ];

    }

    /**
     *  Generates OPEN API Specs for all api endpoints
     */
    function generate_open_api_with_paths($paths)
    {
        return [
            "swagger" => "2.0",
            "info" => [
                "title" => "Bunq Chat",
                "description" => "API for Bunq Chat",
                "version" => "1.0.0"
            ],
            "host" => $_SERVER['HTTP_HOST'],
            "schemes" => ["http"],
            "basePath" => "",
            "consumes" => [
                "application/x-www-form-urlencoded"
            ],
            "produces" => [
                "application/json"
            ],
            "paths" => $paths

        ];
    }

    public function openapi()
    {
        $route_doc = $this->generate_route_doc();
        return $this->generate_open_api_with_paths($route_doc);
    }
}
