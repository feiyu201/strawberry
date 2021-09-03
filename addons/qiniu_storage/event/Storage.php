<?php

namespace addons\qiniu_storage\event;
// 注意命名空间规范

use addons\qiniu_storage\common\library\storage\Driver as StorageDriver;

class Storage
{

    public function handle($config = [])
    {
        // 获取插件基础信息
        $qiniu_storage = $this->whetherToUsePlugin('qiniu_storage');
        if(!$qiniu_storage){
            return false;
        }
        $config = [];
        $config['default'] = 'Qiniu';//存储引擎 实例化对象
        $config['engine']  = [
            $config['default'] => $qiniu_storage,//配置参数
        ];
        $this->config      = $config;
        // 实例化存储驱动
        $StorageDriver = new StorageDriver($this->config);
        // 上传图片
        $res = $StorageDriver->upload();
        if (!$res) {
            throw new Exception('上传失败: ' . $StorageDriver->getError());
        }

        //文件组装信息
        $fileInfoArray = $StorageDriver->getFileInfoArray();
        $engineName = $this->config['default'];
        $fileInfoArray['url'] = $config['engine'][$engineName]['domain'] . '/' . $fileInfoArray['fileName'];
        $fileInfoArray['storage'] = $engineName;
        // 上传成功
        return $fileInfoArray;
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