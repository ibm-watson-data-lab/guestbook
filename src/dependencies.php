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

$container['rabbitmq'] = function ($c) {
    if($c['settings']['rabbitmq'] && $c['settings']['rabbitmq']['host']) {
        // assume we are expecting to set up a connetion here
        $connection = new \PhpAmqpLib\Connection\AMQPConnection(
            $c['settings']['rabbitmq']['host'],
            $c['settings']['rabbitmq']['port'],
            $c['settings']['rabbitmq']['username'],
            $c['settings']['rabbitmq']['password']
        );
        $channel = $connection->channel();
		$channel->queue_declare(
            'comments',
            false,
            true,
            false,
            false
		); 
        return $connection;
    }
    return null;
};
