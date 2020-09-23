<?php
namespace app\admin\controller;
use think\facade\View;
use think\facade\Db;
use app\admin\validate\Admin as adminValidate;
use think\exception\ValidateException;
class Admin extends AdminBase
{
    public function index(){
    	
        return View::fetch();
    }
   public function getList(){
   		$page = $this->request->param('page',1,'intval');
   		$limit = $this->request->param('limit',10,'intval');
   		$count = Db::name('admin')->count();
   		$data = Db::name('admin')->page($page,12)->select()->each(function($item,$k){
   			$item['createtime_text'] = date('Y-m-d H:i',$item['createtime']);
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
	   		unset($data['file']);
	   		$data['status'] = (isset($data['status'])&&$data['status']==1)?'normal':'stop';
	   		$data['avatar'] = $data['avatar']?("/storage/".$data['avatar']):'';
	   		$data['createtime'] = time();
	   		$data['salt'] = GetRandStr(6);
	   		$data['password'] = md5(md5($data['password']).$data['salt']);
	   		if(Db::name('admin')->insert($data)){
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
	   		unset($data['file']);
	   		$data['status'] = (isset($data['status'])&&$data['status']==1)?'normal':'stop';
	   		
	   		$data['updatetime'] = time();
	   		if($data['avatar']){
	   			$data['avatar'] = ("/storage/".$data['avatar']);
	   		}else{
	   			unset($data['avatar']);
	   		}
	   		if($data['password']){
	   			if(strlen($data['password'])<6){
	   				$this->error("密码至少6位");
	   			}
	   			$data['salt'] = GetRandStr(6);
	   			$data['password'] = md5(md5($data['password']).$data['salt']);
	   		}else{
	   			unset($data['password']);
	   		}
	   		if(Db::name('admin')->where('id',$data['id'])->update($data)){
	   			$this->success("编辑成功");
	   		}else{
	   			$this->error("编辑失败");
	   		}
	   	}
	   	$id = $this->request->param('id');
	   	if(!$id){
	   		$this->success("参数错误");
	   	}
	   	$admininfo = Db::name('admin')->where('id',$id)->find();
   		if(!$admininfo){
	   		$this->success("参数错误");
	   	}
	   	View::assign('admininfo',$admininfo);
        return View::fetch();
   }
   public function delete(){
   		$idsStr = $id = $this->request->param('idsStr');
   		if(!$idsStr){
   			$this->success("参数错误");
   		}
   		if(Db::name('admin')->where('id','in',$idsStr)->delete()){
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
   	if(Db::name('admin')->where('id','in',$idsStr)->update(['status'=>'normal'])){
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
   	if(Db::name('admin')->where('id','in',$idsStr)->update(['status'=>'stop'])){
   		$this->success("设置成功");
   	}else{
   		$this->error("设置失败");
   	}
   }
}
