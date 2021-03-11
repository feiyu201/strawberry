<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Db;
use think\facade\Session;

use app\admin\model\CoreOrder;

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
		if( empty($data) ){
			exit('错误');
		}

		View::assign('img', 'http://api.k780.com/?app=qr.get&data='.$data['img'].'&level=L&size=6&appkey=57800&sign=8885b084c401d4c2e5c55f906be45446');
		View::assign('order_sn', $data['order_sn']);
		View::assign('name', $data['name']);
    	// 模板输出
        return View::fetch();
    }

}
