<?php
namespace addons\store\controller;

use app\common\controller\AddonBase;
use addons\store\model\GoodsCategory;
use think\facade\Request;

class Category extends AddonBase
{

    /**
     * 分类页面
     *
     * @return false|mixed|string
     * @throws \think\Exception
     */
    public function category(){
        return $this->fetch();
    }

    /**
     * 查询商品分类接口
     *
     * @return \think\response\Json
     */
    public function getCategory()
    {
        return \app\common\http\Json::success('成功',GoodsCategory::field('name as title')->field(true)->select());
    }

    /**
     * 商品分类选择框json
     *
     * @return \think\response\Json
     */
    public function categorySelect(){
        $pid = 0;
        function select($pid) {
            $i = GoodsCategory::where('pid',$pid)->field('name as title')->field(true)->select();
            $child = [];
            foreach ($i as $k1 => $v1) {
                $l = GoodsCategory::where('pid',$v1['id'])->field('name as title')->field(true)->select();
                $child[] = $v1;
                foreach ($l as $k2 => $v2) {
                    $child[] = $v2;
                }
            }
            return $child;
        }
        return \app\common\http\Json::success('成功',select($pid));
    }

    /**
     * 增加分类页面
     *
     * @return false|mixed|string
     * @throws \think\Exception
     */
    public function categoryAdd(){
        $param = Request::param();
        $data = null;
        if (!empty($param['id'])) {
            $title = [];
            $data = GoodsCategory::find($param['id']);
            $title[] = $data['name'];
            $data['title'] = $data['name'];
            if ($data['pid'] != 0) {
                $title[] = GoodsCategory::where('id',$data['pid'])->value('name');
                $id_2 = GoodsCategory::where('id',$data['pid'])->value('pid');
                if ( $id_2 != 0) {
                    $title[] = GoodsCategory::where('id',$id_2)->value('name');
                }
            }
            $title = array_reverse($title);
            $data['title'] = implode(' | ',$title);
        }
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * 修改分类页面
     *
     * @param int $id
     * @return false|mixed|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function categoryUpc(){
        $param = Request::param();
        $data = null;
        if (!empty($param['id'])) {
            $title = [];
            $data = GoodsCategory::find($param['id']);
            $title[] = $data['name'];
            $data['title'] = $data['name'];
            if ($data['pid'] != 0) {
                $title[] = GoodsCategory::where('id',$data['pid'])->value('name');
                $id_2 = GoodsCategory::where('id',$data['pid'])->value('pid');
                if ( $id_2 != 0) {
                    $title[] = GoodsCategory::where('id',$id_2)->value('name');
                }
            }
            $title = array_reverse($title);
            $data['title'] = implode(' | ',$title);
        }
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function update(){
        $param = Request::param();
        if (strtoupper(Request::method()) == 'POST') {
            $update = Request::only(['icon','name','vice_name','describe','bg_color','big_images','is_home_recommended','sort','is_enable','seo_title','seo_keywords','seo_desc','id']);
            GoodsCategory::update($update);
            return \app\common\http\Json::success('更新成功');

        }
        return \app\common\http\Json::error();
    }

    public function add(){
        $param = Request::param();
        if (strtoupper(Request::method()) == 'POST') {
            $update = Request::only(['icon','name','vice_name','describe','bg_color','big_images','is_home_recommended','sort','is_enable','seo_title','seo_keywords','seo_desc','pid']);
            GoodsCategory::create($update);
            return \app\common\http\Json::success('创建成功');
        }
        return \app\common\http\Json::error();
    }
}