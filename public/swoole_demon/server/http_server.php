<?php

$http = new swoole_http_server("0.0.0.0",9501);

$http->set([
	'enable_static_handler' => true,
	'document_root' => dirname(__DIR__)."/data",
]);

$http->on('request',function($request, $response){
	$response->end("<h1>HTTPserver</h1> - ".json_encode($request->get));
});

$http->start();