<?php
namespace app\api\controller;

use app\common\controller\Api;
use sent\jwt\facade\JWTAuth;
use think\facade\Db;
use app\api\model\User as UserModel;

/**
 * 用户模块
 */
class User extends Api
{
    /**
     * 登录
     */
    public function login()
    {
        $username = input('mobile');
        $password = input('password');
        //登录
        $user = UserModel::login($username);
        
        if (!$user) {
            $this->error('手机号不存在');
        }

        if ($user['password'] != md5(md5($password) . $user['salt'])) {
            $this->error('密码错误');
        }
    
        $token = md5(time().rand(1,100));
        (new UserModel)->where(['id'=>$user['id']])->update(['token'=>$token]);
        $this->success("登录成功！", ["access_token" => $token]);
        
    }

    /**
     * 注册
     */
    public function register()
    {
        $param = input();
        $mobile = $param['mobile'];
        if ($mobile) {
            $user = (new UserModel)->where(['mobile'=>$mobile])->find();
            if ($user) {
                $this->error('手机号重复');
            }
        }else{
            $this->error('手机号必须');
        }
        $password = $param['password'];
        $salt = $param['salt'] = substr(md5(rand(0,100)), 0,6);
        if ($password) {
            $param['password'] = $password = md5(md5($password) . $salt);
        }else{
            $this->error('密码必须');
        }
        //注册
        $user = (new UserModel)->register($param);

        if (!$user) {
            $this->error('注册失败');
        }

        $this->success("注册成功！", ["user" => $user]);

    }   
}
