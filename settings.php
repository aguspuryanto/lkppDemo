<?php

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

/*$config['db']['host']   = 'localhost';
$config['db']['user']   = 'root';
$config['db']['pass']   = '103Wonokromo##';
$config['db']['dbname'] = 'surabaya_hotelokal';*/

// Create and configure Slim app
/*$config = ['settings' => [
    'addContentLengthHeader' => false,
    
]];*/

$config = [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'lkpp',
            'username' => 'root',
            'password' => '103Wonokromo##',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ],
];