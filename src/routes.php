<?php

$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("Hello");
	$db_response = $this->couchdb['handle']->request("GET", "/");;
	echo($db_response->getBody());

    return $response;
});

