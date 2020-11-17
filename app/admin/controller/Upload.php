<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Db;
use think\facade\Cache;
use think\facade\Event;

class Upload extends BaseController
{
	/**
	 * layui 文件上传接口
	 */
	public function index()
	{
		// file('文件域的字段名')
		$file = request()->file('file');
	
		// 上传到本地服务器 返回文件存储位置
		//
		// disk('磁盘配置名称') 该配置 在 config/filesystem.php中的 disks 中查看
		// disk('public') 代表使用的是 disks 中的 public 键名对应的磁盘配置
		// putFile('目录名', $file);
		//
		// $savename 执行上传 返回文件存储位置
		//
		// 当前文件存储位置：public/storage/topic/当前时间/文件名
		$savename = \think\facade\Filesystem::disk('public')->putFile('topic', $file);
	
		
	
		if ($savename) {
			$savename = str_replace(DIRECTORY_SEPARATOR, '/', $savename);
			$file_path = 'storage/'.$savename;
			$add['url'] = '/'.$file_path;
			$add['storage'] = '/'.$file_path;
			$add['filesize'] = filesize($file_path);
			$add['mimetype'] = mime_content_type($file_path);
			$add['sha1'] = sha1_file($file_path);
			$add['createtime'] = time();
			$add['updatetime'] = time();
			$add['uploadtime'] = time();
		
		
			//检测文件信息
			if(in_array($add['mimetype'],array('image/png','image/jpeg','image/gif','image/bmp'))){
				list($width, $height, $type, $attr) = getimagesize($file_path);
				$add['imagewidth'] = $width;
				$add['imageheight'] = $height;
				$add['imagetype'] = $add['mimetype'];
			}
			Db::name('attachment')->insert($add);
			// 将上传后的文件位置返回给前端
			return json(['code' => 0, 'path' => $savename]);
		} else {
			return json(['code' => 1, 'msg' => '上传错误']);
		}
		
	}

	/**
	 * 图片组件上传接口
	 */
	public function uploadfile()
	{
		// file('文件域的字段名')
		$file = request()->file('file');
	
		// 上传到本地服务器 返回文件存储位置
		//
		// disk('磁盘配置名称') 该配置 在 config/filesystem.php中的 disks 中查看
		// disk('public') 代表使用的是 disks 中的 public 键名对应的磁盘配置
		// putFile('目录名', $file);
		//
		// $savename 执行上传 返回文件存储位置
		//
		// 当前文件存储位置：public/storage/topic/当前时间/文件名
		$savename = \think\facade\Filesystem::disk('public')->putFile('upload', $file);
		if ($savename) {
			$savename = str_replace(DIRECTORY_SEPARATOR, '/', $savename);
			$file_path = 'storage/'.$savename;
			$add['url'] = '/'.$file_path;
			$add['storage'] = '/'.$file_path;
			$add['filesize'] = filesize($file_path);
			$add['mimetype'] = mime_content_type($file_path);
			$add['sha1'] = sha1_file($file_path);
			$add['createtime'] = time();
			$add['updatetime'] = time();
			$add['uploadtime'] = time();
	
	
			//检测文件信息
			if(in_array($add['mimetype'],array('image/png','image/jpeg','image/gif','image/bmp'))){
				list($width, $height, $type, $attr) = getimagesize($file_path);
				$add['imagewidth'] = $width;
				$add['imageheight'] = $height;
				$add['imagetype'] = $add['mimetype'];
			}
			Db::name('attachment')->insert($add);
			// 将上传后的文件位置返回给前端
			 $this->success("上传成功", '',['url' => $savename]);
		} else {
			$this->error("上传出错了");
		}
	
	}
	
