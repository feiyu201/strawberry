<?php
namespace app\admin\controller;

use think\App;
use app\BaseController;

class AdminBase extends BaseController
{
	/**
	 * 无需登录的方法
	 * @var array
	 */
	protected $noNeedLogin = [];
	/**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->checkLogin();
    }
	protected function checkLogin(){
		if(empty(session('admin'))){
			$this->error('请登陆','login/index');
		}
	}
	
}
