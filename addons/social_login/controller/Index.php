<?php

namespace addons\social_login\controller;

use app\common\controller\AddonBase;
class Index extends AddonBase
{
    public function index()
    {
        var_dump($this->getInfo());
        $this->assign('name','xiaoming');
        return $this->fetch();
    }

    /**
     * 调用方法demo 默认微信登入
     * @param string $type 登入类型 'config/social.php 配置文件'  //'Qq', 'Weixin', 'Sina', 'Baidu', 'Gitee', 'Github', 'Google', 'Facebook', 'Taobao', 'Oschina', 'Douyin', 'Xiaomi', 'Dingtalk'
     */
    public function social_demo($type='Weixin')
    {
        //钩子事件 短信插件
        $plugin_name = 'Aaliyun';
        Event::listen($plugin_name, 'addons\social_login\event\Social');
        $hoddok_res = event($plugin_name,$type);
//        var_dump($hoddok_res);

        //登入成功 登入注册等用户信息业务逻辑

    }
}