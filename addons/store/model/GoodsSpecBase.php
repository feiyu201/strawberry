<?php
namespace addons\store\model;

use think\Model;

class GoodsSpecBase extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'add_time';

    protected static function init(){

    }
    public function profile()
    {
        return $this->hasOne(GoodsSpecValue::class, 'goods_spec_base_id');
    }
    public function getAddTimeAttr($value)
    {
        return date('Y-m-s h:i:s',$value);
    }
}
