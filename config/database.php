<?php
return [
    "test" => [
        'driver' => 'sqlite',
        'database' => ROOT_PATH . "db.sqlite3.dev",
    ],
    "prod" => [
        'driver' => 'sqlite',
        'database' => ROOT_PATH . "db.sqlite3",
    ]
];