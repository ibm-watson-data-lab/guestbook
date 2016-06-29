<?php
require '../vendor/autoload.php';
require '../config.php';

spl_autoload_register(function ($classname) {
    require ("../classes/" 
        . str_replace('\\', DIRECTORY_SEPARATOR, $classname)
        . ".php");
});

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

// add dependencies
require '../dependencies.php';

// set up routes
require '../routes.php';

$app->run();
