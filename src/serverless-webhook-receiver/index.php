<?php

function main(array $params) : array
{
    echo "Hello world\n";
    $db_url = $params['cloudantURL'];

    $incoming_body = base64_decode($params['__ow_body']);
    $data = json_decode($incoming_body, true);

    echo "Saving data ...\n";
    $server = new \PHPCouchDB\Server(["url" => $db_url]);
    $db = $server->useDb(["name" => "incoming"]);

    $meta = ["received" => time(), "status" => "new"]; 
    $db->create(["data" => $data, "meta" => $meta]);
    return ["body" => "OK"];
}
