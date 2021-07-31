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
use think\Exception;
use think\facade\Db;
use think\facade\Filesystem;

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
        $userId = request()->param('user_id');
        if (!$userId)
            $this->error('用户ID不能为空');
        if (!$file)
            $this->error('文件不能为空');
        //上传到服务器,
        $path = Filesystem::disk('public')->putFile('upload', $file);
        //结果是 $path = upload/20200825\***.jpg
        //图片路径，Filesystem::getDiskConfig('public','url')功能是获取public目录下的storage，
        $fileName = Filesystem::getDiskConfig('public', 'url') . '/' . str_replace('\\', '/', $path);
        $fileurl = $fileName;
        //结果是 $picCover = storage/upload/20200825/***.jpg
        // 創建userId
//        $outId = 'https://meetyouth.club'; // 固定上传到该用户
        // 获取权限
        $getAccessTokenInfo = cunnarApi::accountAccessToken($userId);
        if (!isset($getAccessTokenInfo['access_token']))
            $this->error($getAccessTokenInfo['error_code']);
        // 创建文件信息
        $result = [];
        $id = $file->hash();

        // 检测是否上传
        $fileInfo = Db::name('deposit_evidence_file')->where(['hash' => $id,'member_id' => $userId])->count('id');
        if ($fileInfo)
            $this->error('文件已存在~勿重复提交');

        $fileName = iconv("UTF-8", "GB2312", app()->getRootPath() . 'public' . $fileName);
        if (file_exists($fileName)) {
            $fp = fopen($fileName, "r");
            if ($fp) {
                $name = basename($fileName);
                $length = filesize($fileName);
                $str = fread($fp, $length);
                fclose($fp);
                $hash = hash('sha256', $str);
                $result = cunnarApi::fileCreate($getAccessTokenInfo['access_token'], $id, $name, $length, $hash, null, null, null, null, null);
            }
        }
        if (isset($result['file_id'])) {
            $data = [];
            $data['file_url'] = $fileurl;
            $data['size'] = $length;
            $data['hash'] = $id;
            $data['file_name'] = $file->getOriginalName();
            $data['file_id'] = $result['file_id'];
            $data['create_time'] = time();
            $data['member_id'] = $userId;
            Db::startTrans();
            try {
                $cz = Db::name('deposit_evidence_file')->insert($data);
                // 上传
                $uploadLength = cunnarApi::fileLength($getAccessTokenInfo['access_token'], $result['file_id']);
                $res = [];
                if (file_exists($fileName)) {
                    $leftLength = filesize($fileName) - $uploadLength ['upload_length'];
                    if ($leftLength > 0) {
                        // 如果没有全部上传，则需要重头开始上传
                        $res = cunnarApi::fileUpload($getAccessTokenInfo['access_token'], $result['file_id'], '0', $fileName);
                    }
                }
                if (isset($res['error']) && !$cz) {
                    Db::rollback();
                    $this->error($res['error_code']);
                }
                Db::commit();
                $this->success('上传成功', $data);
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }

        } else
            $this->error($result['error_code']);
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
        $name = request()->param('username');
        $card = request()->param('card');
        $userId = request()->param('user_id');
        // 获取权限
        $getAccessTokenInfo = cunnarApi::accountAccessToken($userId);
        if (!isset($getAccessTokenInfo['access_token']))
            $this->error($getAccessTokenInfo['error_code']);
        $verify = cunnarApi::accountCardVerify($getAccessTokenInfo['access_token'], $name, $card);
        if (isset($verify['verify']) && $verify['verify'])
            $this->success($verify['verify_msg']);
        else
            $this->success($verify['error_code']);
    }

    public function fileDownload()
    {
        $fileId = request()->param('file_id');
        // 获取权限
        $getAccessTokenInfo = cunnarApi::accountAccessToken('1755861');
        if (!isset($getAccessTokenInfo['access_token']))
            $this->error($getAccessTokenInfo['error_code']);
        $result = cunnarApi::fileDownload($getAccessTokenInfo['access_token'], $fileId);
//        输出文件流
        echo $result;
        //存下来
//        $fp = fopen($result, 'wb+');
//        if ($fp){
//            fwrite($fp, $result);
//            fclose($fp);
//        }
//        halt($result);
    }

    /**
     * @remarks 创建存证用户
     * @author 丶长情
     * @email  zeng1144318071@gmail.com
     * @time   2021/07/29
     */
    public function createUser()
    {
        $outId = request()->param('out_id');
        if (!$outId)
            $this->error('用户标识不能为空');
        // 創建userId
//        $outId = 'https://meetyouth.club'; // 固定上传到该用户
        $userInfo = cunnarApi::accountCreate(null, null, $outId);
        if (!isset($userInfo['user_id']))
            $this->error($userInfo['error_code']);
        $this->success('successful', $userInfo);
    }

    public function fileList()
    {
        $userId = request()->param('user_id');
        $page = request()->param('page') ? request()->param('page') : 1;
        $limit = request()->param('limit') ? request()->param('limit') : 10;
        $list = Db::name('deposit_evidence_file')->where('member_id',$userId)->page($page,$limit)->select();
        $listTotal = Db::name('deposit_evidence_file')->where('member_id',$userId)->count('id');
        $this->success('successful',$list,$listTotal);
    }

}