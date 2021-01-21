<?php
namespace app\api\model;

use think\Model;

/**
 * 用户模型
 */
class User extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    /**
     * @param $mobile 手机号
     * @param $password 密码
     */
    static function login($mobile)
    {
    	return self::where(['mobile'=>$mobile])->find()->toArray();
    }


    /**
     * @param $param 注册参数
     */
    function register($param)
    {
    	return $this->strict(true)->insert($param);
    }

}