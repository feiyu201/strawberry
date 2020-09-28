<?php
namespace addons\store\model;

use think\Model;

class GoodsCategory extends Model
{
    protected $pk = 'id';

    protected static function init(){

    }

    public function getIsHomeRecommendedAttr($value)
    {
        $status = [0=>'否',1=>'是'];
        return $status[$value];
    }

    public function getIsEnableAttr($value)
    {
        $status = [0=>'否',1=>'是'];
        return $status[$value];
    }
}
