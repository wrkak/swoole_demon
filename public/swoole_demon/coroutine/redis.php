<?php


const REDIS_SERVER_HOST = '127.0.0.1';
const REDIS_SERVER_PORT = 6379;


// go(function () {
//     $redis = new Swoole\Coroutine\Redis();
//     $redis->connect(REDIS_SERVER_HOST, REDIS_SERVER_PORT);
//     $redis->setDefer();
//     $redis->set('key1', 'value');


//     $redis2 = new Swoole\Coroutine\Redis();
//     $redis2->connect(REDIS_SERVER_HOST, REDIS_SERVER_PORT);
//     $redis2->setDefer();
//     $redis2->get('key1');

//     $result1 = $redis->recv();
//     $result2 = $redis2->recv();

//     var_dump($result1, $result2);
// });


go(function(){
	// $tcpclient = new Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
 //    $tcpclient->connect('127.0.0.1', 9501，0.5)
 //    $tcpclient->send("hello world\n");

    $redis = new Swoole\Coroutine\Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->setDefer();
    $redis->get('key');

  



    $mysql = new Swoole\Coroutine\MySQL();
    $mysql->connect([
        'host' => '127.0.0.1',
        'user' => 'homestead',
        'password' => 'secret',
        'database' => 'laravel-live',
    ]);
    $mysql->setDefer();
    $mysql->query('select sleep(1)');

 	

    $httpclient = new Swoole\Coroutine\Http\Client('0.0.0.0', 9599);
    $httpclient->setHeaders(['Host' => "www.qq.com"]);
    $httpclient->set([ 'timeout' => 1]);
    $httpclient->setDefer();
    $httpclient->get('/');


    $redis_res = $redis->recv();
    echo 'redis-'.date('Y-m-d:H:i:s').PHP_EOL;
	$http_res  = $httpclient->recv();
    echo 'http-'.date('Y-m-d:H:i:s').PHP_EOL;
    $mysql_res = $mysql->recv();
	echo 'mysql-'.date('Y-m-d:H:i:s').PHP_EOL;

    var_dump($redis_res,$http_res,$mysql_res);
    // $tcp_res  = $tcpclient->recv();
});