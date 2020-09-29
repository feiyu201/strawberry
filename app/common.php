<?php
// 应用公共文件
/**
 * 生成随机数
 * @param $len
 * @return string
 */
function GetRandStr($len) {
	$chars = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k","l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v","w", "x", "y", "z","0", "1", "2","3", "4", "5", "6", "7", "8", "9");
	$charsLen = count($chars) - 1;
	shuffle($chars);
	$output = "";
	for ($i=0; $i<$len; $i++){
		$output .= $chars[mt_rand(0, $charsLen)];
	}
	return $output;
}
/**
 * 过滤数组元素前后空格 (支持多维数组)
 * @param $array 要过滤的数组
 * @return array|string
 */
function trim_array_element($array)
{
	if (!is_array($array))
		return trim($array);
	return array_map('trim_array_element', $array);
}

/**
 * 将数据库中查出的列表以指定的 值作为数组的键名，并以另一个值作为键值
 * @param $arr
 * @param $key_name
 * @return array
 */
function convert_arr_kv($arr, $key_name, $value)
{
	$arr2 = array();
	foreach ($arr as $key => $val) {
		$arr2[$val[$key_name]] = $val[$value];
	}
	return $arr2;
}

function string2array($info)
{
	if ($info == '') return array();
	eval("\$r = $info;");
	return $r;
}

function array2string($info)
{
	//删除空格，某些情况下字段的设置会出现换行和空格的情况
	if (is_array($info)) {
		if (array_key_exists('options', $info)) {
			$info['options'] = trim($info['options']);
		}
	}
	if ($info == '') return '';
	if (!is_array($info)) {
		//删除反斜杠
		$string = stripslashes($info);
	}
	foreach ($info as $key => $val) {
		$string[$key] = stripslashes($val);
	}
	$setup = var_export($string, TRUE);
	return $setup;
}

/**
 * 文本域中换行标签输出
 * @param $info 内容
 * @return mixed
 */
function textareaBr($info)
{
	$info = str_replace("\r\n", "<br />", $info);
	$info = str_replace("\n", "<br />", $info);
	$info = str_replace("\r", "<br />", $info);
	return $info;
}

/**
 * 权限设置选中状态
 * @param $cate  列表
 * @param int $pid 父ID
 * @param $rules 规则
 * @return array
 */
function auth($cate , $pid = 0,$rules){
	$arr = array();
	$rulesArr = explode(',',$rules);
	foreach ($cate as $v){
		if ($v['pid'] == $pid) {
			if (in_array($v['id'], $rulesArr)) {
				$v['checked'] = true;
			}
			$v['open'] = true;
			$arr[]=$v;
			$arr = array_merge($arr, auth($cate, $v['id'], $rules));
		}
	}
	return $arr;
}
/**
 * 节点状态
 * 权限设置选中状态
 * @param $cate  列表
 * @param int $pid 父ID
 * @param $rules 规则
 * @return array
 */
function authNew($cate , $pid = 0,$rules){
	$arr = array();
	$rulesArr = explode(',',$rules);
	foreach ($cate as $v){
		if ($v['pid'] == $pid) {
			$v = array_merge($v, ['field' => 'node', 'spread' => true]);
			
			//$v['open'] = true;
			$subcate = authNew($cate, $v['id'], $rules);
			if($subcate){
				$v['children'] =  $subcate;
			}
			//解决tree回显bug
			if (in_array($v['id'], $rulesArr)&&empty($subcate)) {
				$v['checked'] = true;
			}else{
				$v['checked'] = false;
			}
			unset($v['pid']);
			$arr[]=$v;
		}
	}
	return $arr;
}