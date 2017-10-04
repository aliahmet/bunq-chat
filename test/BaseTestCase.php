<?php


use Phinx\Console\PhinxApplication;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Http\Environment;
use Slim\Http\Request as SlimRequest;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class BaseTestCase extends TestCase
{
    public $base_path = "http://localhost:9988";

    public function get($url, $headers = array(), $options = array())
    {
        $url = $this->base_path . $url;
        return Requests::get($url, $headers, $options);
    }

    public function post($url, $headers = array(), $data = array(), $options = array())
    {
        $url = $this->base_path . $url;
        return Requests::post($url, $headers, $data, $options);
    }

    public function generate_request($method, $path, $query)
    {
        return SlimRequest::createFromEnvironment(Environment::mock([
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => $path,
            'QUERY_STRING' => $query
        ]));
    }

    public function setUp()
    {

        $app = new PhinxApplication();
        $app->setAutoExit(false);
        $app->run(new StringInput('migrate -c config/phinx.yml -e test'), new NullOutput());

    }

    protected function tearDown()
    {
        unlink("db.sqlite3.dev");

    }


}

trait UseUsersTrait
{
    public $user_headers_1 = -1;
    public $user_headers_2 = -1;
    public $user_headers_3 = -1;

    public function create_users()
    {

        $headers = [];


        $data = [
            "username" => "aliahmet",
            "password" => "123456"
        ];
        $reponse = $this->post("/auth/register/", $headers, $data);
        $accesstoken = json_decode($reponse->body)->accesstoken;
        $this->user_headers_1 = [
            "Authorization" => $accesstoken
        ];


        $data = [
            "username" => "john_doe",
            "password" => "123456"
        ];
        $reponse = $this->post("/auth/register/", $headers, $data);
        $accesstoken = json_decode($reponse->body)->accesstoken;
        $this->user_headers_2 = [
            "Authorization" => $accesstoken
        ];


        $data = [
            "username" => "sailor",
            "password" => "123456"
        ];
        $reponse = $this->post("/auth/register/", $headers, $data);
        $accesstoken = json_decode($reponse->body)->accesstoken;
        $this->user_headers_3 = [
            "Authorization" => $accesstoken
        ];

    }

}