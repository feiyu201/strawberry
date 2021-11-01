<?php

namespace addons\autoapi\model;

use think\Model;

class Autoapi extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 定义字段类型
    protected $type = [
    ];
    // 追加属性
    protected $append = [
    ];


}
