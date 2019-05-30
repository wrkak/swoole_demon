<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Handlers\PredisHander;

class TestController extends Controller
{
    public function index(Request $request)
    {
        echo 'index'.'ggggggggg';
    	// $value1 = Redis::get('key1');
    	// phpinfo();

    	// var_dump($value1);

    	// var_dump($request->all());

    	// var_dump($_GET);
  //   	return 'test index';
		// return view('test.index');
    }

    public function imageUpload(Request $request)
    {
    	
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->file) {
            // 保存图片到本地
            // $result = $this->save($request->file, 'topics', 'upload', 1024);
            $result = $this->saveImage($request->file,'admin_live','al');
            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
            return $this->returnCode(1,'success',$data);
        }

        return $this->returnCode(400,'error');
    }

    public function adminLive(Request $request)
    {
    	// return $this->returnCode(1,'ok!!!');
    	// $_POST['http_server']->push(2,'okokok');

    	$teams = [
    		1 =>[
	    			'name'=>'马刺',
	    		    'logo'=>'./imgs/team1.png',
	    		],
	    	4 =>[
	    			'name'=>'火箭',
	    		    'logo'=>'./imgs/team2.png',
	    	],
    	];



    	$returnData = [
    		'status' => 1,
    		'message' => 'success',
    		'data' => ['type' => intval($request->type),
    		'title' => $teams[$request->team_id]['name'] ?? '直播员',
    		'logo' => $teams[$request->team_id]['logo'] ?? '',
    		'content'=> $request->content ?? '',
    		'image'=> $request->image ?? '',
    				],
    	];


			$taskData = [
    			'method'   => 'pushLive',
    			'data'   => $returnData,
 
    		];
    		$_POST['http_server']->task($taskData);


	   // $clients = PredisHander::getInstance()->sMembers('live_game_key');
 
	 	// foreach ($_POST['http_server']->connections as $fd) {
	   //          // 需要先判断是否是正确的websocket连接，否则有可能会push失败
	   //          if ($_POST['http_server']->isEstablished($fd)) {
	   //              $_POST['http_server']->push($fd, 'okok');
	   //          }
	   //      }

	 	// foreach ($clients as $fd) {
	   //          // 需要先判断是否是正确的websocket连接，否则有可能会push失败
	   //          if ($_POST['http_server']->isEstablished($fd)) {
	   //              $_POST['http_server']->push($fd, json_encode($returnData));
	   //          }
	   //      }

    	// var_dump($request->all());
    	return $this->returnCode(1,'okok');
    }

    public function chat(Request $request){

    	if(empty($request->content)){
    		return $this->returnCode(400,'error');
    	}

    	$returnData = [
    		'status'=> 1,
    		'message' => 'success',
    		'data' => [
	    		'user'=> rand(0,1000),
	    		'content'=> $request->content ?? '',
    		],
    	];

    	foreach ($_POST['http_server']->ports[1]->connections as  $fd) {
    		 if ($_POST['http_server']->isEstablished($fd)) {
	                $_POST['http_server']->push($fd,json_encode($returnData));
	             }
    	}

		return $this->returnCode(1,'ok');
    }












	public function saveImage($file, $folder, $file_prefix, $max_width = false)
    {
    	$host = 'http://192.168.10.10:9501';
        // 构建存储的文件夹规则，值如：uploads/images/avatars/201709/21/
        // 文件夹切割能让查找效率更高。
        $folder_name = "static/uploads/images/$folder/" . date("Ym/d", time());
        // 文件具体存储的物理路径，`public_path()` 获取的是 `public` 文件夹的物理路径。
        // 值如：/home/vagrant/Code/larabbs/public/uploads/images/avatars/201709/21/
        $upload_path = public_path() . '/' . $folder_name;
        // 获取文件的后缀名，因图片从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';
        // 拼接文件名，加前缀是为了增加辨析度，前缀可以是相关数据模型的 ID
        // 值如：1_1493521050_7BVc9v9ujP.png
        $filename = $file_prefix . '_' . time() . '_' . str_random(10) . '.' . $extension;
        // 如果上传的不是图片将终止操作
        // if ( ! in_array($extension, $this->allowed_ext)) {
        //     return false;
        // }
        // 将图片移动到我们的目标存储路径中
        $file->move($upload_path, $filename);

        // 如果限制了图片宽度，就进行裁剪
        if ($max_width && $extension != 'gif') {
            // 此类中封装的函数，用于裁剪图片
            // $this->reduceSize($upload_path . '/' . $filename, $max_width);
        }

        return [
            'path' => $host .'/'. "uploads/images/$folder/" . date("Ym/d", time())."/$filename"
        ];
    }


    public function returnCode($code=0,$message='',$data=null)
    {
    	$returnData = [
    		'status' => $code,
    		'message' => $message,
    		'data' => $data,
    	];

    	return json_encode($returnData);
    }

}
