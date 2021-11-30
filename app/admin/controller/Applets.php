<?php
//  *+-----------------------------------------------------------------------
//  *                      .::::.
//  *                    .::::::::.            | Author: 丶长情
//  *                    :::::::::::           | Email: zeng1144318071@gmail.com
//  *                 ..:::::::::::'           | Datetime: 2020/09/24
//  *             '::::::::::::'               | Remarks: 小程序管理
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
namespace app\admin\controller;

use think\facade\Event;
use think\facade\View;
use think\facade\Db;
use app\admin\validate\Applets as adminValidate;
use think\exception\ValidateException;
use app\admin\facade\ThinkAddons;
use app\common\model\Wxapp;
use think\facade\Session;
use app\common\library\Portal;

class Applets extends AdminBase
{
    public function index()
    {
        if (!$this->request->isAjax()) {
            return View::fetch();
        } else {
            return $this->getList();
        }
    }

    public function getList()
    {
        $page  = $this->request->param('page', 1, 'intval');
        $limit = $this->request->param('limit', 10, 'intval');
        $count = Db::name('wxapp')->count();
        $data  = Db::name('wxapp')->page($page, $limit)->select()->each(function ($item, $k) {
            return $item;
        });

        return json([
            'code'  => 0,
            'count' => 20,
            'data'  => $data,
            'msg'   => '查询用户成功'
        ]);
    }

    public function add()
    {
        $pluginList        = ThinkAddons::localAddons();
        $installPluginList = [];
        foreach ($pluginList as $key => $value) {
            if (isset($value['install']) && $value['install'] == 1) {
                $temp                = [
                    'name'  => $value['name'],
                    'title' => $value['title']
                ];
                $installPluginList[] = $temp;
            }
        }

        if ($this->request->isPost()) {
            $data = $this->request->post();
            //var_dump($data);exit();
            try {
                validate(adminValidate::class)->check($data);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                $this->error($e->getError());
            }
            $data['status'] = (isset($data['status']) && $data['status'] == 1) ? 'normal' : 'stop';
            if ($result = Wxapp::create($data)) {
                Event::trigger('platform_action',[
                    'type'              =>  'miniprogram',
                    'third_id'          =>  $result->getData('id'),
                    'action'            =>  'add',
                    'name'              =>  $data['name']
                ]);
                $this->success("添加成功");
            } else {
                $this->error("添加失败");
            }
        }
        View::assign('installPluginList', $installPluginList);
        return View::fetch();
    }

    public function edit()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            //var_dump($data);exit();
            try {
                validate(adminValidate::class)->scene('edit')->check($data);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                $this->error($e->getError());
            }
            $data['status'] = (isset($data['status']) && $data['status'] == 1) ? 'normal' : 'stop';
            if (Db::name('wxapp')->where('id', $data['id'])->update($data)) {
                Event::trigger('platform_action',[
                    'type'              =>  'miniprogram',
                    'third_id'          =>  $data['id'],
                    'action'            =>  'edit',
                    'name'              =>  $data['name']
                ]);
                $this->success("编辑成功");
            } else {
                $this->error("编辑失败");
            }
        }
        $id = $this->request->param('id');
        if (!$id) {
            $this->success("参数错误");
        }
        $wxappinfo = Db::name('wxapp')->where('id', $id)->find();
        if (!$wxappinfo) {
            $this->success("参数错误");
        }
        $pluginList        = ThinkAddons::localAddons();
        $installPluginList = [];
        foreach ($pluginList as $key => $value) {
            if (isset($value['install']) && $value['install'] == 1) {
                $temp                = [
                    'name'  => $value['name'],
                    'title' => $value['title']
                ];
                $installPluginList[] = $temp;
            }
        }
        View::assign('installPluginList', $installPluginList);
        View::assign('wxappinfo', $wxappinfo);
        return View::fetch();
    }

    public function delete()
    {
        $idsStr = $id = $this->request->param('idsStr');
        if (!$idsStr) {
            $this->success("参数错误");
        }
        if (Db::name('wxapp')->where('id', 'in', $idsStr)->delete()) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

    public function setNormal()
    {
        $idsStr = $id = $this->request->param('idsStr');
        if (!$idsStr) {
            $this->success("参数错误");
        }
        if (Db::name('wxapp')->where('id', 'in', $idsStr)->update(['status' => 'normal'])) {
            $this->success("设置成功");
        } else {
            $this->error("设置失败");
        }
    }

    public function setStop()
    {
        $idsStr = $id = $this->request->param('idsStr');
        if (!$idsStr) {
            $this->success("参数错误");
        }
        if (Db::name('wxapp')->where('id', 'in', $idsStr)->update(['status' => 'stop'])) {
            $this->success("设置成功");
        } else {
            $this->error("设置失败");
        }
    }

    /**
     * 小程序应用入口
     */
    public function welcome()
    {
        $data = $this->request->get();
        if (empty($data['id'])) {
            $this->error("小程序id不能为空");
        }
        $wxappinfo = Db::name('wxapp')->where('id', $data['id'])->find();
        new Portal();
        global $_W;
        $_W['user']=array(
            'uid'=>Session::get('admin.id'),
            'name'=>Session::get('admin.username'),
            'username'=>Session::get('admin.username'),
        );
        $_W['account']=array(
            'acid'=>$wxappinfo['id'],
            'name'=>$wxappinfo['name'],
        );
        $_W['current_module']=array(
            'name'=>$wxappinfo['addons']
        );

        if (empty($wxappinfo['addons'])) {
            $this->error("请绑定应用插件！");
        }
        if (!ThinkAddons::welcome($wxappinfo['addons'])) {
            $this->error("应用插件无入口welcome方法！");
        };
    }
}