	//附件上传
    public function attachment()
    {
        //钩子事件 存储插件
        Event::listen('Storage', 'addons\qiniu_storage\event\Storage');
        $hook_res = event('Storage');
        if ($hook_res) {
            $hook_res  = $hook_res[0];
            $url       = $hook_res['url'];
            $storage   = $hook_res['storage'];
            $savename  = $hook_res['fileName'];
            $file_path = $hook_res['fileGetRealPath'];
            $mimetype  = $hook_res['fileGetOriginalMime'];
//            dd($hook_res); //本地存储逻辑也可以写钩子里面
        } else {
            $file      = request()->file('file');
            $savename  = \think\facade\Filesystem::disk('public')->putFile('attachment', $file);
            $url = $file_path = 'storage/' . $savename;
            $storage = 'localhost';
            $mimetype  = mime_content_type($file_path);  //mime_content_type 5.3已经废弃
        }

        $add['url'] = $url;
        $add['storage'] = $storage;
        $add['filesize'] = filesize($file_path);
        $add['mimetype'] = $mimetype;
        $add['sha1'] = sha1_file($file_path);
        $add['createtime'] = time();
        $add['updatetime'] = time();
        $add['uploadtime'] = time();

        //检测文件信息
        if(in_array($add['mimetype'],array('image/png','image/jpeg','image/gif','image/bmp'))){
            list($width, $height, $type, $attr) = getimagesize($file_path);
            $add['imagewidth'] = $width;
            $add['imageheight'] = $height;
            $add['imagetype'] = $add['mimetype'];
        }

        $key = md5(time().rand(1000,9999));
        Cache::set($key,$add);
        //Db::name('attachment')->insert($add);
        // 将上传后的文件位置返回给前端
        return json(['code' => 0, 'path' => $savename,'key'=>$key]);

    }
    /**
     * 上传图片至编辑器
     * @return \think\response\Json
     */
    public function uploadEditor()
    {
    	$file = request()->file('upload');
		if(!$file){
			$this->error("请选择文件");
		}
    	$savename = \think\facade\Filesystem::disk('public')->putFile('upload', $file);
    	
    	if ($savename) {
    		$savename = str_replace(DIRECTORY_SEPARATOR, '/', $savename);
    		$file_path = 'storage/'.$savename;
    		$add = [];
    		$add['url'] = '/'.$file_path;
    		$add['storage'] = '/'.$file_path;
    		$add['filesize'] = filesize($file_path);
    		$add['mimetype'] = mime_content_type($file_path);
    		$add['sha1'] = sha1_file($file_path);
    		$add['createtime'] = time();
    		$add['updatetime'] = time();
    		$add['uploadtime'] = time();
    		
    		
    		//检测文件信息
    		if(in_array($add['mimetype'],array('image/png','image/jpeg','image/gif','image/bmp'))){
    			list($width, $height, $type, $attr) = getimagesize($file_path);
    			$add['imagewidth'] = $width;
    			$add['imageheight'] = $height;
    			$add['imagetype'] = $add['mimetype'];
    		}
    		Db::name('attachment')->insert($add);
    		
    		return json([
    				'error'    => [
    					'message' => '上传成功',
    					'number'  => 201,
    				],
    					'fileName' => '',
    					'uploaded' => 1,
    					'url'      => '/storage/'.$savename,
    				]);
    	} else {
    		$this->error("上传出错了");
    	}
    }
    
    /**
     * 获取上传文件列表
     */
    public function getUploadFiles()
    {
    	$get = $this->request->get();
    	$page = isset($get['page']) && !empty($get['page']) ? $get['page'] : 1;
    	$limit = isset($get['limit']) && !empty($get['limit']) ? $get['limit'] : 10;
    	$title = isset($get['title']) && !empty($get['title']) ? $get['title'] : null;
    	$this->model = Db::name('attachment');
    	$count = $this->model
    	->count();
    	$list = $this->model
    	->page($page, $limit)
    	->order("createtime")
    	->select()->each(function($item){
    		$item['createtime'] = date('Y-m-d H:i',$item['createtime']);
    		return $item;
    	});
    	$data = [
	    	'code'  => 0,
	    	'msg'   => '',
	    	'count' => $count,
	    	'data'  => $list,
    	];
    	return json($data);
    }
    
}