<?php
use Illuminate\Database\Capsule\Manager as Capsule;
$capsule = new Capsule;
$config = require ROOT_PATH."config/database.php";
$capsule->addConnection($config['prod']);
$capsule->getContainer();
$capsule->setAsGlobal();
$capsule->bootEloquent();