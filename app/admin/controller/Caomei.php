<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Db;
use think\facade\Session;

class Caomei extends BaseController
{
	
	public function login()
	{

    	// 模板输出
        return View::fetch();
	}
	
	public function payqr()
	{
		$data = input();
		View::assign('img', 'http://api.k780.com:88/?app=qr.get&data='.$data['img'].'&level=L&size=6');
		View::assign('order_sn', $data['order_sn']);
    	// 模板输出
        return View::fetch();
    }

}
