<?php
namespace addons\store\controller;

use app\common\controller\AddonBase;
use think\facade\Request;
use \addons\store\model\Goods as GoodsM;
require_once(app()->getRootPath().'/addons/store/common.php');

class Increase extends AddonBase
{
    public function index()
    {
    	return \app\common\http\Json::success(200,'成功');
    }
    public function addEdit()
    {
        $param = Request::param();
        $data = null;
        $category = null;
        $content_web = '';
        $data['images'] = [];
        $spec = null;
        $specTble = null;
        $specImg = [];
        if (!empty($param['id'])) {
            $data = \addons\store\model\Goods::find($param['id']);
            $content_web = $data['content_web'];
            if (!empty($data['images'])){
                $data['images'] = explode(',',$data['images']);
            } else{

                $data['images'] = [];
            }


            // 查询商品分类
            $category = getCategoryNameId($param['id']);
            // 查询规格并转化成JS可以解析的格式
            $spec = getSpec($param['id']);
            // 查询规格价格表 并转化成JS可以解析的格式
            $specTble = getSpecTable($param['id']);
            // 查询规格图片数据
            $specImg = getSpecImages($param['id']);
        }
        // 商品基础数据
        $this->assign('goods',$data);
        // 商品分类数据
        $this->assign('data',$category);
        // 商品品牌数据
        $brand = \addons\store\model\Brand::select();
        $this->assign('brand',$brand);
        // 商品详情页数据
        $this->assign('content',$content_web);
        // 商品规格基础数据
        $this->assign('spec',$spec);
        // 商品规格价格数据
        $this->assign('specPrice',$specTble);
        // 商品规格图片数据
        $this->assign('specImg',$specImg);
    	 return $this->fetch();
    }

    public function upcAdd(){
        $param = Request::param();
        $data = null;
        if (!empty($param['id'])) {
            // 写入规格数值（带规格图片）
            if (!empty($param['specType'])){
                setSpecTypeSql($param['specType'],$param['id']);
            }

            // 写入规格价格表 基础表与规格值 本写入是先清空再写入
            if (!empty($param['pushItem'])) {
                setSpecValBaseSql($param['pushItem'],$param['id']);
            }


            $data = Request::only([
                'brand_id',
                'site_type',
                'title',
                'title_color',
                'simple_desc',
                'model',
                'place_origin',
                'inventory',
                'inventory_unit',
                'images',
                'orginal_price',
                'min_original_price',
                'max_original_price',
                'price',
                'min_price',
                'max_pric',
                'give_integral',
                'buy_min_number',
                'buy_max_number',
                'is_deduction_inventory',
                'is_shelves',
                'is_home_recommended',
                'content_web',
                'photo_count',
//                'sales_count',
//                'access_count',
                'video',
                'is_exist_many_spec',
                'spec_base',
                'fictitious_goods_value',
                'seo_title',
                'seo_keywords',
                'seo_desc',
                'place',
                'id'
            ]);
            if (!empty($param['images']) && count($param['images']) >0) {
                $data['images'] = join(',',$param['images']);
            }
            $data['place_origin'] = $param['districtId'];
            \addons\store\model\Goods::find($param['id'])->update($data);
            return \app\common\http\Json::success('更新成功');
        } else {
            $data = Request::only([
                'brand_id',
                'site_type',
                'title',
                'title_color',
                'simple_desc',
                'model',
                'place_origin',
                'inventory',
                'inventory_unit',
                'images',
                'orginal_price',
                'min_original_price',
                'max_original_price',
                'price',
                'min_price',
                'max_pric',
                'give_integral',
                'buy_min_number',
                'buy_max_number',
                'is_deduction_inventory',
                'is_shelves',
                'is_home_recommended',
                'content_web',
                'photo_count',
//                'sales_count',
//                'access_count',
                'video',
                'is_exist_many_spec',
                'spec_base',
                'fictitious_goods_value',
                'seo_title',
                'seo_keywords',
                'seo_desc',
                'place',
            ]);
            $data['place_origin'] = $param['districtId'];
            if (!empty($param['images']) && count($param['images']) >0) {
                $data['images'] = join(',',$param['images']);
            }
            $goods = \addons\store\model\Goods::create($data);
            // 写入规格数值（带规格图片）
            if (!empty($param['specType'])){
                setSpecTypeSql($param['specType'],$goods->id);
            }

            // 写入规格价格表 基础表与规格值 本写入是先清空再写入
            if (!empty($param['specType'])){
                setSpecValBaseSql($param['pushItem'],$goods->id);
            }


            return \app\common\http\Json::success('创建成功');
        }
    }

}