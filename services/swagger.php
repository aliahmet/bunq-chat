<?php

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
    foreach ($matches[1] as $match) {
        $parameters[] = [
            "name" => $match,
            "in" => "path",
            "required" => true,
        ];
    }
    return [
        strtolower($method) => [
            "summary" => $title,
            "parameters" => $parameters,
            "responses" => ["200" => ["description" => ""]]
        ]
    ];

}

/**
 *  Generates OPEN API Specs for all api endpoints
 */
function generate_swagger($paths)
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