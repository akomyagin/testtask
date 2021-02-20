<?php
return [
    'app_key' => '6ddbe374f3495c',
    'routing' => [
        'defaultController' => 'SiteController',
    ],
    'bootstrap' => [
        'db' => [
            'class' => 'App\Classes\DB\Connection',
            'param' => [
                'dsn' => 'mysql:host=localhost;dbname=test',
                'username' => '',
                'password' => '',
                'charset' => 'utf8'
            ]
        ]
    ]
];
