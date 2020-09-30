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
 * 管理员表接口
 */

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
     * @ApiTitle    (添加)
     * @ApiSummary  (描述信息)
     * @ApiMethod   (POST/GET)
     * @ApiRoute    (/api/Admin/add)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="username", type="varchar", required=NO, description="用户名")
     * @ApiParams   (name="nickname", type="varchar", required=NO, description="昵称")
     * @ApiParams   (name="password", type="varchar", required=NO, description="密码")
     * @ApiParams   (name="salt", type="varchar", required=NO, description="密码盐")
     * @ApiParams   (name="avatar", type="varchar", required=NO, description="头像")
     * @ApiParams   (name="email", type="varchar", required=NO, description="电子邮箱")
     * @ApiParams   (name="loginfailure", type="tinyint", required=NO, description="失败次数")
     * @ApiParams   (name="logintime", type="int", required=YES, description="登录时间")
     * @ApiParams   (name="loginip", type="varchar", required=YES, description="登录IP")
     * @ApiParams   (name="createtime", type="int", required=YES, description="创建时间")
     * @ApiParams   (name="updatetime", type="int", required=YES, description="更新时间")
     * @ApiParams   (name="token", type="varchar", required=NO, description="Session标识")
     * @ApiParams   (name="status", type="varchar", required=NO, description="状态")
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
     * @ApiTitle    (编辑)
     * @ApiSummary  (描述信息)
     * @ApiMethod   (POST/GET)
     * @ApiRoute    (/api/Admin/edit)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="id", type="int", required=true, description="主键id")
     * @ApiParams   (name="username", type="varchar", required=NO, description="用户名")
     * @ApiParams   (name="nickname", type="varchar", required=NO, description="昵称")
     * @ApiParams   (name="password", type="varchar", required=NO, description="密码")
     * @ApiParams   (name="salt", type="varchar", required=NO, description="密码盐")
     * @ApiParams   (name="avatar", type="varchar", required=NO, description="头像")
     * @ApiParams   (name="email", type="varchar", required=NO, description="电子邮箱")
     * @ApiParams   (name="loginfailure", type="tinyint", required=NO, description="失败次数")
     * @ApiParams   (name="logintime", type="int", required=YES, description="登录时间")
     * @ApiParams   (name="loginip", type="varchar", required=YES, description="登录IP")
     * @ApiParams   (name="createtime", type="int", required=YES, description="创建时间")
     * @ApiParams   (name="updatetime", type="int", required=YES, description="更新时间")
     * @ApiParams   (name="token", type="varchar", required=NO, description="Session标识")
     * @ApiParams   (name="status", type="varchar", required=NO, description="状态")
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
     * @ApiTitle    (查询单条)
     * @ApiSummary  (描述信息)
     * @ApiMethod   (POST/GET)
     * @ApiRoute    (/api/Admin/info/id/{id})
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="id", type="int", required=true, description="主键id")
     * @ApiReturnParams   (name="id", type="int", required=true, description="主键id")
     * @ApiReturnParams   (name="username", type="varchar", required=NO, description="用户名")
     * @ApiReturnParams   (name="nickname", type="varchar", required=NO, description="昵称")
     * @ApiReturnParams   (name="password", type="varchar", required=NO, description="密码")
     * @ApiReturnParams   (name="salt", type="varchar", required=NO, description="密码盐")
     * @ApiReturnParams   (name="avatar", type="varchar", required=NO, description="头像")
     * @ApiReturnParams   (name="email", type="varchar", required=NO, description="电子邮箱")
     * @ApiReturnParams   (name="loginfailure", type="tinyint", required=NO, description="失败次数")
     * @ApiReturnParams   (name="logintime", type="int", required=YES, description="登录时间")
     * @ApiReturnParams   (name="loginip", type="varchar", required=YES, description="登录IP")
     * @ApiReturnParams   (name="createtime", type="int", required=YES, description="创建时间")
     * @ApiReturnParams   (name="updatetime", type="int", required=YES, description="更新时间")
     * @ApiReturnParams   (name="token", type="varchar", required=NO, description="Session标识")
     * @ApiReturnParams   (name="status", type="varchar", required=NO, description="状态")
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
     * @ApiTitle    (查询列表)
     * @ApiSummary  (描述信息)
     * @ApiMethod   (POST/GET)
     * @ApiRoute    (/api/Admin/_list)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="page", type="int", required=true, description="第几页")
     * @ApiParams   (name="limit", type="int", required=true, description="显示条数")
     * @ApiParams   (name="id", type="int", required=true, description="主键id")
     * @ApiParams   (name="username", type="varchar", required=NO, description="用户名")
     * @ApiParams   (name="nickname", type="varchar", required=NO, description="昵称")
     * @ApiParams   (name="password", type="varchar", required=NO, description="密码")
     * @ApiParams   (name="salt", type="varchar", required=NO, description="密码盐")
     * @ApiParams   (name="avatar", type="varchar", required=NO, description="头像")
     * @ApiParams   (name="email", type="varchar", required=NO, description="电子邮箱")
     * @ApiParams   (name="loginfailure", type="tinyint", required=NO, description="失败次数")
     * @ApiParams   (name="logintime", type="int", required=YES, description="登录时间")
     * @ApiParams   (name="loginip", type="varchar", required=YES, description="登录IP")
     * @ApiParams   (name="createtime", type="int", required=YES, description="创建时间")
     * @ApiParams   (name="updatetime", type="int", required=YES, description="更新时间")
     * @ApiParams   (name="token", type="varchar", required=NO, description="Session标识")
     * @ApiParams   (name="status", type="varchar", required=NO, description="状态")
     * @ApiReturnParams   (name="id", type="int", required=true, description="主键id")
     * @ApiReturnParams   (name="username", type="varchar", required=NO, description="用户名")
     * @ApiReturnParams   (name="nickname", type="varchar", required=NO, description="昵称")
     * @ApiReturnParams   (name="password", type="varchar", required=NO, description="密码")
     * @ApiReturnParams   (name="salt", type="varchar", required=NO, description="密码盐")
     * @ApiReturnParams   (name="avatar", type="varchar", required=NO, description="头像")
     * @ApiReturnParams   (name="email", type="varchar", required=NO, description="电子邮箱")
     * @ApiReturnParams   (name="loginfailure", type="tinyint", required=NO, description="失败次数")
     * @ApiReturnParams   (name="logintime", type="int", required=YES, description="登录时间")
     * @ApiReturnParams   (name="loginip", type="varchar", required=YES, description="登录IP")
     * @ApiReturnParams   (name="createtime", type="int", required=YES, description="创建时间")
     * @ApiReturnParams   (name="updatetime", type="int", required=YES, description="更新时间")
     * @ApiReturnParams   (name="token", type="varchar", required=NO, description="Session标识")
     * @ApiReturnParams   (name="status", type="varchar", required=NO, description="状态")
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
     * @ApiTitle    (删除)
     * @ApiSummary  (描述信息)
     * @ApiMethod   (POST/GET)
     * @ApiRoute    (/api/Admin/del/id/{id})
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @ApiParams   (name="id", type="int", required=true, description="主键id")
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