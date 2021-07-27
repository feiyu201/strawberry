<?php
require_once("cunnar.api.php");

class webTest
{
	/*
	 * 创建用户(使用out_id,也可以使用phone,email,具体查看接口文档)
	 * */
	function createUser($outId){
		return cunnarApi::accountCreate(null,null,$outId);
	}
	
	/*
	 * 通过userId获取授权
	 * */
	function getAccessToken($userId){
		return cunnarApi::accountAccessToken($userId);
	}
}

$webTest = new webTest();

$user = $webTest->createUser('网页存证用户');
var_dump($user);

$accessToken = $webTest->getAccessToken($user['user_id']);
var_dump($accessToken);

$url = 'www.cunnar.com';
$notifyUrl = 'www.cunnar.com/notify_url.html';

$webpage = cunnarApi::webPage($accessToken['access_token'],$url,$notifyUrl,null,null,null);
var_dump($webpage);
?>