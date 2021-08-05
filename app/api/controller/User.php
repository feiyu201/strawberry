<?php

namespace app\api\controller;

use app\api\model\User as UserModel;
use app\common\controller\Api;
use app\common\controller\Auth;
use think\facade\Cache;

/**
 * 用户模块
 */
class User extends Api
{

    // 返回 用户字段
    protected $allowFields = ['id', 'token', 'avatar', 'nickname'];

    protected function _initialize()
    {
        // echo 1;
        parent::_initialize();
        
        $this->auth = new Auth();
        // 设置用户信息返回字段
        $this->auth->setAllowFields($this->allowFields);
    }

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
        Cache::set($token, $user, 60*60*24);
        $this->auth->setToken($token);
        $this->success("登录成功！", $this->auth->getUserInfo());

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
        $this->success('successful', $this->auth->getUserInfo());
    }


    /**
     * @remarks 退出登录
     */
    public function logout()
    {
        // 删除缓存
        Cache::delete($this->auth->token);
        // 更新数据
        (new UserModel)->where(['token' => $this->auth->token])->update(['token' => '']);
        $this->success('successful');
    }


/**
     * @title    忘记密码
     * @param varchar username null 用户名 NO
     * @param varchar city null 省市区 NO
     * @param varchar organization null 机构 NO
     * @param varchar duty null 职务 NO
     * @param varchar email null 电子邮箱 NO
     * @param varchar code null 验证码 YES
     * @param varchar password null 输入密码 YES
     * @param varchar rpassword null 确认密码 YES
     * @return   int code null 返回参数 200
     * @return   string message null 返回信息 successful
     * @return   array data null 返回数据 successful
     * *@author 一笑奈何
     * @desc  (描述信息)
     * @method   (POST/GET)
     * @ApiRoute    (/api/user/forgetpassword)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     */
    public function forgetpassword()
    {
        $param = input();
        $token = $param['token'];
        
        $model = new UserModel;
        $user = $model->where(['token' => $token])->find();
        if (!$user) {
            $this->error('用户不存在');
        }
        // $email_code = $param['email_captcha_code'];//邮箱验证码
        // $event = 'forgetpassword';
        // //验证邮箱
        // $res = EmailCaptchaLog::captcha_check($email, $email_code, $event);
        // if ($res['code'] == 0) {$this->error($res['msg']);}





        $password = $param['password'];
        $salt = $param['salt'] = substr(md5(rand(0, 100)), 0, 6);
        if ($password) {
            $param['password'] = $password = md5(md5($password) . $salt);
        } else {
            $this->error('密码必须');
        }

        //找回密码
        $res = $model
            ->where('id', $user['id'])
            ->update([
                'password' => $password,
                'salt' => $salt
            ]);

        if (!$res) {
            $this->error('修改密码失败');
        }

        $this->success("修改密码成功！");

    }

}
