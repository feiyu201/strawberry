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
 * @title 管理员表接口
 **/

class Admin extends Api
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
     * @ApiRoute    (/api/Admin/add)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   varchar username - 用户名 NO
     * @param   varchar nickname - 昵称 NO
     * @param   varchar password - 密码 NO
     * @param   varchar salt - 密码盐 NO
     * @param   varchar avatar - 头像 NO
     * @param   varchar email - 电子邮箱 NO
     * @param   tinyint loginfailure - 失败次数 NO
     * @param   int logintime - 登录时间 YES
     * @param   varchar loginip - 登录IP YES
     * @param   int createtime - 创建时间 YES
     * @param   int updatetime - 更新时间 YES
     * @param   varchar token - Session标识 NO
     * @param   varchar status - 状态 NO
     * @ApiReturn   ({
         'code':'1',
         'msg':'返回成功'
        })
     * */
    public function add()
    {
        $param = request()->param();
        $model = new \app\common\model\Admin();
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
     * @ApiRoute    (/api/Admin/edit)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id - 主键id true
     * @param   varchar username - 用户名 NO
     * @param   varchar nickname - 昵称 NO
     * @param   varchar password - 密码 NO
     * @param   varchar salt - 密码盐 NO
     * @param   varchar avatar - 头像 NO
     * @param   varchar email - 电子邮箱 NO
     * @param   tinyint loginfailure - 失败次数 NO
     * @param   int logintime - 登录时间 YES
     * @param   varchar loginip - 登录IP YES
     * @param   int createtime - 创建时间 YES
     * @param   int updatetime - 更新时间 YES
     * @param   varchar token - Session标识 NO
     * @param   varchar status - 状态 NO
     * @ApiReturn   ({
         'code':'1',
         'msg':'返回成功'
        })
     * */
    public function edit()
    {
        // $id = request()->param('id');
        $param = request()->param();
        $model = new \app\common\model\Admin();
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
     * @ApiRoute    (/api/Admin/info/id/{id})
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id - 主键id true
     * @ApiReturnParams   ("id", "int", true, "主键id")
     * @ApiReturnParams   ("username", "varchar", NO, "用户名")
     * @ApiReturnParams   ("nickname", "varchar", NO, "昵称")
     * @ApiReturnParams   ("password", "varchar", NO, "密码")
     * @ApiReturnParams   ("salt", "varchar", NO, "密码盐")
     * @ApiReturnParams   ("avatar", "varchar", NO, "头像")
     * @ApiReturnParams   ("email", "varchar", NO, "电子邮箱")
     * @ApiReturnParams   ("loginfailure", "tinyint", NO, "失败次数")
     * @ApiReturnParams   ("logintime", "int", YES, "登录时间")
     * @ApiReturnParams   ("loginip", "varchar", YES, "登录IP")
     * @ApiReturnParams   ("createtime", "int", YES, "创建时间")
     * @ApiReturnParams   ("updatetime", "int", YES, "更新时间")
     * @ApiReturnParams   ("token", "varchar", NO, "Session标识")
     * @ApiReturnParams   ("status", "varchar", NO, "状态")
     * @ApiReturn   ({
         'code':'1',
         'msg':'返回成功'
        })
     * */
    public function info()
    {
        $id = request()->param('id');
        $result = Db::name('admin')->where('id', $id)->find();
        
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
     * @ApiRoute    (/api/Admin/_list)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="page", type="int", required=true, description="第几页")
     * @ApiParams   (name="limit", type="int", required=true, description="显示条数")
     * @param   int id - 主键id true
     * @param   varchar username - 用户名 NO
     * @param   varchar nickname - 昵称 NO
     * @param   varchar password - 密码 NO
     * @param   varchar salt - 密码盐 NO
     * @param   varchar avatar - 头像 NO
     * @param   varchar email - 电子邮箱 NO
     * @param   tinyint loginfailure - 失败次数 NO
     * @param   int logintime - 登录时间 YES
     * @param   varchar loginip - 登录IP YES
     * @param   int createtime - 创建时间 YES
     * @param   int updatetime - 更新时间 YES
     * @param   varchar token - Session标识 NO
     * @param   varchar status - 状态 NO
     * @ApiReturnParams   ("id", "int", true, "主键id")
     * @ApiReturnParams   ("username", "varchar", NO, "用户名")
     * @ApiReturnParams   ("nickname", "varchar", NO, "昵称")
     * @ApiReturnParams   ("password", "varchar", NO, "密码")
     * @ApiReturnParams   ("salt", "varchar", NO, "密码盐")
     * @ApiReturnParams   ("avatar", "varchar", NO, "头像")
     * @ApiReturnParams   ("email", "varchar", NO, "电子邮箱")
     * @ApiReturnParams   ("loginfailure", "tinyint", NO, "失败次数")
     * @ApiReturnParams   ("logintime", "int", YES, "登录时间")
     * @ApiReturnParams   ("loginip", "varchar", YES, "登录IP")
     * @ApiReturnParams   ("createtime", "int", YES, "创建时间")
     * @ApiReturnParams   ("updatetime", "int", YES, "更新时间")
     * @ApiReturnParams   ("token", "varchar", NO, "Session标识")
     * @ApiReturnParams   ("status", "varchar", NO, "状态")
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
        $username=request()->param("username");
        $nickname=request()->param("nickname");
        $password=request()->param("password");
        $salt=request()->param("salt");
        $avatar=request()->param("avatar");
        $email=request()->param("email");
        $loginfailure=request()->param("loginfailure");
        $logintime=request()->param("logintime");
        $loginip=request()->param("loginip");
        $createtime=request()->param("createtime");
        $updatetime=request()->param("updatetime");
        $token=request()->param("token");
        $status=request()->param("status");
        if ($username)$where["username"] = ['like', '%' .$username. '%'];
        if ($nickname)$where["nickname"] = ['like', '%' .$nickname. '%'];
        if ($password)$where["password"] = ['like', '%' .$password. '%'];
        if ($salt)$where["salt"] = ['like', '%' .$salt. '%'];
        if ($avatar)$where["avatar"] = ['like', '%' .$avatar. '%'];
        if ($email)$where["email"] = ['like', '%' .$email. '%'];
        if ($loginfailure)$where["loginfailure"] = ['like', '%' .$loginfailure. '%'];
        if (request()->param("startlogintime") && request()->param("endlogintime"))$where["logintime"] = [['>=', request()->param("startlogintime")], ['<=', request()->param("endlogintime")], 'and'];
        if ($loginip)$where["loginip"] = ['like', '%' .$loginip. '%'];
        if (request()->param("startcreatetime") && request()->param("endcreatetime"))$where["createtime"] = [['>=', request()->param("startcreatetime")], ['<=', request()->param("endcreatetime")], 'and'];
        if (request()->param("startupdatetime") && request()->param("endupdatetime"))$where["updatetime"] = [['>=', request()->param("startupdatetime")], ['<=', request()->param("endupdatetime")], 'and'];
        if ($token)$where["token"] = ['like', '%' .$token. '%'];
        if ($status)$where["status"] = ['like', '%' .$status. '%'];

        $result = Db::name('admin')->where($where)->page($page,$limit)->select()->toArray();
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
     * @ApiRoute    (/api/Admin/del/id/{id})
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
        $result = Db::name('admin')->where('id', $id)->delete();
        if ($result)
            $this->success('删除成功');
        else
            $this->error('删除失败');
    }

}