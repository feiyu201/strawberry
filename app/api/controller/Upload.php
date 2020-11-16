<?php

namespace app\api\controller;

use app\common\controller\Api;
use sent\jwt\facade\JWTAuth;
use think\facade\Db;

class Upload extends Api
{

    /**
     * eden 文件上传接口
     */
    public function uploadFile()
    {
        $res = $this->upload();
        //储存文件信息到数据库
        if ($res) {
            $savename = $res['savename'];
            unset($res['savename']);
            Db::name('attachment')->insert($res);
            // 将上传后的文件位置返回给前端
            $this->success('上传成功！', $res);
        } else {
            $this->error('上传失败！');
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
            $file     = request()->file('file');
            $savename = \think\facade\Filesystem::disk('public')->putFile('attachment', $file);
            $url      = $file_path = '/storage/' . $savename;
            $storage  = 'localhost';
            $mimetype = $file->getOriginalExtension();
        }

        $add['url']        = $url;
        $add['storage']    = $storage;
        $add['filesize']   = filesize($file_path);
        $add['mimetype']   = $mimetype;
        $add['sha1']       = sha1_file($file_path);
        $add['createtime'] = time();
        $add['updatetime'] = time();
        $add['uploadtime'] = time();

        //检测文件信息
        if (in_array($add['mimetype'], array('image/png', 'image/jpeg', 'image/gif', 'image/bmp'))) {
            list($width, $height, $type, $attr) = getimagesize($file_path);
            $add['imagewidth']  = $width;
            $add['imageheight'] = $height;
            $add['imagetype']   = $add['mimetype'];
        }

        $key = md5(time() . rand(1000, 9999));
        Cache::set($key, $add);
        //Db::name('attachment')->insert($add);
        // 将上传后的文件位置返回给前端
        return json(['code' => 0, 'path' => $savename, 'key' => $key]);

    }

    /**
     *
     * eden 上传私有方法
     * TP6 上传需要配置 磁盘列表 config/filesystem.php 并给予文件权限777
     * https://www.cnblogs.com/stronger-xsw/p/13032308.html
     *
     * getRealPath()    获取临时文件路径
     * getOriginalName()    获取原始文件名称
     * getOriginalExtension()    获取原始文件后缀名
     *
     */
    private function upload()
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
            $savename  = str_replace(DIRECTORY_SEPARATOR, '/', $savename);
            $file_path = '/storage/' . $savename;
            $add['url']        = $file_path;
            $add['storage']    = $file_path;
            $add['filesize']   = filesize($file_path);
            $add['mimetype']   = $file->getOriginalExtension();
            $add['sha1']       = sha1_file($file_path);
            $add['createtime'] = time();
            $add['updatetime'] = time();
            $add['uploadtime'] = time();
            $add['savename']   = $savename;
        }

        //查询数据库是否已经存在 如果有删除服务器文件此处逻辑可注释
        $info = Db::name('attachment')->where('sha1', sha1_file($file_path))->find();
        if ($info) {
            // 将上传后的文件位置返回给前端
            $this->success('上传成功！', $info);
        }

        //检测文件信息
        if (in_array($add['mimetype'], array('image/png', 'image/jpeg', 'image/gif', 'image/bmp'))) {
            list($width, $height, $type, $attr) = getimagesize($file_path);
            $add['imagewidth']  = $width;
            $add['imageheight'] = $height;
            $add['imagetype']   = $add['mimetype'];
        }

        return $add;
    }
}