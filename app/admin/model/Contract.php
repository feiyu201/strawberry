<?php

namespace app\admin\model;

use think\Model;

class Contract extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    //protected $updateTime = 'updatetime';


    public function setContractFileAttr($value)
{
    if(is_string($value)){
        $value  = explode('|',$value);
    }
    return implode('|',$value);
}

public function getContractFileAttr($value)
{
    return array_filter(explode('|',$value));
}

public function getCategoryIdAttr($value,$data)
{
    $this->set('categoryIdList',\think\facade\Db::name('category')->field('id,name')->where('id',$value)->find()) ;
    $this->append(array_merge($this->append,['categoryIdList']));
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
public function getUpdateTimeAttr($value)
{
    if($value){
        return date('Y-m-d H:i:s',$value);
    }else{
        return null;
    }
   
}
public function setUpdateTimeAttr($value)
{
    return strtotime($value);
}
public function getDeleteTimeAttr($value)
{
    if($value){
        return date('Y-m-d H:i:s',$value);
    }else{
        return null;
    }
   
}
public function setDeleteTimeAttr($value)
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