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

class Category extends AdminBase
{

    public function initialize(){
		parent::initialize();
        $this->model = new \app\admin\model\Category();
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
                
                    $id = $this->request->param('id',null);
                    if($id){
                        $query->whereLike('id',"%{$id}%");
                    }
                    

                    $name = $this->request->param('name',null);
                    if($name){
                        $query->whereLike('name',"%{$name}%");
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
   

   public function add(){
	   	if($this->request->isPost()){
	   		$data = $this->request->post();
            
                
	   		if( $this->model->save($data,false)){
	   			$this->success(__('Add successful'));
	   		}else{
	   			$this->error(__('Add failed'));
	   		}
	   	}
		
	   	return View::fetch('edit');
   }

   public function edpot(){
   		if(!$this->request->isAjax()){
           	return View::fetch();
   		}else{
   			return $this->getEdpot();
   		}
    }


    public function getEdpot(){
   		$page = $this->request->param('page',1,'intval');
   		$limit = $this->request->param('limit',10,'intval');
   		$count = $this->model->count();
   		$data = $this->model->with([])
		   ->where(function($query){
            
                    $id = $this->request->param('id',null);
                    if($id){
                        $query->whereLike('id',"%{$id}%");
                    }
                    

                    $name = $this->request->param('name',null);
                    if($name){
                        $query->whereLike('name',"%{$name}%");
                    }
                    
            $query->where('deletetime','>',0);
        })
		   ->page($page,$limit)->select();
   		return json([
				'code'=> 0,
				'count'=> $count,
   				'data'=>$data,
   				'msg'=>__('Search successful')
   		]);
   }

   public function leading(){
   	   	if($this->request->isPost()){
   	   		$file = $_FILES['file'];
   	   		$inputFileName = $file['tmp_name'];
            try {
                ob_end_clean();//清除缓冲区,避免乱码
                $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);

                $objReader  = \PHPExcel_IOFactory::createReader($inputFileType);

                $objPHPExcel = $objReader->load($inputFileName);
            } catch(\Exception $e) {
                 die('加载文件发生错误：”'.pathinfo($inputFileName,PATHINFO_BASENAME).'”: '.$e->getMessage());
            }
            //形成数组
             $excel_data = $objPHPExcel->getSheet(0)->toArray();


            $insert_data = array();
            foreach($excel_data as $k=>$v){
        
                
        if($k>0){
           
        $insert_data[$k]['name'] = isset($v[0]) ? $v[0] : '';
}
            }


   	   		if( $this->model->saveAll($insert_data,false)){
   	   			$this->success(__('Add successful'));
   	   		}else{
   	   			$this->error(__('Add failed'));
   	   		}
   	   	}
   		
   	   	return View::fetch('leading');
     }

   public function edit(){
	   	if($this->request->isPost()){
	   		$data = $this->request->post();
            
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
		
	   	View::assign('category',$info);
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

   public function trash(){
      		$idsStr = $this->request->param('idsStr');
      		if(!$idsStr){
      			$this->success(__('Parameter error'));
      		}
      		if( $this->model->where('id','in',$idsStr)->update(['deletetime' => time()])){
      			$this->success(__('Delete successful'));
      		}else{
      			$this->error(__('Delete error'));
      		}
      }

   public function setNormal(){
        		$idsStr = $this->request->param('idsStr');
        		if(!$idsStr){
        			$this->success(__('Parameter error'));
        		}
        		if( $this->model->where('id','in',$idsStr)->update(['deletetime' => 0])){
        			$this->success('还原成功');
        		}else{
        			$this->error('还原失败');
        		}
        }


    public function setEnable(){
            		$id = $this->request->param('id');
            		if(!$id){
            			$this->success(__('Parameter error'));
            		}
            		if( $this->model->where('id',$id)->update(['deletetime' => 0])){
            			$this->success('还原成功');
            		}else{
            			$this->error('还原失败');
            		}
            }

    public function allEnabled(){
           if( $this->model->where('deletetime > 0')->update(['deletetime' => 0])){
            	$this->success('还原成功');
            }else{
            	$this->error('还原失败');
            }
     }


    public function allDel(){

                		if( $this->model->where('deletetime > 0')->delete()){
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
