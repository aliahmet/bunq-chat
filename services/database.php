<?php
use Illuminate\Database\Capsule\Manager as Capsule;
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => "db.sqlite3",
]);
$capsule->getContainer();
$capsule->bootEloquent();
$capsule->setAsGlobal();