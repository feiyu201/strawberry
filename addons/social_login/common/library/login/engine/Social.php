<?php

namespace addons\social_login\common\library\login\engine;

use think\Exception;

/**
 * Class Social
 * @package addons\social_login\common\library\login\engine
 */
class Social extends Server
{
    private $type; //'Qq', 'Weixin', 'Sina', 'Baidu', 'Gitee', 'Github', 'Google', 'Facebook', 'Taobao', 'Oschina', 'Douyin', 'Xiaomi', 'Dingtalk'

    /**
     * 构造方法
     * Qiniu constructor.
     * @param $config
     */
    public function __construct($type=null)
    {
        $this->type = $type;
    }

    /**
     * 登入
     * @throws Exception
     */
    public function login()
    {
        if ($this->type == null) {
            throw new Exception('参数错误: ' . $this->engineName);
        }
        // 获取对象实例
        $sns = \liliuwei\social\Oauth::getInstance($this->type);
        //跳转到授权页面
        $this->redirect($sns->getRequestCodeURL());
    }

    /**
     * 授权回调地址 业务逻辑处理
     * @param null $type
     * @param null $code
     */
    public function callback($type = null, $code = null)
    {
        if ($type == null || $code == null) {
            throw new Exception('参数错误: ' . $this->engineName);
        }
        $sns = \liliuwei\social\Oauth::getInstance($type);
        // 获取TOKEN
        $token = $sns->getAccessToken($code);
        //获取当前第三方登录用户信息
        if (is_array($token)) {
            $user_info = \liliuwei\social\GetInfo::getInstance($type, $token);
            dump($user_info);// 获取第三方用户资料
            $sns->openid();//统一使用$sns->openid()获取openid
            //$sns->unionid();//QQ和微信、淘宝可以获取unionid
            dump($sns->openid());
            echo '登录成功!!';
            echo '正在持续开发中，敬请期待!!';
        } else {
            echo "获取第三方用户的基本信息失败";
        }
    }
}

//<a href="{:url('Oauth/login',['type'=>'qq'])}">QQ登录</a>
//<a href="{:url('Oauth/login',['type'=>'sina'])}">新浪微博登录</a>
//<a href="{:url('Oauth/login',['type'=>'weixin'])}">微信登录</a>
//<a href="{:url('Oauth/login',['type'=>'baidu'])}">百度登录</a>
//<a href="{:url('Oauth/login',['type'=>'gitee'])}">gitee登录</a>
//<a href="{:url('Oauth/login',['type'=>'github'])}">github登录</a>
//<a href="{:url('Oauth/login',['type'=>'oschaina'])}">oschaina登录</a>
//<a href="{:url('Oauth/login',['type'=>'google'])}">google登录</a>
//<a href="{:url('Oauth/login',['type'=>'facebook'])}">facebook登录</a>
//<a href="{:url('Oauth/login',['type'=>'taobao'])}">淘宝登录</a>
//<a href="{:url('Oauth/login',['type'=>'douyin'])}">抖音登录</a>
//<a href="{:url('Oauth/login',['type'=>'xiaomi'])}">小米登录</a>
//<a href="{:url('Oauth/login',['type'=>'dingtalk'])}">钉钉登录</a>