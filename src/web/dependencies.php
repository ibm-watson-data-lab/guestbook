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
        if(isset($c['settings']['rabbitmq']['ssl']) && $c['settings']['rabbitmq']['ssl']) {
            // handle cert file: we need to grab the certificate string we got from
            // VCAP and actually write it to a file as the PHP SSL stream context
            // expects to find it
            file_put_contents("../certfile", $c['settings']['rabbitmq']['cert']);

			$ssl_options = array(
				'cafile' => "../certfile",
				'verify_peer' => true,
			);
            $connection = new \PhpAmqpLib\Connection\AMQPSSLConnection(
                $c['settings']['rabbitmq']['host'],
                $c['settings']['rabbitmq']['port'],
                $c['settings']['rabbitmq']['username'],
                $c['settings']['rabbitmq']['password'],
                $c['settings']['rabbitmq']['vhost'],
                $ssl_options
            );
        } else {
            $connection = new \PhpAmqpLib\Connection\AMQPConnection(
                $c['settings']['rabbitmq']['host'],
                $c['settings']['rabbitmq']['port'],
                $c['settings']['rabbitmq']['username'],
                $c['settings']['rabbitmq']['password']
            );
        }

        $channel = $connection->channel();

		$channel->queue_declare(
            'comments',
            false, // passive
            true, // durable
            false, // exclusive
            false // autodelete
		); 
        return $connection;
    }
    return null;
};
