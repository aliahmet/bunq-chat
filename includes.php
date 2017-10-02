<?php
/**
 * Files to include
 **/

include_once ROOT_PATH."utils.php";

require ROOT_PATH.'vendor/autoload.php';


// Controllers
auto_discover("controllers");


require ROOT_PATH.'ChatApp.php';

# Exceptions
auto_discover("exceptions");

// HTTP
auto_discover("http");

// Services
auto_discover("services");


// Models
auto_discover("models");
