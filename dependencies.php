<?php

// Get container
$container = $app->getContainer();

// Register component on container
$container['view'] = function ($container) {
	// $req = $app->request;
	$base_url = "http://localhost/lkppDemo";

    return new \Slim\Views\PhpRenderer('templates/', [
        'baseUrl' => $base_url,
        'templatesUrl' => $base_url ."/templates"
    ]);
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['database'], $db['username'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

// Service factory for the ORM
// https://www.cloudways.com/blog/using-eloquent-orm-with-slim/

/*$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule){
    return $capsule;
};

$container['HomeController'] = function ($container) {
   // return new \App\Controllers\HomeController($container);
};*/