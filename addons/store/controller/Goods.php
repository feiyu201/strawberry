<?php
namespace addons\store\controller;

use app\common\controller\AddonBase;
use think\facade\Request;
use addons\store\model\Goods as GoodsM;

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
        
         $param = Request::param();
        if (empty($param['limit'])) {
            $param['limit'] = 10;
        }
        
        
        $list = GoodsM::page($param['page'])->order('id','desc')->limit($param['limit'])->select();
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
