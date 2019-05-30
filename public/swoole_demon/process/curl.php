<?php
echo '--start--'.date('Y-m-d H:i:s');

$workers = [];
$urls = [
	'http://baidu.com',
	'http://qq.com',
	'http://baidu.com?search=1',
	'http://baidu.com?search=2',
	'http://baidu.com?search=3',
	'http://baidu.com?search=4',
];

// foreach ($urls as $key => $value) {
// 	$content[] = file_get_contents($value);
// }

// var_dump($content);

for ($i=0; $i < 6 ; $i++) { 
	//子进程
	$process = new swoole_process(function(swoole_process $worker)use($i,$urls){
		$content = curlData($urls[$i]);
		// echo $content.PHP_EOL;
		 $worker->write($content.PHP_EOL);
	},true);

	$pid = $process->start();

	$workers[$pid] = $process;

}

foreach ($workers as  $process) {
	echo $process->read();
}

echo '--end--'.date('Y-m-d H:i:s');

function curlData($url){
	sleep(1);
	return $url.' moni';
}

$res = swoole_process::wait();
var_dump($res);