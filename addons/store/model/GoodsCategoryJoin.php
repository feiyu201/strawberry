<?php
namespace addons\store\model;

use think\Model;

class GoodsCategoryJoin extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'add_time';

    protected static function init(){

    }
    public function getAddTimeAttr($value)
    {
        return date('Y-m-s h:i:s',$value);
    }
}
