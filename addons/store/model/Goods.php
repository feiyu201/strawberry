<?php
namespace addons\store\model;

use think\Model;

class Goods extends Model
{
    protected $pk = 'id';
//    protected $type = [
//        'add_time'    =>  'datetime',
//    ];
    protected $autoWriteTimestamp = true;
    protected $createTime = 'add_time';

    protected static function init(){

    }


    public function getIsDeductionInventoryAttr($value)
    {
        $status = [0=>'否',1=>'是'];
        return $status[$value];
    }

    public function getIsHomeRecommendedAttr($value)
    {
        $status = [0=>'否',1=>'是'];
        return $status[$value];
    }
    public function getIsShelvesAttr($value)
    {
        $status = [0=>'否',1=>'是'];
        return $status[$value];
    }
    public function roles()
    {
        return $this->belongsToMany(BrandCategoryJoin::class, Brand::class);
    }
    public function getAddTimeAttr($value)
    {
        return date('Y-m-s h:i:s',$value);
    }
}
