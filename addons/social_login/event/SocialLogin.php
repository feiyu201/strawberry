<?php

namespace addons\social_login\event;
// 注意命名空间规范

use addons\social_login\common\library\login\Driver as PluginDriver;
use think\Exception;

class SocialLogin
{
    protected $plugin_name = '';
    protected $engine_plugin_name = '';

    /**
     * @param $type 'config/social.php 配置文件'  //'Qq', 'Weixin', 'Sina', 'Baidu', 'Gitee', 'Github', 'Google', 'Facebook', 'Taobao', 'Oschina', 'Douyin', 'Xiaomi', 'Dingtalk'
     * @return bool
     * @throws Exception
     */
    public function handle($type)
    {
        $this->plugin_name        = 'social_login';
        $this->engine_plugin_name = 'Social';
        // 获取插件基础信息
        $plugin_config = $this->whetherToUsePlugin($this->plugin_name);
        if (!$plugin_config) {
            return false;
        }

        $config                   = [];
        $config['default']        = $this->engine_plugin_name;//插件引擎 实例化对象
        $config['engine']         = [
            $config['default'] => $type,//配置参数
        ];

        // 实例化对应插件驱动
        $PluginDriver = new PluginDriver($config);
        // 调用方法
        $res = $PluginDriver->login();
        if (!$res) {
            throw new Exception('登入失败: ' . $PluginDriver->getError());
        }

        //返回登入信息 进行用户信息注册登入等表业务逻辑
        return $res;
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