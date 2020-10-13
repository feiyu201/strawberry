<?php
/**
 * PHP格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 获取目录里的文件，不包括下级文件夹
 * @param string $dir  路径
 * @return array
 */
function get_dir($dir){
    $file = @ scandir($dir);
    foreach ($file as $key){
        if ( $key != ".." && $key != "." ){
            $files[] = $key;
        }
    }
    return $files;
}

/**
 * 获取文件夹中的文件,含目录
 * @param $path
 * @param string $exts
 * @param array $list
 * @return array
 */
function dir_list($path, $exts = '', $list= array()) {
    $path = dir_path($path);
    $files = glob($path.'*');
    foreach ($files as $v) {
        $fileext = fileext($v);
        if (!$exts || preg_match("/\.($exts)/i", $v)) {
            $list[] = $v;
            if (is_dir($v)) {
                $list = dir_list($v, $exts, $list);
            }
        }
    }
    return $list;
}

/**
 * 补齐目录后的/
 * @param $path 目录
 * @return string
 */
function dir_path($path) {
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/') $path = $path.'/';
    return $path;
}

/**
 * 查找文件后缀
 * @param $filename 文件名称
 * @return string 后缀名称（如：html）
 */
function fileext($filename) {
    return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}

/**
 * 删除目录及文件
 * @param $dir
 * @return bool
 */
function dir_delete($dir) {
    $dir = dir_path($dir);
    if (!is_dir($dir)) return FALSE;
    $list = glob($dir.'*');
    foreach ($list as $v) {
        is_dir($v) ? dir_delete($v) : @unlink($v);
    }
    return @rmdir($dir);
}



/***
 * 日期筛选格式化
 * @param $dateran
 * @return array
 */
function get_dateran($dateran){
    if ($dateran) {
        $dateran = explode(" 至 ",$dateran);
    }
    if (is_array($dateran) && count($dateran) == 2) {
        $dateran[0] = strtotime($dateran[0]);
        $dateran[1] = strtotime($dateran[1])+24*60*60-1;
    }
    return $dateran;
}

/**
 * 根据数组中某个字段重新分组
 * @param {dataArr:需要分组的数据；keyStr:分组依据}
 * @return: array
 */
function array_group(array $dataArr, string $keyStr)   :array
{
    $newArr=[];
    foreach ($dataArr as $k => $val) {
        $newArr[$val[$keyStr]][] = $val;
    }
    return $newArr;
}
/**
 * 下拉框公共方法
 * @param array $arrList require 需要循环的数组
 * @param string $name require   name
 * @param bool $require not require  前台是否验证必填
 * @param array $option not require  额外的参数 例如id=type  ['id'=>'type']
 * @return string
 */
if(!function_exists('select')) {
    function select($arrList,$name = '',$sel = '',$require = true,$option = [])
    {
        $str = "<select name='".$name."'  lay-search ";
        if($option){
            foreach ($option as $key => $value){
                $str .= " $key=$value ";
            }
        }
        $str .= $require?"lay-verify='require'>":'>';
        $str.="<option value=''>请选择</option>";
        if($arrList){
            foreach ($arrList as $key => $v){
                $select = $key == $sel ? ' selected':'';
                $str.="<option value={$key} $select>{$v}</option>";
            }
        }
        $str.="</select>";
        return $str;
    }
}

/**
 * 单图片上传公共方法
 * todo 请确保引入的layui.js在调用方法之前
 * @param string $name  require 数据库字段
 * @param string $val  not require 修改时的默认
 * @param string $url not require  所上传的路径 默认 /admin/upload/index
 * @param float|int $size not require  图片大小
 * @param string $exts not require  图片的后缀 如需改动 自行修改
 * @return string
 */
