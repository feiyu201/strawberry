<?php

/**
 * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
 * 注意：服务器需要开通fopen配置
 * @param $word 要写入日志里的文本内容 默认值：空值
 */
function logResult($word='') {
	$fp = fopen("log.txt","a");
	flock($fp, LOCK_EX) ;
	fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
	flock($fp, LOCK_UN);
	fclose($fp);
}


function cunnarHttpsRequestNotBuild($url,$data=null,$req="POST",$isGetJson=true){
	try {			
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);	
		if ($req == "POST"){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		}else{
			$url = $url.'?'.http_build_query($data);	
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		curl_close($ch);
		if ($isGetJson){
			return(json_decode($result,true));
		}else{
			return $result;
		}	
	}catch (Exception $e){
		logResult($e);
		return("");
	}
}


function cunnarUploadFile($url,$data,$fileName){
	try {
		$url = $url.'?' .http_build_query($data);
		 
		$cfile = curl_file_create($fileName,'application/pdf',basename($fileName));	
		$imgdata = array('inputStream' => $cfile);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $imgdata); 
		
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		curl_close($ch);
		return(json_decode($result,true));
	}catch (Exception $e){
		logResult($e);
		return("");
	}
}

/**
 * 除去数组中的空值和签名参数
 * @param $para 签名参数组
 * return 去掉空值与签名参数后的新签名参数组
 */
function cunnarParaFilter($para) {
	$para_filter = array();
	foreach ($para as $key => $val) {
		if($key == "sign" || $key == "sign_type" || $val == "")continue;
		else	$para_filter[$key] = $para[$key];
	}
	return $para_filter;
}

/**
 * 对数组排序
 * @param $para 排序前的数组
 * return 排序后的数组
 */
function cunnarArgSort($para) {
	ksort($para);
	reset($para);
	return $para;
}

/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $para 需要拼接的数组
 * return 拼接完成以后的字符串
 */
function cunnarCreateLinkstring($para) {

	$arg  = "";
	foreach ($para as $key => $val) {
		$arg.=$key."=".$val."&";
	}
	//去掉最后一个&字符
	$arg = substr($arg,0,count([$arg])-2);
	
	//如果存在转义字符，那么去掉转义
//	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
	
	return $arg;
}

/**
 * 签名字符串
 * @param $prestr 需要签名的字符串
 * @param $key 私钥
 * return 签名结果
 */
function cunnarMd5Sign($prestr, $key) {
	$prestr = $prestr . $key;
	return md5($prestr);
}

/**
 * 生成签名结果
 * @param $para_sort 已排序要签名的数组
 * return 签名结果字符串
 */
function cunnarBuildRequestMysign($para_sort,$appSecret) {
	//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	$prestr = cunnarCreateLinkstring($para_sort);
	
	$mysign = cunnarMd5Sign($prestr, $appSecret);

	return $mysign;
}

/**
 * 生成要请求给支付宝的参数数组
 * @param $para_temp 请求前的参数数组
 * @return 要请求的参数数组
 */
function cunnarBuildRequestPara($para_temp,$appSecret) {
	//除去待签名参数数组中的空值和签名参数
	$para_filter = cunnarParaFilter($para_temp);
	
	//对待签名参数数组排序
	$para_sort = cunnarArgSort($para_filter);
	
	//生成签名结果
	$mysign = cunnarBuildRequestMysign($para_sort,$appSecret);
	
	//签名结果与签名方式加入请求提交参数组中
	$para_sort['sign'] = $mysign;
	$para_sort['sign_type'] = "MD5";
	return $para_sort;
}




	


?>