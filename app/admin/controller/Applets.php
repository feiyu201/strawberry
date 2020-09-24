<?php
//  *+-----------------------------------------------------------------------
//  *                      .::::.
//  *                    .::::::::.            | Author: 丶长情
//  *                    :::::::::::           | Email: zeng1144318071@gmail.com
//  *                 ..:::::::::::'           | Datetime: 2020/09/24
//  *             '::::::::::::'               | Remarks: 小程序管理
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
use app\admin\validate\Applets as adminValidate;
use think\exception\ValidateException;
class Applets extends AdminBase
{
    public function index(){
        return View::fetch();
    }
   public function getList(){
   		$page = $this->request->param('page',1,'intval');
   		$limit = $this->request->param('limit',10,'intval');
   		$count = Db::name('wxapp')->count();
   		$data = Db::name('wxapp')->page($page,12)->select()->each(function($item,$k){
   			return $item;
   		});
   		
   		return json([
				'code'=> 0,
				'count'=> 20,
   				'data'=>$data,
   				'msg'=>'查询用户成功'
   		]);
   }
   public function add(){
	   	if($this->request->isPost()){
	   		$data = $this->request->post();
	   		//var_dump($data);exit();
	   		try {
	   			validate(adminValidate::class)->check($data);
	   		} catch (ValidateException $e) {
	   			// 验证失败 输出错误信息
	   			$this->error($e->getError());
	   		}
	   		$data['status'] = (isset($data['status'])&&$data['status']==1)?'normal':'stop';
	   		if(Db::name('wxapp')->insert($data)){
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
	   		//var_dump($data);exit();
	   		try {
	   			validate(adminValidate::class) ->scene('edit')->check($data);
	   		} catch (ValidateException $e) {
	   			// 验证失败 输出错误信息
	   			$this->error($e->getError());
	   		}
	   		$data['status'] = (isset($data['status'])&&$data['status']==1)?'normal':'stop';
	   		if(Db::name('wxapp')->where('id',$data['id'])->update($data)){
	   			$this->success("编辑成功");
	   		}else{
	   			$this->error("编辑失败");
	   		}
	   	}
	   	$id = $this->request->param('id');
	   	if(!$id){
	   		$this->success("参数错误");
	   	}
	   	$wxappinfo = Db::name('wxapp')->where('id',$id)->find();
   		if(!$wxappinfo){
	   		$this->success("参数错误");
	   	}
	   	View::assign('wxappinfo',$wxappinfo);
        return View::fetch();
   }
   public function delete(){
   		$idsStr = $id = $this->request->param('idsStr');
   		if(!$idsStr){
   			$this->success("参数错误");
   		}
   		if(Db::name('wxapp')->where('id','in',$idsStr)->delete()){
   			$this->success("删除成功");
   		}else{
   			$this->error("删除失败");
   		}
   }
   public function setNormal(){
   	$idsStr = $id = $this->request->param('idsStr');
   	if(!$idsStr){
   		$this->success("参数错误");
   	}
   	if(Db::name('wxapp')->where('id','in',$idsStr)->update(['status'=>'normal'])){
   		$this->success("设置成功");
   	}else{
   		$this->error("设置失败");
   	}
   }
   public function setStop(){
   	$idsStr = $id = $this->request->param('idsStr');
   	if(!$idsStr){
   		$this->success("参数错误");
   	}
   	if(Db::name('wxapp')->where('id','in',$idsStr)->update(['status'=>'stop'])){
   		$this->success("设置成功");
   	}else{
   		$this->error("设置失败");
   	}
   }
}
