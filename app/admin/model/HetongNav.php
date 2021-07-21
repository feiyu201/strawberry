<?php

namespace app\admin\model;

use think\Model;

class HetongNav extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    //protected $updateTime = 'updatetime';


    public function setNavImageAttr($value)
{
    preg_match('/^\/storage/', $value, $match);
    if(empty($match)){
        return '/storage/'.$value;
    }
    return $value;
}
public function getCreateTimeAttr($value)
{
    if($value){
        return date('Y-m-d H:i:s',$value);
    }else{
        return null;
    }
   
}
public function setCreateTimeAttr($value)
{
    return strtotime($value);
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