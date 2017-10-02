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
    "min_length" => function($value){

    },
    "unique_username" => function($value){
        if(User::where("username","=", $value)->count()> 0)
            throw  new ValidationException("Username is already taken!");

    }
];