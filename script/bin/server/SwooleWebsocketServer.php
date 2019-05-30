<?php


class SwooleWebsocketServer{

	CONST HOST = "0.0.0.0";
	CONST PORT = 9501;
	CONST CHART_PORT = 9502;


	public $server = null;

	public function __construct() {
		
     
		$this->server = new Swoole\WebSocket\Server(self::HOST, self::PORT);

		$this->server->listen(self::HOST,self::CHART_PORT,SWOOLE_SOCK_TCP);

		$this->server->set([
			'enable_static_handler' => true,
			'document_root' => dirname(dirname(dirname(__DIR__)))."/public/static",
			'worker_num' => 2,
			'task_worker_num' => 2,
		]);

		$this->server->on("start", [$this, 'onStart']);
		$this->server->on("open", [$this, 'onOpen']);
		$this->server->on("message", [$this, 'onMessage']);
		$this->server->on("workerstart", [$this, 'onWorkerStart']);
		$this->server->on("request", [$this, 'onRequest']);
		$this->server->on("task", [$this, 'onTask']);
		$this->server->on("finish", [$this, 'onFinish']);
		$this->server->on("close", [$this, 'onClose']);

		$this->server->start();
	}


  /**
     * @param $server
     */
    public function onStart($server) {
    	echo "start..";
        swoole_set_process_name("live_master");
    }
    /**
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server,  $worker_id) {
	      //加载index文件的内容
	    require __DIR__ . '/../../../vendor/autoload.php';
	    require_once __DIR__ . '/../../../bootstrap/app.php';
    }

    /**
     * request回调
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response) {
        if($request->server['request_uri'] == '/favicon.ico') {
            $response->status(404);
            $response->end();
            return ;
        }
        $_SERVER  =  [];
        $_SERVER['argv'] = [];
        if(isset($request->server)) {
            foreach($request->server as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        if(isset($request->header)) {
            foreach($request->header as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }

        $_GET = [];
        if(isset($request->get)) {
            foreach($request->get as $k => $v) {
                $_GET[$k] = $v;
            }
        }
        $_FILES = [];
        if(isset($request->files)) {
            foreach($request->files as $k => $v) {
                $_FILES[$k] = $v;
            }
        }
        $_POST = [];
        if(isset($request->post)) {
            foreach($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }

        $this->writeLog();
        $_POST['http_server'] = $this->server;


        ob_start();
        // 执行应用并响应
        try {

            $kernel = app()->make(Illuminate\Contracts\Http\Kernel::class);

			$laravelResponse = $kernel->handle(
			    $laravelRequest = Illuminate\Http\Request::capture()
			);

			$laravelResponse->send();

			$kernel->terminate($laravelRequest, $laravelResponse);

        }catch (\Exception $e) {
            // todo
        }

        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $workerId
     * @param $data
     */
    public function onTask($serv, $taskId, $workerId, $data) {

        // 分发 task 任务机制，让不同的任务 走不同的逻辑
        $obj = new \App\Handlers\TaskHander;

        $method = $data['method'];
        $flag = $obj->$method($data['data'], $serv);

         return $flag; // 告诉worker
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $data
     */
    public function onFinish($serv, $taskId, $data) {
        echo "taskId:{$taskId}\n";
        echo "finish-data-sucess:{$data}\n";
    }

    /**
     * 监听ws连接事件
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request) {
        // fd redis [1]
     	// var_dump($ws);
        // if($ws->port == self::PORT){
        	   // \App\Handlers\PredisHander::getInstance()->sAdd('live_game_key', $request->fd);
        // }
     
        var_dump($request->fd);
    }

    /**
     * 监听ws消息事件
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame) {
        echo "ser-push-message:{$frame->data}\n";
        // $ws->push($frame->fd, "server-push:".date("Y-m-d H:i:s"));
    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd) {
    
     // if($ws->port == self::PORT){
      // \App\Handlers\PredisHander::getInstance()->sRem('live_game_key', $fd);
  	 // }
        echo "clientid:{$fd}\n";
    }

    public function writeLog()
    {

        $data = array_merge(['date'=>date('Ymd H:i:s')],$_GET,$_POST,$_SERVER);

        $log = '';
        foreach ($data as $key => $value) {
            if(is_array($value)){
                $value = json_encode($value);
            }
            $log .= $key . ' : ' . $value . PHP_EOL;
        }
        $log .= PHP_EOL.'---'.PHP_EOL;
        $fp = fopen(__DIR__ . "/test.log", "a+");
        Swoole\Coroutine::create(function()use($fp,$log){
            $r =  Swoole\Coroutine::fwrite($fp, $log);
        });
    }

}

new SwooleWebsocketServer();