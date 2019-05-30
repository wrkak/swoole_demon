<?php

namespace App\Handlers;

class TaskHander {

	public function pushLive($data,$server)
	{
		 // $clients = PredisHander::getInstance()->sMembers('live_game_key');
		 $clients = $server->ports[0]->connections;

 	 	foreach ($clients as $fd) {
	        // 需要先判断是否是正确的websocket连接，否则有可能会push失败
	        if ($server->isEstablished($fd)) {
	            $server->push($fd, json_encode($data));
	        }
    	}

	}

}