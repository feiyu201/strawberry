<?php

namespace addons\map\event;
// 注意命名空间规范


class Baidu
{

    public function handle($config = [])
    {
        // 获取插件基础信息
        $map = $this->whetherToUsePlugin('map');
        if(!$map){
            return false;
        }
        $config = [];
        $this->config      = $map;
       return $this->config;
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
            $info = $object->getInfo();
            //判断是否开启插件
            if($info && $info['status'] == 1 && $info['install'] ==1){
                //返回配置信息
                $info = $object->getConfig();
                return $info;
            }
        }
        return false;
    }
}