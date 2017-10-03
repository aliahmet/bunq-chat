<?php

class APIException extends Exception
{

    public $payload;
    public $status = 403;

    public function __construct($payload = [], $code = 0, Throwable $previous = null)
    {
        if(is_string($payload))
            $payload = ["message" => $payload];
        $this->payload = $payload;
        parent::__construct("", $code, $previous);
    }

}