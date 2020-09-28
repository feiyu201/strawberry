<?php
namespace addons\store\controller;

use app\common\controller\AddonBase;

class Index extends AddonBase
{
    public function index()
    {

    	return \app\common\http\Json::success(200,'成功');
    }
    public function add()
    {
    	 return $this->fetch();
    }
    public function detail()
    {
    	 return $this->fetch();
    }

}