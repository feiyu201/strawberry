<?php
namespace addons\store\controller;

use app\common\controller\AddonBase;
use addons\store\model\GoodsSpecType;
use think\facade\Request;

class Spec extends AddonBase
{
    public function index()
    {

        return \app\common\http\Json::success(200,'成功');
    }
    public function getSpec()
    {
        $param = Request::param();
        if (!empty($param['id'])){
            $specType = GoodsSpecType::where('goods_id',$param['id'])->select();
            $specTypeArr = [];
            foreach ($specType as $val) {
                $specTypeContent = json_decode($val['value']);
                $specTypeArr[] = [
                    'title'=> $val['name'],
                    'content'=> $specTypeContent
                ];
            }
            return \app\common\http\Json::success('查询成功',$specTypeArr);
        }

        return $this->fetch();
    }
    public function detail()
    {
        return $this->fetch();
    }

}