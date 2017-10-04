<?php
use Illuminate\Database\Capsule\Manager as Capsule;
$capsule = new Capsule;
$config = require ROOT_PATH."config/database.php";
$capsule->addConnection($config[get_or_default($_ENV['ENV'], 'prod')]);
$capsule->getContainer();
$capsule->setAsGlobal();
$capsule->bootEloquent();