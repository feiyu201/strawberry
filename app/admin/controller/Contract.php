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

class Contract extends AdminBase
{

    public function initialize(){
		parent::initialize();
        $this->model = new \app\admin\model\Contract();
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
->dateRange('update_time',$this->request->param('update_time',null))
->dateRange('delete_time',$this->request->param('delete_time',null));
                
                    $id = $this->request->param('id',null);
                    if($id){
                        $query->whereLike('id',"%{$id}%");
                    }
                    

                    $contract_name = $this->request->param('contract_name',null);
                    if($contract_name){
                        $query->whereLike('contract_name',"%{$contract_name}%");
                    }
                    

                    $market_price = $this->request->param('market_price',null);
                    if($market_price){
                        $query->whereLike('market_price',"%{$market_price}%");
                    }
                    

                    $sell_price = $this->request->param('sell_price',null);
                    if($sell_price){
                        $query->whereLike('sell_price',"%{$sell_price}%");
                    }
                    

                    $category_id = $this->request->param('category_id',null);
                    if($category_id){
                        $query->where('category_id',$category_id);
                    }
                    

                    $switch = $this->request->param('switch',null);
                    if($switch){
                        $query->whereLike('switch',"%{$switch}%");
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
   public function getCategoryList(){
    $data =  \think\facade\Db::name('category')->field('id,name')->select();
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
            
                
	   		if( $this->model->save($data,false)){
	   			$this->success(__('Add successful'));
	   		}else{
	   			$this->error(__('Add failed'));
	   		}
	   	}
		$categorys = \think\facade\Db::name('category')->field('id,name')->select();
View::assign('categorys',$categorys);
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
            $query->dateRange('create_time',$this->request->param('create_time',null))
->dateRange('update_time',$this->request->param('update_time',null))
->dateRange('delete_time',$this->request->param('delete_time',null));
            
                    $id = $this->request->param('id',null);
                    if($id){
                        $query->whereLike('id',"%{$id}%");
                    }
                    

                    $contract_name = $this->request->param('contract_name',null);
                    if($contract_name){
                        $query->whereLike('contract_name',"%{$contract_name}%");
                    }
                    

                    $market_price = $this->request->param('market_price',null);
                    if($market_price){
                        $query->whereLike('market_price',"%{$market_price}%");
                    }
                    

                    $sell_price = $this->request->param('sell_price',null);
                    if($sell_price){
                        $query->whereLike('sell_price',"%{$sell_price}%");
                    }
                    

                    $category_id = $this->request->param('category_id',null);
                    if($category_id){
                        $query->where('category_id',$category_id);
                    }
                    

                    $switch = $this->request->param('switch',null);
                    if($switch){
                        $query->whereLike('switch',"%{$switch}%");
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
           
        $insert_data[$k]['contract_name'] = isset($v[0]) ? $v[0] : '';
$insert_data[$k]['market_price'] = isset($v[1]) ? $v[1] : '';
$insert_data[$k]['sell_price'] = isset($v[2]) ? $v[2] : '';
$insert_data[$k]['contract_file'] = isset($v[3]) ? $v[3] : '';
$insert_data[$k]['category_id'] = isset($v[4]) ? $v[4] : '';
$insert_data[$k]['switch'] = isset($v[5]) ? $v[5] : '';
}
            }


   	   		if( $this->model->saveAll($insert_data,false)){
   	   			$this->success(__('Add successful'));
   	   		}else{
   	   			$this->error(__('Add failed'));
   	   		}
   	   	}
   		$categorys = \think\facade\Db::name('category')->field('id,name')->select();
View::assign('categorys',$categorys);
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
		$categorys = \think\facade\Db::name('category')->field('id,name')->select();
View::assign('categorys',$categorys);
	   	View::assign('contract',$info);
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
