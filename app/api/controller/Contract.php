<?php
//  *+-----------------------------------------------------------------------
//  *                      .::::.
//  *                    .::::::::.            | Author: 丶长情
//  *                    :::::::::::           | Email: zeng1144318071@gmail.com
//  *                 ..:::::::::::'           | Datetime: 2020/08/15
//  *             '::::::::::::'               | Remarks:
//  *                .::::::::::
//  *           '::::::::::::::..
//  *                ..::::::::::::.
//  *              ``::::::::::::::::
//  *               ::::``:::::::::'        .:::.
//  *              ::::'   ':::::'       .::::::::.
//  *            .::::'      ::::     .:::::::'::::.
//  *           .:::'       :::::  .:::::::::' ':::::.
//  *          .::'        :::::.:::::::::'      ':::::.
//  *         .::'         ::::::::::::::'         ``::::.
//  *     ...:::           ::::::::::::'              ``::.
//  *   ```` ':.          ':::::::::'                  ::::..
//  *                      '.:::::'                    ':'````..
//  * +-----------------------------------------------------------------------
namespace app\api\controller;

use app\common\controller\Api;
use contract\cunnarApi;
use think\facade\Db;

/**
 * @title 合同
 */
class Contract extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ["*"];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ["*"];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * @remarks 添加专线地址
     * @author 丶长情
     * @email  zeng1144318071@gmail.com
     * @time   2020/01/12
     */
    public function upload()
    {
        $file = \think\facade\Request::file('file');
        if (!$file)
            $this->error('文件不能为空');
        $fileurl = \think\facade\Filesystem::putFile('topic', $file);
        // 創建userId
        $outId = 'https://meetyouth.club'; // 固定上传到该用户
        $userInfo = cunnarApi::accountCreate(null, null, $outId);
        if (!isset($userInfo['user_id']))
            $this->error($userInfo['error_code']);
        // 获取权限
        $getAccessTokenInfo = cunnarApi::accountAccessToken($userInfo['user_id']);
        if (!isset($getAccessTokenInfo['access_token']))
            $this->error($getAccessTokenInfo['error_code']);
        // 创建文件信息
        $id = $file->hash();
        $name = $fileurl;
        $length = $file->getSize();
        $hash = bin2hex($file);
        $createFile = cunnarApi::fileCreate($getAccessTokenInfo['access_token'], $id, $name, $length, $hash, null, null, null, null, null);
        if (isset($createFile['file_id'])){
            $data = [];
            $data['file_url'] = $fileurl;
            $data['size'] = $length;
            $data['hash'] = $id;
            $data['file_name'] = $file->getOriginalName();
            $data['file_id'] = $createFile['file_id'];
            $this->success('保存成功',$data);
        }
        else
            $this->error($createFile['error_code']);
    }

    /**
     * @remarks 存证云实名认证
     * @author 丶长情
     * @email  zeng1144318071@gmail.com
     * @time   2021/07/27
     */
    public function userVerify()
    {
        // 测试账号已认证
        $name = '';
        $card = '';
        // 获取权限
        $getAccessTokenInfo = cunnarApi::accountAccessToken('1755861');
        if (!isset($getAccessTokenInfo['access_token']))
            $this->error($getAccessTokenInfo['error_code']);
        $verify = cunnarApi::accountCardVerify($getAccessTokenInfo['access_token'],$name,$card);
        if (isset($verify['verify']) && $verify['verify'])
            $this->success($verify['verify_msg']);
        else
            $this->success($verify['error_code']);
    }

}