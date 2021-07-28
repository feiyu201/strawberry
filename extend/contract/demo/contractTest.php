<?php

require_once("cunnar.api.php");

class contractTest{
	
	/*
	 * 创建用户(使用out_id,也可以使用phone,email,具体查看接口文档)
	 * */
	function  createUser($outId){
		return cunnarApi::accountCreate(null,null,$outId);
	}
	
	/*
	 * 通过userId获取授权
	 * */
	function getAccessToken($userId){
		return cunnarApi::accountAccessToken($userId);
	}
	
	/*
	 * 进行实名认证
	 * */
	function userVerirfy($accessToken,$userInfo){	
		return cunnarApi::accountCardVerify($accessToken,$userInfo['readName'],$userInfo['card']);
	}
	
	/*
	 * 申请签章
	 * */
	function createStamp($accessToken,$userInfo,$type){
		$realName = $userInfo['readName'];
		$card = $userInfo['card'];             
		return (cunnarApi::accountStampCreate($accessToken,$realName,$card,'',$type));
	}
	
	//申请证书
	function incrementItursCert($accessToken){
		return(cunnarApi::accountCertItrus($accessToken));
	}
	
	//创建合同文件
	function createContract($fileName,$id){
		if (file_exists($fileName)){		
			$fp = fopen($fileName, "r");		
			if ($fp){
				$name = basename($fileName);
				$length = filesize($fileName);
				$str = fread($fp,$length);
				fclose($fp);
				$hash = sha1($str);
				return(cunnarApi::contractCreate($id,$name,$length,$hash,null,null,null));
			}
		}
		return(false);
	}
	
	//上传合同文件
	function uploadContract($contractId, $fileName) {
		$uploadLength = cunnarApi::contractLength ( $contractId );
		if (file_exists ( $fileName )) {
			$leftLength = filesize ( $fileName ) - $uploadLength ['upload_length'];
			if ($leftLength> 0) {
				// 如果没有全部上传，则需要重头开始上传
				return (cunnarApi::contractUpload ( $contractId, '0', $fileName ));
			}
		}
		return(false);
	}
	
	//合同签章
	function createContractFile($contarctId,$status,$stampInfos){
		unset($param);
		foreach ($stampInfos as $key=>$value){
			$rec = $value[0] .','.$value[1].','.$value[2].','.$value[3].','.$value[4].','.$value[5];
			if (!isset($param)){
				$param = $rec;
			}else{
				$param = '|'.$param.$rec;
			}
		}
		return cunnarApi::contractStamp($contarctId,$status,$param);
	}
	
	//下载合同
	function downloadContract($contractId,$filePath){
		$rawData = cunnarApi::contractDownload($contractId);
		//存下来
		$fp = fopen($filePath, 'wb+');
		if ($fp){
			fwrite($fp, $rawData);
			fclose($fp);
		}
	}
}


$contractTest = new contractTest();

//创建合同
$fileName="d:\\test.pdf";
$id = strval(time());
$contractId = $contractTest->createContract($fileName, $id);
var_dump($contractId);
$updRet = $contractTest->uploadContract($contractId['contract_id'], $fileName);
var_dump($updRet);

//创建用户(使用out_id,也可以使用phone,email,具体看接口文档)
$user1 = $contractTest->createUser("1");
var_dump($user1);
$user2 = $contractTest->createUser("2");
var_dump($user2);

//获取授权
$accessToken1 = $contractTest->getAccessToken($user1['user_id']);
var_dump($accessToken1);
$accessToken2 = $contractTest->getAccessToken($user2['user_id']);
var_dump($accessToken2);

//实名认证(根据用户需求选择是否需要)
$verifyResult = cunnarApi::accountVerify($accessToken1['access_token']);
if (! ($verifyResult['verify'] == "true" ))
{
	$userInfo['card'] = '140303199901011610';
	$userInfo['readName'] = '用户1';
	$userAuthResult = $contractTest->userVerirfy($accessToken1['access_token'], $userInfo);
	var_dump($userAuthResult);
}

//申请签章和证书
$stampResult = cunnarApi::accountStamp($accessToken1['access_token']); 
if (!($stampResult['stamp'] == "true")){
	$userInfo['card'] = '140303199901011610';
	$userInfo['readName'] = '用户1';
	$stampResult = $contractTest->createStamp($accessToken1, $userInfo, 1);
	var_dump($stampResult);
}

$certInstall = $contractTest->incrementItursCert($accessToken1['access_token']);
var_dump($certInstall);


$stampResult =  cunnarApi::accountStamp($accessToken2['access_token']); 
if (!($stampResult['stamp'] == "true"))
{
	$userInfo['card'] = '14030319990101872';
	$userInfo['readName'] = '用户2';
	$stampResult = $contractTest->createStamp($accessToken2['access_token'], $userInfo, 1);
	var_dump($stampResult);
}
$certInstall = $contractTest->incrementItursCert($accessToken2['access_token']);
var_dump($certInstall);

//合同盖章
$stampInfo = array();
array_push($stampInfo, array($user1['user_id'],100,100,1,10,10));
$stampInfos = $contractTest->createContractFile($contractId['contract_id'], 0, $stampInfo);
var_dump($stampInfos);

$stampInfo2 = array();
array_push($stampInfo2,  array($user2['user_id'],500,50,1,10,10));
$stampInfos = $contractTest->createContractFile($contractId['contract_id'], 1, $stampInfo2);
var_dump($stampInfos);

//下载合同
$fileName = "d:\\test_out.pdf";
$contractTest->downloadContract($contractId['contract_id'], $fileName);

//在线查看pdf
$t=strval(round(microtime(true)*1000));
$result = cunnarApi::getContractPdfView($accessToken1['access_token'],$contractId['contract_id'],$t);
var_dump($result);

// 企业实名认证（第一步：企业实名信息校验）
$key = "";//营业执照号
$key_name = "";//公司名称
$user_name = "";//法人姓名
$card = "";//法人身份证号
$account_no = "";//银行账号
$account_name = "";//银行户名
$branch_no = "";//开户网点联
$card_place = "";//开户地
$result = cunnarApi::accountAuthEnterpriseMolinkinfo($accessToken1['access_token'], $key, $key_name, $user_name, $card, $account_no, $account_name, $branch_no, $card_place);
var_dump($result);

// 企业实名认证（第二步：金额和验证码校验）
$amount = "";//金额（单位：分)
$verify_code = "";//验证码
$result = cunnarApi::accountAuthEnterpriseMolinkamount($accessToken1['access_token'], $amount, $verify_code);
var_dump($result);
?>