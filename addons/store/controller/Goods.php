<?php
namespace addons\store\controller;

use app\common\controller\AddonBase;
use think\facade\Request;
use addons\store\model\Goods as GoodsM;
use think\facade\Session;

class Goods extends AddonBase
{
    public function index()
    {
        return \app\common\http\Json::success(200, '成功');
    }
    public function list()
    {
        if (!$this->request->isAjax()) {
            return $this->fetch();
        } else {
            return $this->getList();
        }
    }

    public function getList()
    {
        $param = Request::param();
        if (empty($param['page'])) {
            $param['page'] = 1;
        }

        $admin_id = Session::get('admin.id');
        $condition =array();
        if($admin_id == 1){//判断是否超级管理员
            //读取全部
        }else{
            $condition['admin_id'] =$admin_id;
        }


        $list = GoodsM::where($condition)->page($param['page'])->limit($param['limit'])->select();
        return \app\common\http\Json::success('成功', $list, GoodsM::count());
    }

    public function delete()
    {
        $param = Request::param();
        if (!empty($param['id'])) {
            \addons\store\model\Goods::find($param['id'])->delete();
            \addons\store\model\GoodsSpecType::where('goods_id', $param['id'])->delete();
            \addons\store\model\GoodsSpecBase::where('goods_id', $param['id'])->delete();
            \addons\store\model\GoodsSpecValue::where('goods_id', $param['id'])->delete();
            \addons\store\model\GoodsCategoryJoin::where('goods_id', $param['id'])->delete();
            \addons\store\model\GoodsPhoto::where('goods_id', $param['id'])->delete();
            return \app\common\http\Json::success('删除成功');
        }
        return \app\common\http\Json::error();
    }
}
