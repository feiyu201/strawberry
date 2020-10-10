<?php
namespace addons\store\controller;

use app\common\controller\AddonBase;
use addons\store\model\GoodsPhoto;
use think\facade\Request;

class Upload extends AddonBase
{
    /**
     * 上传小图标图片 最大200X200
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function uploadIcon(){
        $files = [Request::file('file')];
        try {
            $md5 = $files[0]->md5();
            $exist = GoodsPhoto::where('md5',$md5)->find();
            if (!empty($exist)) {
                return \app\common\http\Json::success('已上传，取历史图片',$exist['images']);
            }
            validate(['image'=>'filesize:10240|fileExt:jpg,png,gif|image:200,200,jpg,png,gif'])
                ->check($files);
            $savename = '/storage/'.\think\facade\Filesystem::disk('public')->putFile( 'icon',$files[0], 'md5');
            $newPhoto = new GoodsPhoto;
            $newPhoto->save([
                'goods_id'=>0,
                'md5'=>$md5,
                'images'=>$savename,
                'is_show'=>1,
            ]);
            return \app\common\http\Json::success('已上传，取历史图片',$savename);
        } catch (\think\exception\ValidateException $e) {
            return \app\common\http\Json::error($e->getMessage());
        }
    }


    /**
     * 上传商品分类大图片
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function uploadImages(){
        $files = [Request::file('file')];
        try {
            $md5 = $files[0]->md5();
            $exist = GoodsPhoto::where('md5',$md5)->find();
            if (!empty($exist)) {
                return \app\common\http\Json::success('已上传，取历史图片',$exist['images']);
            }
            validate(['image'=>'filesize:10240|fileExt:jpg,png,gif|image:2000,2000,jpg,png,gif'])
                ->check($files);
            $savename = '/storage/'.\think\facade\Filesystem::disk('public')->putFile( 'categoryImages',$files[0], 'md5');
            $newPhoto = new GoodsPhoto;
            $newPhoto->save([
                'goods_id'=>0,
                'md5'=>$md5,
                'images'=>$savename,
                'is_show'=>1,
            ]);
            return \app\common\http\Json::success('已上传，取历史图片',$savename);
        } catch (\think\exception\ValidateException $e) {
            return \app\common\http\Json::error($e->getMessage());
        }
    }
}