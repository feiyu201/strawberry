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
 * @title 测试接口
 */

class Test extends Api
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
     * @ApiRoute    (/api/Test/add)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   enum select_test &nbsp; 下拉:10=选项一,20=选项二 NO
     * @param   set set_test &nbsp; 爱好(多选):music=音乐,reading=读书,swimming=游泳 NO
     * @param    content &nbsp; 编辑器 NO
     * @param    time123 &nbsp; 时间 NO
     * @param   varchar switch &nbsp; 开关 NO
     * @param   enum state &nbsp; 单选:10=选项一,20=选项二 NO
     * @param   int create_time &nbsp; 时间戳 NO
     * @param   int test1_id &nbsp; 关联id NO
     * @param   varchar test1_ids &nbsp; 关联ids NO
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function add()
    {
        $param = request()->param();
        $model = new \app\common\model\Test();
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
     * @ApiRoute    (/api/Test/edit)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id &nbsp; 主键id Yes
     * @param   enum select_test &nbsp; 下拉:10=选项一,20=选项二 NO
     * @param   set set_test &nbsp; 爱好(多选):music=音乐,reading=读书,swimming=游泳 NO
     * @param    content &nbsp; 编辑器 NO
     * @param    time123 &nbsp; 时间 NO
     * @param   varchar switch &nbsp; 开关 NO
     * @param   enum state &nbsp; 单选:10=选项一,20=选项二 NO
     * @param   int create_time &nbsp; 时间戳 NO
     * @param   int test1_id &nbsp; 关联id NO
     * @param   varchar test1_ids &nbsp; 关联ids NO
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function edit()
    {
        // $id = request()->param('id');
        $param = request()->param();
        $model = new \app\common\model\Test();
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
     * @ApiRoute    (/api/Test/info/id/{id})
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id &nbsp; 主键id Yes
     * @return   int id &nbsp; 主键id
     * @return   enum select_test &nbsp; 下拉:10=选项一,20=选项二 NO
     * @return   set set_test &nbsp; 爱好(多选):music=音乐,reading=读书,swimming=游泳 NO
     * @return    content &nbsp; 编辑器 NO
     * @return    time123 &nbsp; 时间 NO
     * @return   varchar switch &nbsp; 开关 NO
     * @return   enum state &nbsp; 单选:10=选项一,20=选项二 NO
     * @return   int create_time &nbsp; 时间戳 NO
     * @return   int test1_id &nbsp; 关联id NO
     * @return   varchar test1_ids &nbsp; 关联ids NO
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function info()
    {
        $id = request()->param('id');
        $result = Db::name('test')->where('id', $id)->find();
        $result["test1_name"] = Db::name("test1")->where("id",$result["test1_id"])->field('username')->find()['username'];
        $result["test1_names"] = implode(",",Db::name("test1")->where(["id" => ["in",explode(",",$result["test1_ids"])]])->column('username'));
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
     * @ApiRoute    (/api/Test/_list)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int page &nbsp; 第几页 Yes
     * @param   int page &nbsp; 显示条数 Yes
     * @param   int id &nbsp; 主键id Yes
     * @param   enum select_test &nbsp; 下拉:10=选项一,20=选项二 NO
     * @param   set set_test &nbsp; 爱好(多选):music=音乐,reading=读书,swimming=游泳 NO
     * @param    content &nbsp; 编辑器 NO
     * @param    time123 &nbsp; 时间 NO
     * @param   varchar switch &nbsp; 开关 NO
     * @param   enum state &nbsp; 单选:10=选项一,20=选项二 NO
     * @param   int create_time &nbsp; 时间戳 NO
     * @param   int test1_id &nbsp; 关联id NO
     * @param   varchar test1_ids &nbsp; 关联ids NO
     * @return   int id &nbsp; 主键id
     * @return   enum select_test &nbsp; 下拉:10=选项一,20=选项二 NO
     * @return   set set_test &nbsp; 爱好(多选):music=音乐,reading=读书,swimming=游泳 NO
     * @return    content &nbsp; 编辑器 NO
     * @return    time123 &nbsp; 时间 NO
     * @return   varchar switch &nbsp; 开关 NO
     * @return   enum state &nbsp; 单选:10=选项一,20=选项二 NO
     * @return   int create_time &nbsp; 时间戳 NO
     * @return   int test1_id &nbsp; 关联id NO
     * @return   varchar test1_ids &nbsp; 关联ids NO
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function _list()
    {
        $page = $this->request->param('page',1,'intval');
        $limit = $this->request->param('limit',10,'intval');
        $where = [];
        $select_test=request()->param("select_test");
        $set_test=request()->param("set_test");
        $content=request()->param("content");
        $time123=request()->param("time123");
        $switch=request()->param("switch");
        $state=request()->param("state");
        $create_time=request()->param("create_time");
        $test1_id=request()->param("test1_id");
        $test1_ids=request()->param("test1_ids");
        if ($select_test)$where["select_test"] = ['like', '%' .$select_test. '%'];
        if ($set_test)$where["set_test"] = ['like', '%' .$set_test. '%'];
        if ($content)$where["content"] = ['like', '%' .$content. '%'];
        if ($time123)$where["time123"] = ['like', '%' .$time123. '%'];
        if ($switch)$where["switch"] = ['like', '%' .$switch. '%'];
        if ($state)$where["state"] = ['like', '%' .$state. '%'];
        if (request()->param("startcreate_time") && request()->param("endcreate_time"))$where["create_time"] = [['>=', request()->param("startcreate_time")], ['<=', request()->param("endcreate_time")], 'and'];
        if ($test1_id)$where["test1_id"] = ['like', '%' .$test1_id. '%'];
        if ($test1_ids)$where["test1_ids"] = ['like', '%' .$test1_ids. '%'];

        $result = Db::name('test')->where($where)->page($page,$limit)->select()->toArray();
        foreach($result as $elt => $item){

            $result[$elt]["test1_name"] = Db::name("test1")->where("id",$item["test1_id"])->field('username')->find()['username'];
            $result[$elt]["test1_names"] = implode(",",Db::name("test1")->where(["id" => ["in",explode(",",$item["test1_ids"])]])->column('username'));
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
     * @ApiRoute    (/api/Test/del/id/{id})
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id &nbsp; 主键id Yes
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function del()
    {
        $id = request()->param('id');
        $result = Db::name('test')->where('id', $id)->delete();
        if ($result)
            $this->success('删除成功');
        else
            $this->error('删除失败');
    }

}