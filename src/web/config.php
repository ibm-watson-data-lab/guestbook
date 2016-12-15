<?php

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

if(isset($_ENV['VCAP_SERVICES'])) {
    $vcap_services = json_decode($_ENV['VCAP_SERVICES'], true);
    $config['couchdb']['url'] = $vcap_services['cloudantNoSQLDB'][0]['credentials']['url'];
} else {
    $config['couchdb']['url'] = "http://localhost:5984";
}

if(isset($_ENV['VCAP_SERVICES'])) {
    $vcap_services = json_decode($_ENV['VCAP_SERVICES'], true);
    $rabbit_url = $vcap_services['compose-for-rabbitmq'][0]['credentials']['uri'];
    $url_bits = parse_url($rabbit_url);
    $config['rabbitmq']['host'] = $url_bits['host'];
    $config['rabbitmq']['port'] = $url_bits['port'];
    $config['rabbitmq']['vhost'] = substr($url_bits['path'], 1);
    $config['rabbitmq']['username'] = $url_bits['user'];
    $config['rabbitmq']['password'] = $url_bits['pass'];
    $config['rabbitmq']['ssl'] = true;
    $config['rabbitmq']['cert'] = base64_decode($vcap_services['compose-for-rabbitmq'][0]['credentials']['ca_certificate_base64']);
} else {
    $config['rabbitmq']['host'] = "localhost";
    $config['rabbitmq']['port'] = 5672;
    $config['rabbitmq']['username'] = "guest";
    $config['rabbitmq']['password'] = "guest";
}

