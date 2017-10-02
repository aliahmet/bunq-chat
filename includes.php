<?php
/**
 * Files to include
 **/

include_once "utils.php";

require 'vendor/autoload.php';
require 'ChatApp.php';

# Exceptions
auto_discover("exceptions");

// HTTP
auto_discover("http");
// Services
require "services/database.php";


// Models
auto_discover("models");
