<?php

namespace addons\wechat\model;

use think\Model;

class Wx extends Model
{

public function getTypeAttr($value)
{
    $arr = array (
  1 => '服务号',
  2 => '订阅号',
);
    $data = explode(',',$value);
    foreach($data as &$item){
        if(array_key_exists($item,$arr)){
            $item = $arr[$item];
        }
    }
    $this->set('type_name',implode(',',$data)) ;
    $this->append(array_merge($this->append,['type_name']));
    return $value;
}

    
public function scopeDateRange($query,$field,$data)
{
    if(is_string($data)){
        $arr  =explode(' - ',$data);
        if(count($arr)==2){
            $query->whereTime($field, 'between', $arr) ;
        }
    }
}
}