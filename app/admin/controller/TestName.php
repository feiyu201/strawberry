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

class TestName extends AdminBase
{

    public function initialize(){
		parent::initialize();
        $this->model = new \app\admin\model\TestName();
    }
    public function index(){
		if(!$this->request->isAjax()){
        	return View::fetch();
		}else{
			return $this->getList();
		}
    }

   public function getList(){
   		$page = $this->request->param('page',1,'intval');
   		$limit = $this->request->param('limit',10,'intval');
   		$count = $this->model->count();
   		$data = $this->model->with([])
		   ->where(function($query){
            $query->dateRange('create_time',$this->request->param('create_time',null))
->dateRange('create1time',$this->request->param('create1time',null))
->dateRange('update_time',$this->request->param('update_time',null))
->dateRange('create_at',$this->request->param('create_at',null));
            
                    $id = $this->request->param('id',null);
                    if($id){
                        $query->whereLike('id',"%{$id}%");
                    }
                    

                $select_test = $this->request->param('select_test',null);
                if($select_test){
                    $query->where('select_test',$select_test);
                }
                

                    $set_test = $this->request->param('set_test',null);
                    if($set_test){
                        $query->whereFindInSet('set_test',$set_test.'');
                    }
                    

                    $time123 = $this->request->param('time123',null);
                    if($time123){
                        $query->whereLike('time123',"%{$time123}%");
                    }
                    

                    $switch = $this->request->param('switch',null);
                    if($switch){
                        $query->whereLike('switch',"%{$switch}%");
                    }
                    

                $state = $this->request->param('state',null);
                if($state){
                    $query->where('state',$state);
                }
                

                    $test1_name_id = $this->request->param('test1_name_id',null);
                    if($test1_name_id){
                        $query->where('test1_name_id',$test1_name_id);
                    }
                    

                    $test1_name_ids = $this->request->param('test1_name_ids',null);
                    if($test1_name_ids){
                        $query->whereFindInSet('test1_name_ids',$test1_name_ids.'');
                    }
                    
        })
		   ->page($page,$limit)->select();
   		return json([
				'code'=> 0,
				'count'=> $count,
   				'data'=>$data,
   				'msg'=>__('Search successful')
   		]);
   }
   public function getTest1NameList(){
    $data =  \think\facade\Db::name('test1_name')->field('id,name')->select();
    return json([
            'code'=> 0,
            'count'=> count($data),
            'data'=>$data,
            'msg'=>__('Search successful')
   	]);
}
   public function add(){
	   	if($this->request->isPost()){
	   		$data = $this->request->post();
	   		unset($data['file']);
            
                
	   		if( $this->model->save($data,false)){
	   			$this->success(__('Add successful'));
	   		}else{
	   			$this->error(__('Add failed'));
	   		}
	   	}
		$test1Names = \think\facade\Db::name('test1_name')->field('id,name')->select();
View::assign('test1Names',$test1Names);
	   	return View::fetch('edit');
   }

   public function edit(){
	   	if($this->request->isPost()){
	   		$data = $this->request->post();
	   		unset($data['file']);
            
            if( $this->model->find($data['id'])->save($data)){
	   			$this->success(__('Editor successful'));
	   		}else{
	   			$this->error(__('Editor failed'));
	   		}
	   	}
	   	$id = $this->request->param('id');
	   	if(!$id){
	   		$this->success(__('Parameter error'));
	   	}
	   	$info =  $this->model->where('id',$id)->find();
   		if(!$info){
	   		$this->success(__('Parameter error'));
	   	}
		$test1Names = \think\facade\Db::name('test1_name')->field('id,name')->select();
View::assign('test1Names',$test1Names);
	   	View::assign('test_name',$info);
        return View::fetch('edit');
   }

   public function delete(){
   		$idsStr = $this->request->param('idsStr');
   		if(!$idsStr){
   			$this->success(__('Parameter error'));
   		}
   		if( $this->model->where('id','in',$idsStr)->delete()){
   			$this->success(__('Delete successful'));
   		}else{
   			$this->error(__('Delete error'));
   		}
   }

   public function sw(){
      		$id = $this->request->param('id');
      		$switch = $this->request->param('switch');
      		if( $this->model->where('id',$id)->update(['switch' => $switch])){
      			$this->success(__('Editor successful'));
      		}else{
      			$this->error(__('Editor failed'));
      		}
      }

}
