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