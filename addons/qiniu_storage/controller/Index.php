<?php
namespace addons\qiniu_storage\controller;

use app\common\controller\AddonBase;
class Index extends AddonBase
{
    public function index()
    {
    	var_dump($this->getInfo());
    	$this->assign('name','xiaoming');
        return $this->fetch();
    }
}