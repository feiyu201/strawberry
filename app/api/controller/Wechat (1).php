<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\facade\Db;
//微信公众/小程序开发接口
class Wechat extends Api
{
    private  $appid = "wx2fcf59e49d737c0e";
    private  $secret = "31b04dc25569388d88e110775a3d3fc7";
    private $sessionKey = "";
    private function decryptData($encryptedData, $iv, &$data)
    {
        if (strlen($this->sessionKey) != 24) {
            return ErrorCode::$IllegalAesKey;
        }
        $aesKey = base64_decode($this->sessionKey);


        if (strlen($iv) != 24) {
            return ErrorCode::$IllegalIv;
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($encryptedData);

        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj = json_decode($result);
        if ($dataObj  == NULL) {
            return ErrorCode::$IllegalBuffer;
        }
        if ($dataObj->watermark->appid != $this->appid) {
            return ErrorCode::$IllegalBuffer;
        }
        $data = $result;
        return ErrorCode::$OK;
    }
    //wx1a65497280b810ce
    //c1872916c038dd33706b223b07813e2c
    public function getWxUserInfo()
    {
        $code   = $this->request->get("code");
        
        $encryptedData = $this->request->get("encryptedData");
        $iv = $this->request->get("iv");
        $url   = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $this->appid . "&secret=" .  $this->secret . "&js_code=" . $code . "&grant_type=authorization_code";
        $rjson  = $this->http_curl($url);
        if (isset($rjson["errcode"])) {
            $this->error('code已过期，请重新登录！');
        }
        $this->sessionKey = $rjson['session_key'];
        $errCode = $this->decryptData($encryptedData, $iv, $data);
        if ($errCode == 0) {
            $this->success('获取用户信息成功！', json_decode($data));
        } else {
            $this->error($errCode);
        }
    }
    public function getWxPhone()
    {
        $code   = $this->request->get("code");
        $encryptedData = $this->request->get("encryptedData");
        $iv = $this->request->get("iv");
        $url   = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $this->appid . "&secret=" .  $this->secret . "&js_code=" . $code . "&grant_type=authorization_code";
        $rjson  = $this->http_curl($url);
        if (isset($rjson["errcode"])) {
            $this->error('code已过期，请重新登录！');
        }
        $this->sessionKey = $rjson['session_key'];
        $errCode = $this->decryptData($encryptedData, $iv, $data);
        if ($errCode == 0) {
            $this->success('获取用户手机号成功！', json_decode($data));
        } else {
            $this->error($errCode);
        }
    }
    private function http_curl($url, $data = [])
    {
        $curl = curl_init();                               //初始化
        curl_setopt($curl, CURLOPT_URL, $url);             //设置抓取的url
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HEADER, 0);         //  设置头文件的信息作为数据流输出  设置为1 的时候，会把HTTP信息打印出来  不要http header 加快效率
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //设置获取的信息以文件流的形式返回，而不是直接输出。 如果设置是0，打印信息就是true
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data, true));
        $data   = curl_exec($curl); //执行命令
        $result = json_decode($data, true);
        if ($data == false) {
            echo "Curl Error:" . curl_error($curl);
            exit();
        }
        curl_close($curl); //关闭URL请求
        return $result;
    }

   
    public function insertion(){
        if (request()->isPost()){
            $data = request()->post();
            
            file_put_contents("/homekuorong/fangchanqiche/test99.txt",$data);
            
            $validate = \think\facade\Validate::rule([
                'username'  => 'require',
                'openid' => 'require',
                //'mobile' => 'require|mobile',
            ]);
            //$validate->message(['mobile.mobile'=>'手机格式错误']);
            if (!$validate->check($data)) {
               return   $validate->getError();
            }
           $user =  new \app\api\model\User;
           $user_s =  $user->save($data);
            if ($user_s)
                return \app\common\http\Json::success('添加成功');
            else
                return \app\common\http\Json::success('添加失败',null,0,500);
        }
    }

    public function info(){
        if (request()->isGet()){
            $openid = request()->get('openid');
            if (!empty($openid)){
               $data =  \app\api\model\User::where(['openid'=>$openid,'status'=>1])->find();
                return \app\common\http\Json::success('数据获取成功',$data,1,200);
            }
            return \app\common\http\Json::success('openid参数获取失败',null,0,500);
        }
        
        
        
        
    }






  /**
     * 我的团队接口
     */
    public function myTeam()
    {
       
        
         $id   = request()->param('user_id');
  
        $field = 'id, nickname, avatar, inviter_mem_info_id';
        $data = Db::name('user')->field($field)->select()->toArray();
        // //直推用户数量
         $subordinate = Db::name('user')->field($field)->where('inviter_mem_info_id', $id)->count();

        //团队列表 3级
        $list = self::getSon($data, $id);

        $data = ['subordinate' => $subordinate, 'count' => count($list), 'list' => $list];
        return \app\common\http\Json::success('数据获取成功',$data,1,200);


    }

    protected static function getSon($data, $p_id = 0, $level = 1, $isClear = true){
        //声明一个静态数组存储结果
        static $res = array();
        //刚进入函数要清除上次调用此函数后留下的静态变量的值，进入深一层循环时则不要清除
        if($isClear == true) $res = array();
        foreach ($data as $v) {
            if ($level == 3) {
                break;
            }
            if($v['inviter_mem_info_id'] == $p_id){
                $v['level'] = $level;
                $res[] = $v;
                self::getSon($data, $v['id'], $level + 1, $isClear = false);
            }
        }
        return $res;
    }









}
