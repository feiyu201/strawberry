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
use think\facade\Db;

/**
 * @title 小程序管理接口
 */

class Wxapp extends Api
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
     * @title    添加
     * @author 一笑奈何
     * @desc  (描述信息)
     * @method   (POST/GET)
     * @ApiRoute    (/api/Wxapp/add)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   varchar appid &nbsp; 小程序APPID NO
     * @param   varchar token &nbsp;  NO
     * @param   varchar encodingaeskey &nbsp;  NO
     * @param   tinyint level &nbsp;  NO
     * @param   varchar account &nbsp;  NO
     * @param   varchar original &nbsp;  NO
     * @param   varchar key &nbsp;  NO
     * @param   varchar secret &nbsp;  NO
     * @param   varchar name &nbsp; 小程序名称 NO
     * @param   varchar status &nbsp;  NO
     * @param   int id &nbsp;  NO
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function add()
    {
        $param = request()->param();
        $model = new \app\common\model\Wxapp();
        $result = $model->save($param);
        if ($result)
            $this->success();
        else
            $this->error('添加失败');

    }

    /**
     * @title    编辑
     * @author 一笑奈何
     * @desc  (描述信息)
     * @method   (POST/GET)
     * @ApiRoute    (/api/Wxapp/edit)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id &nbsp; 主键id Yes
     * @param   varchar appid &nbsp; 小程序APPID NO
     * @param   varchar token &nbsp;  NO
     * @param   varchar encodingaeskey &nbsp;  NO
     * @param   tinyint level &nbsp;  NO
     * @param   varchar account &nbsp;  NO
     * @param   varchar original &nbsp;  NO
     * @param   varchar key &nbsp;  NO
     * @param   varchar secret &nbsp;  NO
     * @param   varchar name &nbsp; 小程序名称 NO
     * @param   varchar status &nbsp;  NO
     * @param   int id &nbsp;  NO
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function edit()
    {
        // $id = request()->param('id');
        $param = request()->param();
        $model = new \app\common\model\Wxapp();
        $result = $model->update($param);
        if ($result)
            $this->success();
        else
            $this->error('编辑失败');

    }

    /**
     * @title    查询单条
     * @author 一笑奈何
     * @desc  (描述信息)
     * @method   (POST/GET)
     * @ApiRoute    (/api/Wxapp/info/id/{id})
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id &nbsp; 主键id Yes
     * @return   int id &nbsp; 主键id
     * @return   varchar appid &nbsp; 小程序APPID NO
     * @return   varchar token &nbsp;  NO
     * @return   varchar encodingaeskey &nbsp;  NO
     * @return   tinyint level &nbsp;  NO
     * @return   varchar account &nbsp;  NO
     * @return   varchar original &nbsp;  NO
     * @return   varchar key &nbsp;  NO
     * @return   varchar secret &nbsp;  NO
     * @return   varchar name &nbsp; 小程序名称 NO
     * @return   varchar status &nbsp;  NO
     * @return   int id &nbsp;  NO
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function info()
    {
        $id = request()->param('id');
        $result = Db::name('wxapp')->where('id', $id)->find();
        
        if ($result)
            $this->success('查询成功', $result);
        else
            $this->error('信息不存在');

    }

    /**
     * @title    查询列表
     * @author 一笑奈何
     * @desc  (描述信息)
     * @method   (POST/GET)
     * @ApiRoute    (/api/Wxapp/_list)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int page &nbsp; 第几页 Yes
     * @param   int page &nbsp; 显示条数 Yes
     * @param   int id &nbsp; 主键id Yes
     * @param   varchar appid &nbsp; 小程序APPID NO
     * @param   varchar token &nbsp;  NO
     * @param   varchar encodingaeskey &nbsp;  NO
     * @param   tinyint level &nbsp;  NO
     * @param   varchar account &nbsp;  NO
     * @param   varchar original &nbsp;  NO
     * @param   varchar key &nbsp;  NO
     * @param   varchar secret &nbsp;  NO
     * @param   varchar name &nbsp; 小程序名称 NO
     * @param   varchar status &nbsp;  NO
     * @param   int id &nbsp;  NO
     * @return   int id &nbsp; 主键id
     * @return   varchar appid &nbsp; 小程序APPID NO
     * @return   varchar token &nbsp;  NO
     * @return   varchar encodingaeskey &nbsp;  NO
     * @return   tinyint level &nbsp;  NO
     * @return   varchar account &nbsp;  NO
     * @return   varchar original &nbsp;  NO
     * @return   varchar key &nbsp;  NO
     * @return   varchar secret &nbsp;  NO
     * @return   varchar name &nbsp; 小程序名称 NO
     * @return   varchar status &nbsp;  NO
     * @return   int id &nbsp;  NO
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function _list()
    {
        $page = $this->request->param('page',1,'intval');
        $limit = $this->request->param('limit',10,'intval');
        $where = [];
        $appid=request()->param("appid");
        $token=request()->param("token");
        $encodingaeskey=request()->param("encodingaeskey");
        $level=request()->param("level");
        $account=request()->param("account");
        $original=request()->param("original");
        $key=request()->param("key");
        $secret=request()->param("secret");
        $name=request()->param("name");
        $status=request()->param("status");
        $id=request()->param("id");
        if ($appid)$where["appid"] = ['like', '%' .$appid. '%'];
        if ($token)$where["token"] = ['like', '%' .$token. '%'];
        if ($encodingaeskey)$where["encodingaeskey"] = ['like', '%' .$encodingaeskey. '%'];
        if ($level)$where["level"] = ['like', '%' .$level. '%'];
        if ($account)$where["account"] = ['like', '%' .$account. '%'];
        if ($original)$where["original"] = ['like', '%' .$original. '%'];
        if ($key)$where["key"] = ['like', '%' .$key. '%'];
        if ($secret)$where["secret"] = ['like', '%' .$secret. '%'];
        if ($name)$where["name"] = ['like', '%' .$name. '%'];
        if ($status)$where["status"] = ['like', '%' .$status. '%'];
        if ($id)$where["id"] = ['like', '%' .$id. '%'];

        $result = Db::name('wxapp')->where($where)->page($page,$limit)->select()->toArray();
        foreach($result as $elt => $item){

        }
        if ($result)
            $this->success('查询成功', $result);
        else
            $this->error('信息不存在');
    }

    /**
     * @title    删除
     * @author 一笑奈何
     * @desc  (描述信息)
     * @method   (POST/GET)
     * @ApiRoute    (/api/Wxapp/del/id/{id})
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id &nbsp; 主键id Yes
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function del()
    {
        $id = request()->param('id');
        $result = Db::name('wxapp')->where('id', $id)->delete();
        if ($result)
            $this->success('删除成功');
        else
            $this->error('删除失败');
    }

}