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

class Test extends AdminBase
{

    public function index(){
        return View::fetch();
    }

   public function getList(){
   		$page = $this->request->param('page',1,'intval');
   		$limit = $this->request->param('limit',10,'intval');
   		$count = Db::name('test')->count();
   		$data = Db::name('test')->page($page,$limit)->select()->each(function($item,$k){
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
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $arr = [];
                    foreach ($value as $elt => $item) {
                        array_push($arr, $elt);
                    }
                    $data[$key] = implode(',', $arr);
                }
                $s = explode('_',$key);
                if (end($s) === 'time'){
                    $data[$key] = strtotime($data[$key]);
                }
            }
	   		if(Db::name('test')->insert($data)){
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
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $arr = [];
                    foreach ($value as $elt => $item) {
                        array_push($arr, $elt);
                    }
                    $data[$key] = implode(',', $arr);
                }
                $s = explode('_',$key);
                if (end($s) === 'time'){
                    $data[$key] = strtotime($data[$key]);
                }
            }
            if(Db::name('test')->where('id',$data['id'])->update($data)){
	   			$this->success("编辑成功");
	   		}else{
	   			$this->error("编辑失败");
	   		}
	   	}
	   	$id = $this->request->param('id');
	   	if(!$id){
	   		$this->success("参数错误");
	   	}
	   	$test = Db::name('test')->where('id',$id)->find();
   		if(!$test){
	   		$this->success("参数错误");
	   	}
	   	View::assign('test',$test);
        return View::fetch();
   }

   public function delete(){
   		$idsStr = $id = $this->request->param('idsStr');
   		if(!$idsStr){
   			$this->success("参数错误");
   		}
   		if(Db::name('test')->where('id','in',$idsStr)->delete()){
   			$this->success("删除成功");
   		}else{
   			$this->error("删除失败");
   		}
   }

}
