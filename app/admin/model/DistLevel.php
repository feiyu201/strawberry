<?php

namespace app\admin\model;

use think\Model;
//开启软删除的引用trait
//use think\model\concern\SoftDelete;
class DistLevel extends Model
{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    //开启软删除：
    //功能说明：https://www.kancloud.cn/manual/thinkphp6_0/1037594
    //use SoftDelete;
    //protected $deleteTime = 'delete_time';
    //protected $defaultSoftDelete = 0;


    
public function getMallIdAttr($value,$data)
{
    $this->set('mallIdList',\think\facade\Db::name('mall')->field('id,name')->where('id',$value)->find()) ;
    $this->append(array_merge($this->append,['mallIdList']));
    return $value;
}
public function mall()
{
    return $this->hasOne(Mall::class,'dist_level_ids','id');
}
public function getCreateTimeAttr($value)
{
    if($value&&($this->autoWriteTimestamp=='int')){
        return date('Y-m-d H:i:s',$value);
    }else{
        return $value;
    }
   
}
public function setCreateTimeAttr($value)
{
    return strtotime($value);
}
public function getUpdateTimeAttr($value)
{
    if($value&&($this->autoWriteTimestamp=='int')){
        return date('Y-m-d H:i:s',$value);
    }else{
        return $value;
    }
   
}
public function setUpdateTimeAttr($value)
{
    return strtotime($value);
}
public function getDeleteTimeAttr($value)
{
    if($value&&($this->autoWriteTimestamp=='int')){
        return date('Y-m-d H:i:s',$value);
    }else{
        return $value;
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