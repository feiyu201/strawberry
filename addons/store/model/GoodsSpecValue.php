<?php
namespace addons\store\model;

use think\Model;

class GoodsSpecValue extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'add_time';

    protected static function init(){

    }

    public function spec()
    {
        return $this->hasOneThrough(\addons\store\model\GoodsSpecBase::class,\addons\store\model\GoodsSpecValue::class,'goods_spec_base_id','id','id');
    }

    public function getAddTimeAttr($value)
    {
        return date('Y-m-s h:i:s',$value);
    }
}
