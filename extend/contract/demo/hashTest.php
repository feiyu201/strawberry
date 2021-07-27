<?php

require_once("cunnar.api.php");

class hashTest
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
	
	function createHash($accessToken,$fileName){	
		if (file_exists($fileName)){
			$fp = fopen($fileName, "r");
			if ($fp){
				$name = basename($fileName);
				$length = filesize($fileName);
				$str = fread($fp,$length);
				fclose($fp);
				$hash = sha1($str);
				return(cunnarApi::fileHashCreate($accessToken,$name,$length,$hash,null));
			}
		}
		return(false);	
	}
	
	function createFile($accessToken,$fileName,$id){
		if (file_exists($fileName)){
			$fp = fopen($fileName, "r");
			if ($fp){
				$name = basename($fileName);
				$length = filesize($fileName);
				$str = fread($fp,$length);
				fclose($fp);
				$hash = sha1($str);
				return(cunnarApi::fileCreate($accessToken,$id,$name,$length,$hash,null,null,null,null,null));
			}
		}
		return(false);
	}
	
	function uploadFile($accessToken,$fileId,$fileName){
		$uploadLength = cunnarApi::fileLength($accessToken, $fileId);
		
		if (file_exists ( $fileName )) {
			$leftLength= filesize ( $fileName ) - $uploadLength ['upload_length'];
			if ($leftLength> 0) {
				// 如果没有全部上传，则需要重头开始上传
				return (cunnarApi::fileUpload( $accessToken, $fileId,'0', $fileName ));
			}
		}
		return(false);
	}
	 
}

$hashTest = new hashTest();
$user = $hashTest->createUser('存证用户');
var_dump($user);

$accessToken = $hashTest->getAccessToken($user['user_id']);
var_dump($accessToken);

$fileName = iconv("UTF-8","GB2312",'d:\\hash存证文件.pdf');
$hashFileId = $hashTest->createHash($accessToken['access_token'], $fileName );
var_dump($hashFileId);

//申请出证前需上传原始文件
$fileId = $hashTest->createFile($accessToken['access_token'], $fileName, strval(time()));
var_dump($fileId);

$uploadRet = $hashTest->uploadFile($accessToken['access_token'], $fileId['file_id'], $fileName);
var_dump($uploadRet);

//申请出证据
$t=strval(round(microtime(true)*1000));
$result =cunnarApi::getFileCertify($accessToken['access_token'],$fileId['file_id'],$t,'2');
var_dump($result);
?>