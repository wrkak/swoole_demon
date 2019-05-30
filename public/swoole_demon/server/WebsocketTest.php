<?php

class WebsocketTest {
	const HOST = "0.0.0.0";
	const PORT = 9501;

    public $server;

    public function __construct() {
    	
        $this->server = new Swoole\WebSocket\Server(self::HOST, self::PORT);

		$this->server->set([
			'enable_static_handler' => true,
			'document_root' => dirname(__DIR__)."/data",
			'worker_num' => 2,
			'task_worker_num' => 2,
		]);

        $this->server->on('open', [$this,'onOpen']);

        $this->server->on('message', [$this,'onMessage']);

        $this->server->on('close', [$this,'onClose']);

        $this->server->on('request', [$this,'onRequest']);

		$this->server->on('task', [$this,'onTask']);
		$this->server->on('finish', [$this,'onFinish']);

        $this->server->start();
    }

    public function onOpen(swoole_websocket_server $server, $request)
    {
    	 echo "server: handshake success with fd{$request->fd}\n";
    }

    public function onMessage(Swoole\WebSocket\Server $server, $frame)
    {
    	echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";

    		//===task===
    		// $data = [
    		// 	'fd'   => $frame->fd,
    		// ];
    		//$server->task($data);

    		//===定时器===
   			//Swoole\Timer::tick(2000, function()use($server,$frame){
			//       $server->push($frame->fd, "this is server");
			// });

            $server->push($frame->fd, "this is server".time());
    }

    public function onClose($ser, $fd){
    	 echo "client {$fd} closed\n";
    }

    public function onRequest($request, $response){
    	 // 接收http请求从get获取message参数的值，给用户推送
            // $this->server->connections 遍历所有websocket连接用户的fd，给所有用户推送
            foreach ($this->server->connections as $fd) {
                // 需要先判断是否是正确的websocket连接，否则有可能会push失败
                if ($this->server->isEstablished($fd)) {
                    $this->server->push($fd, $request->get['message']);
                }
            }
    }

    public function onTask($serv, $task_id, $src_worker_id, $data)
    {
    	echo "task {$task_id} start... \n";
    	print_r($data);
    	echo "\n";
    	sleep(10);//耗时10秒
    	return "on task finish"; //告诉worker
    }

    public function onFinish($serv, $task_id, $data)
    {
    	echo "taskId ： {$task_id} finish success\n";
    	echo "data : {$data}\n";
    }


}
new WebsocketTest();