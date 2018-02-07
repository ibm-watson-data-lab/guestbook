<?php

function main(array $params) : array
{
    echo "Hello world";
    $db_url = $params['cloudantURL'];

    $data = json_decode($params['__ow_body'], true);
    echo "Saving data ...\n";

    $meta = ["received" => time(),
        "status" => "new"];

    $options = ["http" => [
        "method" => "POST",
        "header" => ["Content-Type: application/json"],
        "content" => json_encode(["data" => $data, "meta" => $meta])]
        ];

    $context = stream_context_create($options);
    $response = file_get_contents($db_url, false, $context);

    return ["body" => "Exxxxxcellent"];
}
