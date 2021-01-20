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
     * @title    添加
     * @author 一笑奈何
     * @desc  (描述信息)
     * @method   (POST/GET)
     * @ApiRoute    (/api/Admin/add)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   varchar username null 用户名 NO
     * @param   varchar nickname null 昵称 NO
     * @param   varchar password null 密码 NO
     * @param   varchar salt null 密码盐 NO
     * @param   varchar avatar null 头像 NO
     * @param   varchar email null 电子邮箱 NO
     * @param   tinyint loginfailure null 失败次数 NO
     * @param   int logintime null 登录时间 YES
     * @param   varchar loginip null 登录IP YES
     * @param   int createtime null 创建时间 YES
     * @param   int updatetime null 更新时间 YES
     * @param   varchar token null Session标识 NO
     * @param   varchar status null 状态 NO
     * @return   int code null 返回参数 200
     * @return   string message null 返回信息 successful
     * @return   array data null 返回数据 successful
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
     * @param   int id null 主键id Yes
     * @param   varchar username null 用户名 NO
     * @param   varchar nickname null 昵称 NO
     * @param   varchar password null 密码 NO
     * @param   varchar salt null 密码盐 NO
     * @param   varchar avatar null 头像 NO
     * @param   varchar email null 电子邮箱 NO
     * @param   tinyint loginfailure null 失败次数 NO
     * @param   int logintime null 登录时间 YES
     * @param   varchar loginip null 登录IP YES
     * @param   int createtime null 创建时间 YES
     * @param   int updatetime null 更新时间 YES
     * @param   varchar token null Session标识 NO
     * @param   varchar status null 状态 NO
     * @return   int code null 返回参数 200
     * @return   string message null 返回信息 successful
     * @return   array data null 返回数据 successful
     * */
    public function edit()
    {
        // $id = request()->param('id');
        $param = request()->param();
        $model = new \app\common\model\Admin();
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
     * @ApiRoute    (/api/Admin/info/id/{id})
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id null 主键id Yes
     * @return   int id null 主键id
     * @return   varchar username null 用户名 NO
     * @return   varchar nickname null 昵称 NO
     * @return   varchar password null 密码 NO
     * @return   varchar salt null 密码盐 NO
     * @return   varchar avatar null 头像 NO
     * @return   varchar email null 电子邮箱 NO
     * @return   tinyint loginfailure null 失败次数 NO
     * @return   int logintime null 登录时间 YES
     * @return   varchar loginip null 登录IP YES
     * @return   int createtime null 创建时间 YES
     * @return   int updatetime null 更新时间 YES
     * @return   varchar token null Session标识 NO
     * @return   varchar status null 状态 NO
     * @return   int code null 返回参数 200
     * @return   string message null 返回信息 successful
     * @return   array data null 返回数据 successful
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
     * @param   int page null 第几页 Yes
     * @param   int limit null 显示条数 Yes
     * @param   int id null 主键id Yes
     * @param   varchar username null 用户名 NO
     * @param   varchar nickname null 昵称 NO
     * @param   varchar password null 密码 NO
     * @param   varchar salt null 密码盐 NO
     * @param   varchar avatar null 头像 NO
     * @param   varchar email null 电子邮箱 NO
     * @param   tinyint loginfailure null 失败次数 NO
     * @param   int logintime null 登录时间 YES
     * @param   varchar loginip null 登录IP YES
     * @param   int createtime null 创建时间 YES
     * @param   int updatetime null 更新时间 YES
     * @param   varchar token null Session标识 NO
     * @param   varchar status null 状态 NO
     * @return   int id null 主键id
     * @return   varchar username null 用户名 NO
     * @return   varchar nickname null 昵称 NO
     * @return   varchar password null 密码 NO
     * @return   varchar salt null 密码盐 NO
     * @return   varchar avatar null 头像 NO
     * @return   varchar email null 电子邮箱 NO
     * @return   tinyint loginfailure null 失败次数 NO
     * @return   int logintime null 登录时间 YES
     * @return   varchar loginip null 登录IP YES
     * @return   int createtime null 创建时间 YES
     * @return   int updatetime null 更新时间 YES
     * @return   varchar token null Session标识 NO
     * @return   varchar status null 状态 NO
     * @return   int code null 返回参数 200
     * @return   string message null 返回信息 successful
     * @return   array data null 返回数据 successful
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
        if ($username)$where[] = ['username', 'like', '%' .$username. '%'];
        if ($nickname)$where[] = ['nickname', 'like', '%' .$nickname. '%'];
        if ($password)$where[] = ['password', 'like', '%' .$password. '%'];
        if ($salt)$where[] = ['salt', 'like', '%' .$salt. '%'];
        if ($avatar)$where[] = ['avatar', 'like', '%' .$avatar. '%'];
        if ($email)$where[] = ['email', 'like', '%' .$email. '%'];
        if ($loginfailure)$where[] = ['loginfailure', 'like', '%' .$loginfailure. '%'];
        if (request()->param("startlogintime") && request()->param("endlogintime"))$where["logintime"] = [['>=', request()->param("startlogintime")], ['<=', request()->param("endlogintime")], 'and'];
        if ($loginip)$where[] = ['loginip', 'like', '%' .$loginip. '%'];
        if (request()->param("startcreatetime") && request()->param("endcreatetime"))$where["createtime"] = [['>=', request()->param("startcreatetime")], ['<=', request()->param("endcreatetime")], 'and'];
        if (request()->param("startupdatetime") && request()->param("endupdatetime"))$where["updatetime"] = [['>=', request()->param("startupdatetime")], ['<=', request()->param("endupdatetime")], 'and'];
        if ($token)$where[] = ['token', 'like', '%' .$token. '%'];
        if ($status)$where[] = ['status', 'like', '%' .$status. '%'];

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
     * @param   int id null 主键id Yes
     * @return   int code null 返回参数 200
     * @return   string message null 返回信息 successful
     * @return   array data null 返回数据 successful
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