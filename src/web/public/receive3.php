<?php

if($json = json_decode(file_get_contents("php://input"), true)) {
    print_r($json);
    $data = $json;
} else {
    print_r($_POST);
    $data = $_POST;
}

// now write this to a database (CouchDB)
$url = "http://localhost:5984/incoming";

$meta = ["received" => time(),
    "status" => "new",
    "agent" => $_SERVER['HTTP_USER_AGENT']];

$options = ["http" => [
    "method" => "POST",
    "header" => ["Content-Type: application/json"],
    "content" => json_encode(["data" => $data, "meta" => $meta])]
    ];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);
