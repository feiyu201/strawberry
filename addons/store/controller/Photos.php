<?php

namespace addons\store\controller;

use app\common\controller\AddonBase;
use think\facade\Request;
use addons\store\model\GoodsPhoto;

class Photos extends AddonBase
{
    public function index()
    {
        return \app\common\http\Json::success(200, '成功');
    }

    public function upload()
    {
        return $this->fetch();
    }

    public function list(){
        $param = Request::param();
        if (empty($param['page'])) {
            $param['page'] = 1;
        }
        if (empty($param['goods_id'])){
            $param['goods_id'] = 0;
        }
        $list = GoodsPhoto::where('goods_id', $param['goods_id'])->page($param['page'])->limit(18)->select();
        $this->assign('list', $list);
        $this->assign('count',GoodsPhoto::where('goods_id', $param['goods_id'])->count());
        return $this->fetch();
    }

    public function detail()
    {
        return $this->fetch();
    }

}