if(!function_exists('upload')) {
    function upload($name,$val='',$url='/admin/upload/index',$size=1024*10,$exts='jpg|jpeg|png|gif'){
        $default = $val?" src=$val":'';
        $uploads =
            "<div class='layui-upload'>
                <button type='button' class='layui-btn' id={$name}>点击上传</button>  
                <input  type='hidden' name='{$name}' >
                <div class='layui-upload-list'>
                    <img class='layui-upload-img' id='{$name}id' name={$name} $default>
                    <p id='{$name}Text'></p>
                </div>
            </div>
            <script>
            layui.use(['layer','upload','form'], function(){
            var $ = layui.jquery
            ,upload = layui.upload;
            var uploadInst = upload.render({
                elem: '#{$name}'
                ,url: '$url' //改成您自己的上传接口
                ,size: $size //限制文件大小，单位 KB
                ,exts: '$exts'  //允许的上传后缀
                ,before: function(obj){
                  //预读本地文件示例，不支持ie8
                  obj.preview(function(index, file, result){
                    $('#{$name}id').attr('src', result); //图片链接（base64）
                  });
                }
                ,done: function(res){
                  //如果上传失败
                  if(res.code > 0){
                    return layer.msg('上传失败');
                  }
                  $('input[name=$name]').val(/storage/+res.path);
                  //上传成功
                }
                ,error: function(){
                  //演示失败状态，并实现重传
                  var demoText = $('#{$name}Text');
                  demoText.html('<span style=color: #FF5722;>上传失败</span> <a class=layui-btn layui-btn-xs demo-reload>重试</a>');

                  demoText.find('.demo-reload').on('click', function(){
                    uploadInst.upload();
                  });
                }
              });
              })</script>";
        return $uploads;
    }
}
/**
 * 多图片上传公共方法
 * todo 请确保引入的layui.js在调用方法之前
 * @param string $name  require 数据库字段
 * @param string $val  not not require 修改时的默认
 * @param string $url not require  所上传的路径方法 默认 /admin/upload/index
 * @param float|int $size not require  图片大小
 * @param string $exts not require  图片的后缀 如需改动 自行修改
 * @return string
 */
if(!function_exists('uploadMul')) {
    function uploadMul($name,$val='',$url='/admin/upload/index',$size=1024*10,$exts='jpg|jpeg|png|gif'){
        $default = '';
        if($val){
            foreach (explode(',',$val) as $value){
                $default .= '<img src='.$value.   ' class=layui-upload-img>';
            }
        }
        $uploads ="           
            <div class='layui-upload'>
			<button type='button' class='layui-btn' id={$name}>多图片上传</button>
			<input  type='hidden' name='{$name}' >
			<blockquote class='layui-elem-quote layui-quote-nm' style='margin-top: 10px;'>
				预览图：
				<div class='layui-upload-list' id='{$name}id'>$default</div>
			</blockquote>
		</div>
            <script>
            layui.use(['layer','upload','form'], function(){
            var $ = layui.jquery
            ,upload = layui.upload;
            var path = []
            var uploadInst = upload.render({
                elem: '#{$name}'
                ,multiple: true
                ,url: '$url' //改成您自己的上传接口
                ,size: $size //限制文件大小，单位 KB
                ,exts: '$exts'
                ,before: function(obj){
                    //预读本地文件示例，不支持ie8
                    obj.preview(function(index, file, result){
                         $('#{$name}id').append('<img src='+ result + '  alt='+ file.name +' class=layui-upload-img>')
                    });
                }
                ,done: function(res){
                  //如果上传失败
                  if(res.code > 0){
                    return layer.msg('上传失败');
                  }
                  path.push(/storage/+res.path)
                  $('input[name=$name]').val(path.toString());
                  //上传成功
                }
              })
              })</script>";
        return $uploads;
    }
}
/**
 * 复选框公共方法
 * @param array $data require  循环的数组
 * @param $name require 数据库字段名称
 * @param string $val not require 修改时的默认数据
 * @return string
 */
if(!function_exists('checkbox')) {
    function checkbox($data,$name,$val = ''){
        if($data){
            $str = '';
            $val && $arr = explode(',',$val);
            foreach ($data as $key => $v){
                $checked = '';
                if(isset($arr)){
                    in_array($key,$arr) && $checked = '  checked';
                }
                $str .= "<input type='checkbox' name='".$name."' value='".$key."' title='".$v."' $checked>";
            }
            return $str;
        }
    }
}
/**
 * 单选按钮公共方法
 * @param array $data require  循环的数组
 * @param $name require 数据库字段名称
 * @param string $val not require 修改时的默认数据
 * @return string
 */
if(!function_exists('radio')) {
    function radio($data,$name,$val = ''){
        if($data){
            $str = '';
            foreach ($data as $key => $v){
                $checked = '';
                if ($val){
                    $checked = $val == $key?'  checked':'';
                }
                $str .= "<input type='radio' name='".$name."' value='".$key."' title='".$v."' $checked>";
            }
            return $str;
        }
    }
}


