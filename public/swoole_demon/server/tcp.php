<?php

//创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server("127.0.0.1", 9501); 

$serv->set(array(
    'reactor_num' => 2, //reactor thread num
    'max_request' => 50,
));


//监听连接进入事件
$serv->on('connect', function ($serv, $fd, $reactor_id) {  
    echo "Client: {$reactor_id} - {$fd} - Connect.\n";
});

//监听数据接收事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Server: {$from_id} - {$fd} ".$data);
});

//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start(); 