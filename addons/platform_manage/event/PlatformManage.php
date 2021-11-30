<?php

namespace addons\platform_manage\event;
// 注意命名空间规范

use think\Exception;

class PlatformManage
{
    protected $plugin_name = '';

    /**
     * @return false
     * type 'wx','miniprogram'
     * @throws \think\Exception
     */
    public function handle($data)
    {
        $this->plugin_name        = 'platform_manage';
        // 获取插件基础信息
        $plugin_config = $this->whetherToUsePlugin($this->plugin_name);
        if (!$plugin_config) {
            return false;
        }

        $type = $data['type'];
        $third_id = $data['third_id'];

        $action = $data['action'];

        if ($action == 'del') {
            Db::name('platform_manage')->where('type', $type)->where('third_id', 'in', $third_id)->delete();
            return;
        }
        $model = new \addons\platform_manage\model\PlatformManage();

        $platInfo = $model->where('type', $type)->where('third_id',$third_id)->find();

        $insert = [
            'name'      => $data['name'],
            'third_id'  => $third_id,
            'type'      => $type
        ];

        if ($platInfo) {
            $insert['id'] = $platInfo['id'];
        }
        $model->save($insert);
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
                //$info = $object->getConfig();
                return $info;
            }
        }
        return false;
    }
}