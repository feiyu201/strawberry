<?php

namespace app\api\controller;

use app\api\model\User as UserModel;
use app\common\controller\Api;
use think\Cache;

/**
 * 用户模块
 */
class User extends Api
{

    // 返回 用户字段
    protected $allowFields = ['id', 'token'];

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

        $token = md5(time() . rand(1, 100));
        (new UserModel)->where(['id' => $user['id']])->update(['token' => $token]);
        // 用户登录缓存, 缓存一天
        Cache::set($user, $token, 60*60*24) ;
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
            $user = (new UserModel)->where(['mobile' => $mobile])->find();
            if ($user) {
                $this->error('手机号重复');
            }
        } else {
            $this->error('手机号必须');
        }
        $password = $param['password'];
        $salt = $param['salt'] = substr(md5(rand(0, 100)), 0, 6);
        if ($password) {
            $param['password'] = $password = md5(md5($password) . $salt);
        } else {
            $this->error('密码必须');
        }
        //注册
        $user = (new UserModel)->register($param);

        if (!$user) {
            $this->error('注册失败');
        }

        $this->success("注册成功！", ["user" => $user]);

    }

    /**
     * @remarks 获取用户信息
     * @author 丶长情
     * @email  896@gmail.com
     * @time   2021/07/28
     */
    public function getUserInfo()
    {
        $token = request()->header('token');
        // 登录完善后需验证token真实性
        if (!$token) {
            $this->error('token不能为空');
        }

        // $userInfo = UserModel::where('token', $token)->find()->toArray();
        $userInfo = Cache::get($token);

        if (!$userInfo) {
            $this->error('用户未登录');
        }

        $allowFields = $this->allowFields;
        $userInfo = array_intersect_key($userInfo, array_flip($allowFields));
        $this->success('successful', $userInfo);
    }


    /**
     * @remarks 退出登录
     */
    public function logout()
    {
        $token = request()->header('token');
        // 登录完善后需验证token真实性
        if (!$token) {
            $this->error('token不能为空');
        }
        // 删除缓存
        Cache::delete($token);
        // 更新数据
        (new UserModel)->where(['token' => $token])->update(['token' => '']);
        $this->success('successful');
    }
}
