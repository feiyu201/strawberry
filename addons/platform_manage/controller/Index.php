<?php
namespace addons\platform_manage\controller;

use addons\bm\business\BM;
use app\common\controller\AddonBase;
use think\facade\View;

class Index extends AddonBase {

    public function initialize()
    {
        parent::initialize();
        $this->model = new \addons\platform_manage\model\PlatformManage();

    }

    public function index() {
        if (!$this->request->isAjax()) {
            return $this->fetch();
        } else {
            return $this->getList();
        }
    }
    public function getList() {

        $page = $this->request->param('page', 1, 'intval');
        $limit = $this->request->param('limit', 10, 'intval');
        $count = $this->model->count();
        $data = $this->model->page($page, $limit)->select()->toArray();

        $data = array_map(function ($item) {
           $map = [
               'wx'  => '公众号',
               'miniprogram'  => '小程序'
           ];

           $item['type'] = $map[$item['type']];
            return $item;
        }, $data);

        return json([
            'code' => 0,
            'count' => $count,
            'data' => $data,
            'msg' => '请求成功',
        ]);
    }

}