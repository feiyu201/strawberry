<?php
namespace addons\store\model;

use think\Model;

class GoodsPhoto extends Model
{
    protected $pk = 'id';
    protected $add_time = 'create_timestamp';

    protected static function init(){

    }

    public function getIsShowAttr($value)
    {
        $status = [0=>'否',1=>'是'];
        return $status[$value];
    }

}
