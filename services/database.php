<?php
use Illuminate\Database\Capsule\Manager as Capsule;
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => ROOT_PATH."db.sqlite3",
]);
$capsule->getContainer();
$capsule->setAsGlobal();
$capsule->bootEloquent();