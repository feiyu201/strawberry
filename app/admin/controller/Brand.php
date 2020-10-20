<?php
//  *+-----------------------------------------------------------------------
//  *                      .::::.
//  *                    .::::::::.            | Author: 丶长情
//  *                    :::::::::::           | Email: zeng1144318071@gmail.com
//  *                 ..:::::::::::'           | Datetime: 2020/09/24
//  *             '::::::::::::'               | Remarks: 11
//  *                .::::::::::
//  *           '::::::::::::::..
//  *                ..::::::::::::.
//  *              ``::::::::::::::::
//  *               ::::``:::::::::'        .:::.
//  *              ::::'   ':::::'       .::::::::.
//  *            .::::'      ::::     .:::::::'::::.
//  *           .:::'       :::::  .:::::::::' ':::::.
//  *          .::'        :::::.:::::::::'      ':::::.
//  *         .::'         ::::::::::::::'         ``::::.
//  *     ...:::           ::::::::::::'              ``::.
//  *   ```` ':.          ':::::::::'                  ::::..
//  *                      '.:::::'                    ':'````..
//  * +-----------------------------------------------------------------------
namespace app\admin\controller;

use think\facade\View;
use think\facade\Db;

class Brand extends AdminBase
{

    public function index(){
        return View::fetch();
    }

   public function getList(){
   		$page = $this->request->param('page',1,'intval');
   		$limit = $this->request->param('limit',10,'intval');
   		$count = Db::name('brand')->count();
   		$data = Db::name('brand')->page($page,$limit)->select()->each(function($item,$k){
   			return $item;
   		});
   		return json([
				'code'=> 0,
				'count'=> $count,
   				'data'=>$data,
   				'msg'=>'查询用户成功'
   		]);
   }

   public function add(){
	   	if($this->request->isPost()){
	   		$data = $this->request->post();
	   		if(Db::name('brand')->insert($data)){
	   			$this->success("添加成功");
	   		}else{
	   			$this->error("添加失败");
	   		}
	   	}
	   	return View::fetch();
   }

   public function edit(){
	   	if($this->request->isPost()){
	   		$data = $this->request->post();
	   		if(Db::name('brand')->where('id',$data['id'])->update($data)){
	   			$this->success("编辑成功");
	   		}else{
	   			$this->error("编辑失败");
	   		}
	   	}
	   	$id = $this->request->param('id');
	   	if(!$id){
	   		$this->success("参数错误");
	   	}
	   	$brand = Db::name('brand')->where('id',$id)->find();
   		if(!$brand){
	   		$this->success("参数错误");
	   	}
	   	View::assign('brand',$brand);
        return View::fetch();
   }

   public function delete(){
   		$idsStr = $id = $this->request->param('idsStr');
   		if(!$idsStr){
   			$this->success("参数错误");
   		}
   		if(Db::name('brand')->where('id','in',$idsStr)->delete()){
   			$this->success("删除成功");
   		}else{
   			$this->error("删除失败");
   		}
   }

}
