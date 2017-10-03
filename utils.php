<?php

function get_or_default(&$var, $default=null) {
    return isset($var) ? $var : $default;
}

function auto_discover($path){

    foreach (glob(ROOT_PATH."$path/*.php") as $filename)
    {
        include $filename;
    }

}

function set_default(&$arr, $key, $value){
    if(!isset($arr[$key])){
        $arr[$key] = $value;
    }

}