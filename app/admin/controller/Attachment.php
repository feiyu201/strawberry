<?php
namespace app\admin\controller;

use think\facade\Cache;
use think\facade\View;
use think\facade\Db;
use app\admin\validate\Admin as adminValidate;
use think\exception\ValidateException;

class Attachment extends AdminBase
{
    public function filemanage()
    {
        return View::fetch('filemanage');
    }
    public function index()
    {
        return View::fetch();
    }
    public function getList()
    {
        $page = $this->request->param('page', 1, 'intval');
        $limit = $this->request->param('limit', 10, 'intval');
        $sort = $this->request->param('sort', null);
        $count = Db::name('attachment')->count();
        $data = Db::name('attachment')->page($page, $limit)
        ->order($sort)
        ->select()->each(function ($item, $k) {
            if (in_array($item['mimetype'], array('image/png','image/jpeg','image/gif','image/bmp'))) {
                $url = $item['url'];
                $item['see'] = "<img style='width:30px;height:30px;'  src='/{$url}' />";
            } else {
                $item['see'] = '';
            }

            $item['createtime_text'] = date('Y-m-d H:i', $item['createtime']);
            $item['updatetime_text'] = date('Y-m-d H:i', $item['updatetime']);
            $item['filesize'] = $filesize = round($item['filesize'] / 1024 * 100) / 100 . ' KB';
            return $item;
        });
        
        return json([
                'code'=> 0,
                'count'=> $count,
                'data'=>$data,
                'msg'=>'查询成功'
        ]);
    }
    public function add()
    {
        if ($this->request->isPost()) {
            $key = $this->request->param('key');
            $data = Cache::get($key);
            $admin = session('admin');
            if (!$admin) {
                $this->error("添加失败,请登录");
            }
            $data['admin_id'] = $admin['id'];
            if (Db::name('attachment')->insert($data)) {
                $this->success("添加成功");
            } else {
                $this->error("添加失败");
            }
        }
        return View::fetch();
    }
    public function edit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            $data = Cache::get($post['key']);
            $admin = session('admin');
            if (!$admin) {
                $this->error("添加失败,请登录");
            }
            $data['admin_id'] = $admin['id'];
            unset($data['createtime']);
            if (Db::name('attachment')->where('id', $post['id'])->update($data)) {
                $this->success("编辑成功");
            } else {
                $this->error("编辑失败");
            }
        }
        $id = $this->request->param('id');
        if (!$id) {
            $this->success("参数错误");
        }
        $admininfo = Db::name('attachment')->where('id', $id)->find();
        if (!$admininfo) {
            $this->success("参数错误");
        }
        View::assign('admininfo', $admininfo);
        return View::fetch();
    }
    public function delete()
    {
        $idsStr = $id = $this->request->param('idsStr');
        if (!$idsStr) {
            $this->success("参数错误");
        }
        if (Db::name('attachment')->where('id', 'in', $idsStr)->delete()) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

    /**
     * 分片上传
     */
    public function multipart_upload_add()
    {
        return View::fetch();
    }
}
