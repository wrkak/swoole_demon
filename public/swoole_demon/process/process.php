<?php

$process = new swoole_process(function(swoole_process $worker){
	  $worker->exec('/usr/local/bin/php', array(__DIR__.'/swoole_server.php'));
},true);

$pid = $process->start();

swoole_process::wait();