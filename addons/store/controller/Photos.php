<?php

namespace addons\store\controller;

use addons\store\model\BrandCategoryJoin;
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
        return $this->fetch('file');
    }

    public function list()
    {
        if (!$this->request->isAjax()) {
            $param = Request::param();
            if (empty($param['page'])) {
                $param['page'] = 1;
            }
            if (empty($param['goods_id'])) {
                $param['goods_id'] = 0;
            }
//        $list = GoodsPhoto::where('goods_id', $param['goods_id'])->page($param['page'])->limit(18)->select();
//        $this->assign('list', $list);
//        $this->assign('count',GoodsPhoto::where('goods_id', $param['goods_id'])->count());
            return $this->fetch('file');
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
        if (empty($param['goods_id'])) {
            $param['goods_id'] = 0;
        }
        if (empty($param['limit'])) {
            $param['limit'] = 10;
        }
        $list = GoodsPhoto::where('goods_id', $param['goods_id'])->page($param['page'])->limit($param['limit'])->select()->each(function ($item) {
            $item['thumb'] = $item['images_thumb'];
            $item['type'] = 'image';
            $item['path'] = $item['images'];
            $item['name'] = $item['md5'];
            return $item;
        });
        return \app\common\http\Json::success('成功', $list, GoodsPhoto::where('goods_id', $param['goods_id'])->count());
    }

    public function detail()
    {
        return $this->fetch();
    }

    public function delete()
    {
        $param = Request::param();
        if (!empty($param['id'])) {
            GoodsPhoto::where('id', $param['id'])->delete();
            return \app\common\http\Json::success('删除成功');
        }
        return \app\common\http\Json::error('删除失败');
    }
}
