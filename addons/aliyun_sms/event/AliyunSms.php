<?php

namespace addons\aliyun_sms\event;
// 注意命名空间规范

use addons\aliyun_sms\common\library\sms\Driver as PluginDriver;
use think\Exception;

class AliyunSms
{
    protected $plugin_name = '';
    protected $engine_plugin_name = '';
    protected $config = [];

    /**
     * @param $data msg_type template_code  accept_phone template_params
     * @return false
     * @throws \think\Exception
     */
    public function handle($data)
    {
        $template_code  = $data['template_code'];
        $accept_phone   = $data['accept_phone'];
        $msg_type       = $data['msg_type'];
        $templateParams = $data['template_params'];

        $this->plugin_name        = 'aliyun_sms';
        $this->engine_plugin_name = 'Aliyun';
        // 获取插件基础信息
        $plugin_config = $this->whetherToUsePlugin($this->plugin_name);
        if (!$plugin_config) {
            return false;
        }

        $config                   = [];
        $config['default']        = $this->engine_plugin_name;//插件引擎 实例化对象
        $plugin_config[$msg_type] = [
            'template_code' => $template_code,
            'accept_phone'  => $accept_phone
        ];
        $config['engine']         = [
            $config['default'] => $plugin_config,//配置参数
        ];
        $this->config             = $config;
        // 实例化对应插件驱动
        $PluginDriver = new PluginDriver($this->config);
        // 调用方法
        $res = $PluginDriver->sendSms($msg_type, $templateParams);
        if (!$res) {
            throw new Exception('发送失败: ' . $PluginDriver->getError());
        }
        return true;
    }

    /**
     * 根据插件标识获取属性
     * @param 插件标识
     * @return:
     */
    private function whetherToUsePlugin(string $file)
    {
        $class = "\\addons\\{$file}\\Plugin";
        if (class_exists($class)) {
            // 容器类的工作由think\Container类完成，但大多数情况我们只需要通过app助手函数或者think\App类即可容器操作
            $object = app($class);
            $info   = $object->getInfo();
            //判断是否开启插件
            if ($info && $info['status'] == 1 && $info['install'] == 1) {
                //返回配置信息
                $info = $object->getConfig();
                return $info;
            }
        }
        return false;
    }
}