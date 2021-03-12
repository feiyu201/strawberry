<?php
namespace addons\store\controller;

use app\common\controller\AddonBase;
use think\facade\Request;
use addons\store\model\Brand as BrandM;
use addons\store\model\BrandCategoryJoin;

class Brand extends AddonBase
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
        $list = BrandM::page($param['page'])->limit($param['limit'])->select();
        return \app\common\http\Json::success('成功', $list, BrandM::count());
    }

    public function addEdit()
    {
        $param = Request::param();
        $data = null;
        if (!empty($param['id'])) {
            $data = BrandM::find($param['id']);
            $data['icon'] = $data['logo'];
        }
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function upcAdd()
    {
        $param = Request::param();
        $data = null;
        if (!empty($param['id'])) {
            $icon = $param['icon'];
            $param = Request::only(['logo','name','website_url','is_enable','sort','seo_title','seo_keywords','seo_desc','id']);
            $param['logo'] = $icon;
            BrandM::update($param);
            return \app\common\http\Json::success('更新成功');
        } else {
            $brand = new BrandM;
            $icon = $param['icon'];
            $param = Request::only(['logo','name','website_url','is_enable','sort','seo_title','seo_keywords','seo_desc']);
            $param['logo'] = $icon;
            $brand->save($param);
            return \app\common\http\Json::success('创建成功');
        }
    }

    public function delete()
    {
        $param = Request::param();
        if (!empty($param['id'])) {
            BrandM::where('id', $param['id'])->delete();
            BrandCategoryJoin::where('brand_id', $param['id'])->delete();
            return \app\common\http\Json::success('删除成功');
        }
        return \app\common\http\Json::error('删除失败');
    }
}
