<?php
/**
 * 菜单规则模型
 */
namespace app\common\model;

// 引入框架内置类
use think\facade\Request;

class Admin extends Base
{
	// 开启自动写入时间戳字段
	protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
}