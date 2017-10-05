<?php
use Illuminate\Database\Capsule\Manager as Capsule;
$capsule = new Capsule;
$config = require ROOT_PATH."config/database.php";
$env = getenv("ENV")?getenv("ENV"):"prod";
$capsule->addConnection($config[$env]);
$capsule->getContainer();
$capsule->setAsGlobal();
$capsule->bootEloquent();