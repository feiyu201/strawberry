<?php

namespace app\api\controller;

use app\api\model\User as UserModel;
use app\common\controller\Api;
use app\common\controller\Auth;
use think\facade\Event;
use think\facade\Cache;

/**
 * @title 用户接口
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
     * @title    用户注册
     * @author 一笑奈何
     * @desc 只需要传递需要的参数就可以
     * @method   (POST/GET)
     * @ApiRoute    (/api/user/register)
     * @param   varchar username &nbsp; 用户名 YES
     * @param   varchar openid &nbsp; 微信信息 NO
     * @param   varchar nickname &nbsp; 昵称 NO
     * @param   varchar password &nbsp; 密码 NO
     * @param   varchar email &nbsp; 电子邮箱 NO
     * @param   varchar mobile &nbsp; 手机号 NO
     * @param   varchar avatar &nbsp; 头像 NO
     * @param   tinyint  level &nbsp; 等级 NO
     * @param   tinyint  gender &nbsp; 性别 NO
     * @param   date birthday &nbsp; 生日 NO
     * @param   varchar bio &nbsp; 格言 NO
     * @param   decimal money &nbsp; 余额 NO
     * @param   int  score &nbsp; 积分 NO
     * @param   int  successions &nbsp; 连续登录天数 NO
     * @param   int  maxsuccessions &nbsp; 最大连续登录天数 NO
     * @param   int prevtime &nbsp; 上次登录时间 NO
     * @param   int logintime &nbsp; 登录时间 NO
     * @param   varchar loginip &nbsp; 登录IP NO
     * @param   tinyint  loginfailure &nbsp; 失败次数 NO
     * @param   varchar joinip &nbsp; 加入IP NO
     * @param   int jointime &nbsp; 加入时间 NO
     * @param   int  past_time &nbsp; 过期时间 NO
     * @param   int  begin_time &nbsp; 开始时间 NO
     * @param   int createtime &nbsp; 创建时间 NO
     * @param   int updatetime &nbsp; 更新时间 NO
     * @param   varchar token &nbsp; Token NO
     * @param   varchar status &nbsp; 状态 NO
     * @param   varchar verification &nbsp; 验证 NO
     * @param   int inviter_mem_info_id &nbsp; 上级用户ID NO
     * @param   varchar inviter_code &nbsp; 用户的邀请码 NO
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
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
