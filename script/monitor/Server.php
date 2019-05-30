<?php

/**
 *  
 */
class Server
{
	const PORT = 9501;

	function __construct()
	{
		# code...
	}

	public function port()
	{
		//netstat -anp 2>/dev/null | grep 9501 | grep LISTEN | wc -l
		$shell = 'netstat -anp 2>/dev/null | grep '.self::PORT.' | grep LISTEN | wc -l'.PHP_EOL;
		$result = shell_exec($shell);
		if($result < 1){
			//发送报警服务
			echo date('Ymd H:i:s').' error';
		}else{
			echo 'success'.PHP_EOL;;
		}
	}


}

// nohup
Swoole\Timer::tick(1000, function(){
    (new Server())->port();
});
