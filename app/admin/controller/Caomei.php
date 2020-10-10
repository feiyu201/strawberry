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

}
