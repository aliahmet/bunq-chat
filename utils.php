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