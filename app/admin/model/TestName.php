<?php

namespace app\admin\model;

use think\Model;

class TestName extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';


    public function getSelectTestAttr($value)
{
    $arr = array (
  10 => '选项一',
  20 => '选项二',
);
    $data = explode(',',$value);
    foreach($data as &$item){
        if(array_key_exists($item,$arr)){
            $item = $arr[$item];
        }
    }
    $this->set('select_test_name',implode(',',$data)) ;
    $this->append(array_merge($this->append,['select_test_name']));
    return $value;
}
public function setSetTestAttr($value)
{
    $str = [];
    foreach($value as $key=>$item){
        if($item=='on'){
            $str[]= $key;
        }
    }
    return implode(',',$str);
}
public function getSetTestAttr($value)
{
    $arr = array (
  'music' => '音乐',
  'reading' => '读书',
  'swimming' => '游泳',
);
    $data = explode(',',$value);
    foreach($data as &$item){
        if(array_key_exists($item,$arr)){
            $item = $arr[$item];
        }
    }
    $this->set('set_test_name',implode(',',$data)) ;
    $this->append(array_merge($this->append,['set_test_name']));
    return $value;
}
public function getStateAttr($value)
{
    $arr = array (
  10 => '选项一',
  20 => '选项二',
);
    $data = explode(',',$value);
    foreach($data as &$item){
        if(array_key_exists($item,$arr)){
            $item = $arr[$item];
        }
    }
    $this->set('state_name',implode(',',$data)) ;
    $this->append(array_merge($this->append,['state_name']));
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
public function getCreate1timeAttr($value)
{
    if($value){
        return date('Y-m-d H:i:s',$value);
    }else{
        return null;
    }
   
}
public function setCreate1timeAttr($value)
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
public function getCreateAtAttr($value)
{
    if($value){
        return date('Y-m-d H:i:s',$value);
    }else{
        return null;
    }
   
}
public function setCreateAtAttr($value)
{
    return strtotime($value);
}
public function setImgAttr($value)
{
    preg_match('/^\/storage/', $value, $match);
    if(empty($match)){
        return '/storage/'.$value;
    }
    return $value;
}
public function setImageAttr($value)
{
    preg_match('/^\/storage/', $value, $match);
    if(empty($match)){
        return '/storage/'.$value;
    }
    return $value;
}
public function setImagesAttr($value)
{
    if(is_string($value)){
        $value  = explode('|',$value);
    }
   
    if(is_array($value)){
        foreach($value as &$item){
            preg_match('/^\/storage/', $item, $match);
            if(empty($match)){
                $item =  '/storage/'.$item;
            }
        }
    }
    return implode('|',$value);
}

public function getImagesAttr($value)
{
    return array_filter(explode('|',$value));
}
public function setImgsAttr($value)
{
    if(is_string($value)){
        $value  = explode('|',$value);
    }
   
    if(is_array($value)){
        foreach($value as &$item){
            preg_match('/^\/storage/', $item, $match);
            if(empty($match)){
                $item =  '/storage/'.$item;
            }
        }
    }
    return implode('|',$value);
}

public function getImgsAttr($value)
{
    return array_filter(explode('|',$value));
}

public function getTest1NameIdAttr($value,$data)
{
    $this->set('test1NameIdList',\think\facade\Db::name('test1_name')->field('id,name')->where('id',$value)->find()) ;
    $this->append(array_merge($this->append,['test1NameIdList']));
    return $value;
}

public function getTest1NameIdsAttr($value,$data)
{
    $this->set('test1NameIdsList',\think\facade\Db::name('test1_name')->field('id,name')->where('id','in',explode(',',$value))->select()) ;
    $this->append(array_merge($this->append,['test1NameIdsList']));
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