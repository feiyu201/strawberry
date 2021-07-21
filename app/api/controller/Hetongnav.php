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
 * @title 导航接口
 */

class Hetongnav extends Api
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
     * @ApiRoute    (/api/Hetongnav/add)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   varchar name null 导航名称 NO
     * @param   varchar nav_image null 导航图标 YES
     * @param   varchar navurl null 导航链接 NO
     * @param   varchar switch null 是否显示 NO
     * @param   int create_time null create_time NO
     * @return   int code null 返回参数 200
     * @return   string message null 返回信息 successful
     * @return   array data null 返回数据 successful
     * */
    public function add()
    {
        $param = request()->param();
        $model = new \app\common\model\HetongNav();
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
     * @ApiRoute    (/api/Hetongnav/edit)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id null 主键id Yes
     * @param   varchar name null 导航名称 NO
     * @param   varchar nav_image null 导航图标 YES
     * @param   varchar navurl null 导航链接 NO
     * @param   varchar switch null 是否显示 NO
     * @param   int create_time null create_time NO
     * @return   int code null 返回参数 200
     * @return   string message null 返回信息 successful
     * @return   array data null 返回数据 successful
     * */
    public function edit()
    {
        // $id = request()->param('id');
        $param = request()->param();
        $model = new \app\common\model\HetongNav();
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
     * @ApiRoute    (/api/Hetongnav/info/id/{id})
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id null 主键id Yes
     * @return   int id null 主键id
     * @return   varchar name null 导航名称 NO
     * @return   varchar nav_image null 导航图标 YES
     * @return   varchar navurl null 导航链接 NO
     * @return   varchar switch null 是否显示 NO
     * @return   int create_time null create_time NO
     * @return   int code null 返回参数 200
     * @return   string message null 返回信息 successful
     * @return   array data null 返回数据 successful
     * */
    public function info()
    {
        $id = request()->param('id');
        $result = Db::name('hetong_nav')->where('id', $id)->find();
        $result["nav_image"] = url($result["nav_image"],[],null,true)->build();

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
     * @ApiRoute    (/api/Hetongnav/_list)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int page null 第几页 Yes
     * @param   int limit null 显示条数 Yes
     * @param   int id null 主键id Yes
     * @param   varchar name null 导航名称 NO
     * @param   varchar nav_image null 导航图标 YES
     * @param   varchar navurl null 导航链接 NO
     * @param   varchar switch null 是否显示 NO
     * @param   int create_time null create_time NO
     * @return   int id null 主键id
     * @return   varchar name null 导航名称 NO
     * @return   varchar nav_image null 导航图标 YES
     * @return   varchar navurl null 导航链接 NO
     * @return   varchar switch null 是否显示 NO
     * @return   int create_time null create_time NO
     * @return   int code null 返回参数 200
     * @return   string message null 返回信息 successful
     * @return   array data null 返回数据 successful
     * */
    public function _list()
    {
        $page = $this->request->param('page',1,'intval');
        $limit = $this->request->param('limit',10,'intval');
        $where = [];
        $name=request()->param("name");
        $nav_image=request()->param("nav_image");
        $navurl=request()->param("navurl");
        $switch=request()->param("switch");
        $create_time=request()->param("create_time");
        if ($name)$where[] = ['name', 'like', '%' .$name. '%'];
        if ($nav_image)$where[] = ['nav_image', 'like', '%' .$nav_image. '%'];
        if ($navurl)$where[] = ['navurl', 'like', '%' .$navurl. '%'];
        if ($switch)$where[] = ['switch', 'like', '%' .$switch. '%'];
        if (request()->param("startcreate_time") && request()->param("endcreate_time"))$where["create_time"] = [['>=', request()->param("startcreate_time")], ['<=', request()->param("endcreate_time")], 'and'];

        $result = Db::name('hetong_nav')->where($where)->page($page,$limit)->select()->toArray();
        foreach($result as $elt => $item){
$result[$elt]["nav_image"] = url($item["nav_image"],[],null,true)->build();

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
     * @ApiRoute    (/api/Hetongnav/del/id/{id})
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id null 主键id Yes
     * @return   int code null 返回参数 200
     * @return   string message null 返回信息 successful
     * @return   array data null 返回数据 successful
     * */
    public function del()
    {
        $id = request()->param('id');
        $result = Db::name('hetong_nav')->where('id', $id)->delete();
        if ($result)
            $this->success('删除成功');
        else
            $this->error('删除失败');
    }

}