<?php
namespace addons\store\controller;

use app\common\controller\AddonBase;
use addons\store\model\GoodsCategory;
use think\facade\Request;
use think\facade\Session;

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
            $admin_id = Session::get('admin.id');
            $con1 =array(
                'pid'=>$pid,
                'admin_id'=>$admin_id
            );
            $i = GoodsCategory::where($con1)->field('name as title')->field(true)->select();
            $child = [];
            foreach ($i as $k1 => $v1) {
                $con2 =array(
                    'pid'=>$v1['id'],
                    'admin_id'=>$admin_id
                );
                $l = GoodsCategory::where($con2)->field('name as title')->field(true)->select();
                $child[] = $v1;
                foreach ($l as $k2 => $v2) {
                    $child[] = $v2;
                }
            }
            return $child;
        }
        $topMenu  = [[
            'pid'=>0,
            'id'=> null,
            'title'=> '顶级分类',
            'name'=> '顶级分类'
        ]];
        return \app\common\http\Json::success('成功',array_merge($topMenu,select($pid)));
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
            if($update['pid'] == 'null') {
                $update['pid'] = 0;
            }
            $admin_id = Session::get('admin.id');
            $update['admin_id'] = $admin_id;
            GoodsCategory::create($update);
            return \app\common\http\Json::success('创建成功');
        }
        return \app\common\http\Json::error();
    }
    
    public function delete(){
        $param = Request::param();
        if (!empty($param['id'])) {
            $first = GoodsCategory::find($param['id']);
            \addons\store\model\GoodsCategoryJoin::where('category_id',$first['id'])->delete();
            if ($first != null && $first['pid'] != 0) {
                $next = GoodsCategory::where('id',$first['pid'])->find();
                if ($next != null) {
                    \addons\store\model\GoodsCategoryJoin::where('category_id',$next['id'])->delete();
                    $last = GoodsCategory::where('id',$next['pid'])->find();
                    $next->delete();
                    if ($last != null) {
                        \addons\store\model\GoodsCategoryJoin::where('category_id',$last['id'])->delete();
                        $last->delete();
                    }
                }
            }
            $first->delete();
            return \app\common\http\Json::success('删除成功');
        }
        return \app\common\http\Json::error();
    }
}