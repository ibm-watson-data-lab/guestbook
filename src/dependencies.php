<?php

$container['couchdb'] = function ($c) {
	$couchdb = [];
	$couchdb['handle'] = new \GuzzleHttp\Client([
		"base_uri" => $c['settings']['couchdb']['url'],
	]);
	return $couchdb;
};

$container['view'] = function ($c) {
    return new \Slim\Views\PhpRenderer(
        "../templates/", 
        ["router" => $c->router]
    );
};
