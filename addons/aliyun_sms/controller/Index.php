<?php

namespace addons\aliyun_sms\controller;

use addons\aliyun_sms\controller\Base;
use addons\aliyun_sms\model\SmsLog;
use think\facade\Event;

class Index extends Base
{
    public function index()
    {
        $title = '阿里云短信插件';
        return $this->fetch('', [
            'title' => $title,
        ]);
    }

    public function index_json()
    {
        $list = (new SmsLog())->paginate(10);
        return $this->returnSuccess('success', $list);
    }

    public function add()
    {
        $title = '添加';
        return $this->fetch('', [
            'title' => $title,
        ]);
    }

    public function detail()
    {
        $title = '详情';
        return $this->fetch('', [
            'title' => $title,
        ]);
    }

    /**
     * 发送短信通知测试
     * @param $msg_type
     * @param $template_code
     * @param $accept_phone
     * @return array
     * @throws \think\Exception
     */
    public function sms_test()
    {
        $input = input();
        $msg_type        = 'sms_test';
        $accept_phone    = $input['phone'];
        $template_code   = 0;
        $template_params = '草莓快速开发框架,你值得拥有。';

        //钩子事件 短信插件
        $plugin_name = 'Aaliyun';
        Event::listen($plugin_name, 'addons\aliyun_sms\event\AliyunSms');
        $hoddok_res = event($plugin_name, [
            'msg_type'        => $msg_type,
            'template_code'   => $template_code,
            'accept_phone'    => $accept_phone,
            'template_params' => $template_params
        ]);
//        var_dump($hoddok_res);
        //记录短信日志
        (new SmsLog())->save([
            'event'      => $msg_type,
            'title'      => $template_code,
            'content'    => $template_params,
            'mobile'     => $accept_phone,
            'ip'         => request()->ip(),
            'createtime' => time(),
        ]);

    }
}