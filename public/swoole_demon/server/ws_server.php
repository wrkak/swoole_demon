<?php

$server = new Swoole\WebSocket\Server("0.0.0.0", 9501);


$server->set([
	'enable_static_handler' => true,
	'document_root' => dirname(__DIR__)."/data",
]);

//设置了onRequest回调，WebSocket\Server也可以同时作为http服务器

$server->on('request',function($request, $response){
	$response->end("<h1>HTTPserver</h1> - ".json_encode($request->get));
});


$server->on('open', function (Swoole\WebSocket\Server $server, $request) {
    echo "server: handshake success with fd{$request->fd}\n";
});

$server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    $server->push($frame->fd, "this is server");
});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->start();