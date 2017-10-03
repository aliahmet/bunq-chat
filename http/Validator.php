<?php
namespace  Http;

include_once ROOT_PATH."utils.php";

class Validator
{
    /****
     *  USAGE:
     *     Validator::check([
     *        "param1" => [
     *             "rules" => ["numeric" ... etc ] # see validators
     *             "required" => true or false            | defualt: true
     *             "type" => GET,  POST, COOKIE, HEADER   | default: POST
     *             "parser" => string, int, list          | default: string
     *                   ]
     *                  ]);
     *
     */

    public static  function check($ruleSet)
    {
        # Load validators
        $all_validators = require "validators.php";

        # Error messages from all datas
        $errors = [];
        $validated_data = [];

        foreach ($ruleSet as $name => $rule) {
            $rules =    get_or_default($rule['rules'], []);
            $required = get_or_default($rule['required'], true);
            $type =     get_or_default($rule['type'], "POST");


            $validators = array_map(function ($name) use ($all_validators) {
                return $all_validators[$name];
            }, $rules);

            try {
                $value = Validator::applyCheck($name, $validators, $required, $type);
                $validated_data[$name] = $value;
            } catch (\ValidationException $e){
                $errors[$name] = $e->getMessage();
            }
        }

        if($errors){
            throw new \APIException($errors);
        }
        return $validated_data;

    }

    private static  function applyCheck($name, $validators, $required, $type)
    {
        $map = [
            "GET" => $_GET,
            "POST" => $_POST,
            "COOKIE" => $_COOKIE,
            "HEADER" => $_REQUEST
        ];
        $arr = get_or_default($map[$type], []);
        if($required && ! array_key_exists($name, $arr)){
            throw  new \ValidationException("This field is required!");
        }
        if(array_key_exists($name, $arr)){
            $value = $arr[$name];
            if($value == ""){
                throw  new \ValidationException("This field is required!");
            }
            foreach ($validators as $validator) {
                $validator($value);
            }
            return $value;
        }
        return NULL;

    }
}