<?php
namespace contract;
require_once("cunnar.core.php");
class cunnarApi{
    //配置
	static private  $apiConfigs = array(
			'appId'       => '202107265429',
			'appSecret'   => '1ac7b34bd7e24bf394ae5014908bd525',
			'url'         =>  'https://t.cunnar.com'
	);
	
	//api入口
	static private  $apiUrls = array(
		'webPage'            =>  '/opencloud/api/account/webpage.json',
		'fileHashCreate'      => '/opencloud/api/account/filehash/create.json',
		'fileDownload'        => '/opencloud/api/account/file/download.json',
		'fileUpload'          => '/opencloud/api/account/file/upload.json', 
		'fileLength'          => '/opencloud/api/account/file/length.json',	
		'fileCreate'          => '/opencloud/api/account/file/create.json' ,
		'accountExist'        => '/opencloud/api/account/exist.json',	
		'accountAccessToken'  => '/opencloud/api/account/access_token.json',
		'accountCreate'       => '/opencloud/api/account/create.json',
		'accountVerify'       => '/opencloud/api/account/verify.json',
		'accountStamp'        => '/opencloud/api/account/stamp.json',
		'accountCertItrus'    =>  '/opencloud/api/account/cert/itrus.json',
		'accountCardVerify'   =>  '/opencloud/api/account/card_verify.json',
		'contractCreate'      => '/opencloud/api/contract/create.json',
		'contractUpload'      => '/opencloud/api/contract/upload.json',
		'contractLength'      => '/opencloud/api/contract/length.json',
		'contractStamp'       => '/opencloud/api/contract/stamp.json',
		'contractDownload'    => '/opencloud/api/contract/download.json',	
			
		'getContractPdfView'  =>  '/opencloud/api/account/hcontract',
		'getFileCertify'      =>  '/opencloud/api/account/hcertify',
		'accountAuthEnterpriseMolinkinfo'=>   '/opencloud/api/account/auth/enterprise/molinkinfo.json',
		'accountAuthEnterpriseMolinkamount'=> '/opencloud/api/account/auth/enterprise/molinkamount.json'                    
	);
	
