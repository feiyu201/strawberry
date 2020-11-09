<?php

namespace app\api\controller;

use app\common\controller\Api;
use sent\jwt\facade\JWTAuth;
use think\facade\Db;

/**
 *  登陆获取token
 */

class Login extends Api
{
    //刷新token后，老token失效达到退出目的
    public function loginout()
    {
        if (JWTAuth::refresh()) {
            $this->success('退出成功！');
        } else {
            $this->error('退出失败！');
        }
    }
    //登录获取token
    public function login()
    {
        $username = $this->request->param('username');
        $password = $this->request->param('password');
        $user = Db::name('admin')->where('username', $username)->find();
        if (empty($user)) {
            $this->error('账号/密码错误.');
        }
        if ($user['status'] != 'normal') {
            $this->error('账户被禁用');
        }
        if ($user['password'] != md5(md5($password) . $user['salt'])) {
            $this->error('账号/密码错误');
        }
        if ($user) {
            $token = JWTAuth::builder($user);
            $this->success("登录成功！", ["access_token" => $token]);
        } else {
            $this->error("登录失败，请检查您的账号或者密码！");
        }
    }
}
