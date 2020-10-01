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
 * @title Api自动生成接口
 */

class Lazy extends Api
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
     * @ApiRoute    (/api/Lazy/add)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   varchar table_name - 表名 NO
     * @param   int create_time - 生成接口时间 YES
     * @param   int admin_id - 操作人ID YES
     * @ApiReturn   ({
         'code':'1',
         'msg':'返回成功'
        })
     * */
    public function add()
    {
        $param = request()->param();
        $model = new \app\common\model\Lazy();
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
     * @ApiRoute    (/api/Lazy/edit)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id - 主键id true
     * @param   varchar table_name - 表名 NO
     * @param   int create_time - 生成接口时间 YES
     * @param   int admin_id - 操作人ID YES
     * @ApiReturn   ({
         'code':'1',
         'msg':'返回成功'
        })
     * */
    public function edit()
    {
        // $id = request()->param('id');
        $param = request()->param();
        $model = new \app\common\model\Lazy();
        $result = $model->isUpdate()->save($param);
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
     * @ApiRoute    (/api/Lazy/info/id/{id})
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id - 主键id true
     * @ApiReturnParams   ("id", "int", true, "主键id")
     * @ApiReturnParams   ("table_name", "varchar", NO, "表名")
     * @ApiReturnParams   ("create_time", "int", YES, "生成接口时间")
     * @ApiReturnParams   ("admin_id", "int", YES, "操作人ID")
     * @ApiReturn   ({
         'code':'1',
         'msg':'返回成功'
        })
     * */
    public function info()
    {
        $id = request()->param('id');
        $result = Db::name('lazy')->where('id', $id)->find();
        $result["admin_name"] = Db::name("admin")->where("id",$result["admin_id"])->field('username')->find()['username'];
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
     * @ApiRoute    (/api/Lazy/_list)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="page", type="int", required=true, description="第几页")
     * @ApiParams   (name="limit", type="int", required=true, description="显示条数")
     * @param   int id - 主键id true
     * @param   varchar table_name - 表名 NO
     * @param   int create_time - 生成接口时间 YES
     * @param   int admin_id - 操作人ID YES
     * @ApiReturnParams   ("id", "int", true, "主键id")
     * @ApiReturnParams   ("table_name", "varchar", NO, "表名")
     * @ApiReturnParams   ("create_time", "int", YES, "生成接口时间")
     * @ApiReturnParams   ("admin_id", "int", YES, "操作人ID")
     * @ApiReturn   ({
         'code':'1',
         'msg':'返回成功'
        })
     * */
    public function _list()
    {
        $page = $this->request->param('page',1,'intval');
        $limit = $this->request->param('limit',10,'intval');
        $where = [];
        $table_name=request()->param("table_name");
        $create_time=request()->param("create_time");
        $admin_id=request()->param("admin_id");
        if ($table_name)$where["table_name"] = ['like', '%' .$table_name. '%'];
        if (request()->param("startcreate_time") && request()->param("endcreate_time"))$where["create_time"] = [['>=', request()->param("startcreate_time")], ['<=', request()->param("endcreate_time")], 'and'];
        if ($admin_id)$where["admin_id"] = ['like', '%' .$admin_id. '%'];

        $result = Db::name('lazy')->where($where)->page($page,$limit)->select()->toArray();
        foreach($result as $elt => $item){

            $result[$elt]["admin_name"] = Db::name("admin")->where("id",$item["admin_id"])->field('username')->find()['username'];
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
     * @ApiRoute    (/api/Lazy/del/id/{id})
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id - 主键id true
     * @ApiReturn   ({
         'code':'1',
         'msg':'返回成功'
        })
     * */
    public function del()
    {
        $id = request()->param('id');
        $result = Db::name('lazy')->where('id', $id)->delete();
        if ($result)
            $this->success('删除成功');
        else
            $this->error('删除失败');
    }

}