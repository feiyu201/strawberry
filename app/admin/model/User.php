<?php

namespace app\admin\model;

use think\Model;

class User extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    //protected $updateTime = 'updatetime';


    public function getPrevtimeAttr($value)
{
    if($value){
        return date('Y-m-d H:i:s',$value);
    }else{
        return null;
    }
   
}
public function setPrevtimeAttr($value)
{
    return strtotime($value);
}
public function getLogintimeAttr($value)
{
    if($value){
        return date('Y-m-d H:i:s',$value);
    }else{
        return null;
    }
   
}
public function setLogintimeAttr($value)
{
    return strtotime($value);
}
public function getJointimeAttr($value)
{
    if($value){
        return date('Y-m-d H:i:s',$value);
    }else{
        return null;
    }
   
}
public function setJointimeAttr($value)
{
    return strtotime($value);
}
public function getPastTimeAttr($value)
{
    if($value){
        return date('Y-m-d H:i:s',$value);
    }else{
        return null;
    }
   
}
public function setPastTimeAttr($value)
{
    return strtotime($value);
}
public function getBeginTimeAttr($value)
{
    if($value){
        return date('Y-m-d H:i:s',$value);
    }else{
        return null;
    }
   
}
public function setBeginTimeAttr($value)
{
    return strtotime($value);
}
public function getCreatetimeAttr($value)
{
    if($value){
        return date('Y-m-d H:i:s',$value);
    }else{
        return null;
    }
   
}
public function setCreatetimeAttr($value)
{
    return strtotime($value);
}
public function getUpdatetimeAttr($value)
{
    if($value){
        return date('Y-m-d H:i:s',$value);
    }else{
        return null;
    }
   
}
public function setUpdatetimeAttr($value)
{
    return strtotime($value);
}

// public function getInviterMemInfoIdAttr($value,$data)
// {
//     $this->set('inviterMemInfoIdList',\think\facade\Db::name('inviter_mem_info')->field('id,name')->where('id',$value)->find()) ;
//     $this->append(array_merge($this->append,['inviterMemInfoIdList']));
//     return $value;
// }

    
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