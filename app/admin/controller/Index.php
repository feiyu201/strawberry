<?php
namespace app\admin\controller;
use think\facade\View;
use think\facade\Db;
class Index extends AdminBase
{
    public function index(){
    	// 模板输出
    	// 模板变量赋值
    	$admin = session('admin');
    	$admininfo = Db::name('admin')->where('id',$admin['id'])->find();
    	View::assign('admininfo',$admininfo);
        return View::fetch();
    }
    public function welcome(){
    	return View::fetch();
    }
}
