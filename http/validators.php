<?php
use Model\User;

return [
    "numeric" => function ($value) {
        if(!is_numeric($value)){
            throw  new ValidationException("This value must be numberic!");
        }
    },
    "long_enough" => function ($value){
    if(strlen($value) < 6)
        throw  new ValidationException("Must be at least 6 chars!");

    },
    "unique_username" => function($value){
        if(User::where("username","=", $value)->count()> 0)
            throw  new ValidationException("Username is already taken!");

    },
    "boolean" => function($value){
        if($value != "true" && $value != "false")
            throw  new ValidationException("Value must be `true` or `false`");
    }
];