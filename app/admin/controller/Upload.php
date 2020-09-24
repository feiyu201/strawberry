<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Db;
use think\facade\Cache;
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
	
		// 将上传后的文件位置返回给前端
		return json(['code' => 0, 'path' => $savename]);
	
	}

	//附件上传
    public function attachment()
    {
        $file = request()->file('file');
        $savename = \think\facade\Filesystem::disk('public')->putFile('attachment', $file);
        $file_path = 'storage/'.$savename;
        $add['url'] = $file_path;
        $add['storage'] = $file_path;
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

        $key = md5(time().rand(1000,9999));
        Cache::set($key,$add);
        //db('attachment')->insert($add);
        // 将上传后的文件位置返回给前端
        return json(['code' => 0, 'path' => $savename,'key'=>$key]);

    }
}