	static  function  webPage($accessToken,$url,$notifyUrl,$browser,$cookie,$extraParam){
		$param['access_token'] = $accessToken;
		$param['url'] = $url;
		$param['cookie'] = $cookie;
		$param['browser'] = $browser;
		$param['notify_url'] = $notifyUrl;
		$param['extra_param'] = $extraParam;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data));
		
	}
	
	static function fileHashCreate($accessToken,$name,$length,$hash,$fileCreateTime){
		$param['access_token'] = $accessToken;
		$param['name'] = $name;
		$param['length'] = $length;
		$param['hash'] = $hash;
		$param['file_create_time'] = $fileCreateTime;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data));
	}
	
	static function  getFileCertify($accessToken,$fileId,$t,$type){
		$param['access_token'] = $accessToken;
		$param['file_ids'] = $fileId;
		$param['t'] = $t;
		$param['type'] = $type;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		$url = $url.'?'.http_build_query($request_data);
		return ($url);
	}
	
	static function fileDownload($accessToken,$fileId){
		$param['access_token'] = $accessToken;
		$param['file_id'] = $fileId;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data,'GET',false));
	}
	
	static  function fileUpload($accessToken,$fileId,$index,$fileName){
		$param['access_token'] = $accessToken;
		$param['file_id'] = $fileId;
		$param['index'] = $index;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarUploadFile($url,$request_data,$fileName));
	}
	
	static function fileLength($accessToken,$fileId){
		$param['access_token'] = $accessToken;
		$param['file_id'] = $fileId;
 
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data,'GET'));
	}
	
	static function fileCreate($accessToken,$id,$name,$length,$hash,$label,$comment,$fileCreateTime,$notifyUrl,$extraParam){
		$param['access_token'] = $accessToken;
		$param['id'] = $id;
		$param['name'] = $name;
		$param['length'] = $length;
		$param['hash'] = $hash;
		$param['label'] = $label;
		$param['comment'] = $comment;
		$param['file_create_time'] = $fileCreateTime;
		$param['notify_url'] = $notifyUrl;
		$param['extra_param'] = $extraParam;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data));
	}
	
	static function accountCardVerify($accessToken,$realName,$card){
		$param['access_token'] = $accessToken;
		$param['real_name'] = $realName;
		$param['card'] = $card;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data));
	}
	
	static function getContractPdfView($accessToken,$contractId,$t){
				
		$param['access_token'] = $accessToken;
		$param['contract_id'] = $contractId;
		$param['t'] = $t;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		$url = $url.'?'.http_build_query($request_data);
		return ($url);
	}
	
	static function contractLength($contractId){
		$param['contract_id'] = $contractId;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data,"GET"));
	}
	
	static function contractUpload($contractId,$index,$fileName){
		$param['contract_id'] = $contractId;
		$param['index'] = $index;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarUploadFile($url,$request_data,$fileName));
	}
	
	static function contractDownload($contractId){
		$param['contract_id'] = $contractId;
		 
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data,"GET",false));
	}
	
	static function contractStamp($contractId,$status,$params){
		$param['contract_id'] = $contractId;
		$param['status'] = $status;
		$param['param'] = $params;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data));
	}
	
	static function accountCertItrus($access_token){
		$param['access_token'] = $access_token;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data));
	}
	
	static function accountStamp($access_token){
		$param['access_token'] = $access_token;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data,"GET"));
	}
	
	
	static function accountStampCreate($access_token,$realName,$card,$stamp,$type){
		$param['access_token'] = $access_token;
		$param['real_name'] = $realName;
		$param['card'] = $card;
		$param['stamp'] = $stamp;
		$param['type'] = $type;
		
		$url = self::$apiConfigs['url']. self::$apiUrls['accountStamp'];//和获取一样的解耦
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data));
	}
	
	static function accountExist($phone,$email,$outId){
		$param['phone'] = $phone;
		$param['email'] = $email;
		$param['out_id'] = $outId;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data,"GET"));
	}
	
	static function accountAccessToken($userId){
		$param['user_id'] = $userId;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data,"GET"));
	}
	
	static function accountVerify($accessToken){
		$param['access_token'] = $accessToken;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data,"GET"));
	}
	
	static  function accountCreate($phone,$email,$out_id){
		
		$param['phone'] = $phone;
		$param['email'] = $email;
		$param['out_id'] = $out_id;

		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data));	
	}
	
	static function contractCreate($id,$name,$length,$hash,$label,$comment,$fileCreateTime){			
		$param['id'] = $id;
		$param['name'] = $name;
		$param['length'] = $length;
		$param['hash']= $hash;
		$param['label'] =$label;
		$param['comment'] = $comment;
		$param['file_create_time']=$fileCreateTime;	
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];		
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);	
		return (cunnarHttpsRequestNotBuild($url,$request_data));	
	}
	 
	static function accountAuthEnterpriseMolinkinfo($accessToken,$key,$key_name,$user_name,$card,$account_no,$account_name,$branch_no,$card_place){
	 	$param['access_token'] = $accessToken;
	 	$param['key'] = $key;
	 	$param['key_name'] = $key_name;
	 	$param['user_name'] = $user_name;
	 	$param['card'] = $card;
	 	$param['account_no'] = $account_no;
	 	$param['account_name'] = $account_name;
	 	$param['branch_no'] = $branch_no;
	 	$param['card_place'] = $card_place;
	 	
	 	$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
	 	$param['app_key'] = self::$apiConfigs['appId'];
	 	$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
	 	return (cunnarHttpsRequestNotBuild($url,$request_data));
	}
	
	static function accountAuthEnterpriseMolinkamount($accessToken,$amount,$verify_code){
		$param['access_token'] = $accessToken;
		$param['amount'] = $amount;
		$param['verify_code'] = $verify_code;
		
		$url = self::$apiConfigs['url']. self::$apiUrls[__FUNCTION__];
		$param['app_key'] = self::$apiConfigs['appId'];
		$request_data = cunnarBuildRequestPara($param,self::$apiConfigs['appSecret']);
		return (cunnarHttpsRequestNotBuild($url,$request_data));
	}
	
};


?>