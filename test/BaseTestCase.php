<?php


use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Http\Environment;
use Slim\Http\Request;

class BaseTestCase extends TestCase
{

    public function generate_request($method, $path, $query)
    {
        return Request::createFromEnvironment(Environment::mock([
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => $path,
            'QUERY_STRING' => $query
        ]));
    }

    public function setUp()
    {

    }

    protected function tearDown()
    {

    }


}