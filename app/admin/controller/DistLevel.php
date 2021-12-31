<?php
namespace app\admin\controller;

use app\common\controller\AddonBase;
use think\facade\View;
use think\facade\Db;

class DistLevel extends AdminBase
{

    public function initialize(){
		parent::initialize();
        $this->model = new \app\admin\model\DistLevel();
    }
    public function index(){
        $extra=[];
        $related_ids = $this->request->param('related_ids','');
        $related_field = $this->request->param('related_field','');

        if($related_ids&&$related_field){
            $extra=["$related_field"=>["in","($related_ids)"]];
        }
        View::assign('related_ids',$related_ids);
        View::assign('related_field',$related_field);
		if(!$this->request->isAjax()){
        	return View::fetch();
		}else{
			return $this->getList($extra);
		}
    }

   public function getList($extra=[]){
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
                    

                    $mall_id = $this->request->param('mall_id',null);
                    if($mall_id){
                        $query->where('mall_id',$mall_id);
                    }
                    

                    $level = $this->request->param('level',null);
                    if($level){
                        $query->whereLike('level',"%{$level}%");
                    }
                    

                    $name = $this->request->param('name',null);
                    if($name){
                        $query->whereLike('name',"%{$name}%");
                    }
                    

                    $condition_type = $this->request->param('condition_type',null);
                    if($condition_type){
                        $query->whereLike('condition_type',"%{$condition_type}%");
                    }
                    

                    $condition = $this->request->param('condition',null);
                    if($condition){
                        $query->whereLike('condition',"%{$condition}%");
                    }
                    

                    $price_type = $this->request->param('price_type',null);
                    if($price_type){
                        $query->whereLike('price_type',"%{$price_type}%");
                    }
                    

                    $first = $this->request->param('first',null);
                    if($first){
                        $query->whereLike('first',"%{$first}%");
                    }
                    

                    $second = $this->request->param('second',null);
                    if($second){
                        $query->whereLike('second',"%{$second}%");
                    }
                    

                    $third = $this->request->param('third',null);
                    if($third){
                        $query->whereLike('third',"%{$third}%");
                    }
                    

                    $is_auto_level = $this->request->param('is_auto_level',null);
                    if($is_auto_level){
                        $query->whereLike('is_auto_level',"%{$is_auto_level}%");
                    }
                    

                    $rule = $this->request->param('rule',null);
                    if($rule){
                        $query->whereLike('rule',"%{$rule}%");
                    }
                    

                    $status = $this->request->param('status',null);
                    if($status){
                        $query->whereLike('status',"%{$status}%");
                    }
                    
            $related_ids = $this->request->param('related_ids',null);
            $related_field = $this->request->param('related_field',null);
            if($related_ids&&$related_field){
                   $query->whereIn("$related_field","$related_ids");
             }})
           ->order('id','desc')
		   ->page($page,$limit)->select();
   		return json([
				'code'=> 0,
				'count'=> $count,
   				'data'=>$data,
   				'msg'=>__('Search successful')
   		]);
   }
   public function getMallList(){
    $data =  \think\facade\Db::name('mall')->field('id,name')->select();
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
		$malls = \think\facade\Db::name('mall')->field('id,name')->select();
       View::assign('malls',$malls);
	   	return View::fetch('edit');
   }


   public function leading(){
   	   	if($this->request->isPost()){
   	   		$file = $_FILES['file'];
   	   		$inputFileName = $file['tmp_name'];
            try {
                ob_end_clean();//清除缓冲区,避免乱码
                $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);

                $objReader  = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

                $objPHPExcel = $objReader->load($inputFileName);
                $sheet = $objPHPExcel->getSheet(0);
                $data = $sheet->toArray(); //该方法读取不到图片，图片需单独处理
                $saveTime=date('Y-m-d');
                $imageFilePath = "/excel/imgs/".$saveTime.'/'; //图片本地存储的路径
                if (!file_exists($imageFilePath)) {
                    mkdir($imageFilePath, 0777, true);
                }
                //处理图片
                foreach ($sheet->getDrawingCollection() as $img) {
                    list($startColumn, $startRow) = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($img->getCoordinates()); //获取图片所在行和列
                    $imageFileName = $img->getCoordinates() . mt_rand(1000, 9999);
                    switch($img->getExtension()) {
                        case 'jpg':
                        case 'jpeg':
                            $imageFileName .= '.jpeg';
                            $source = imagecreatefromjpeg($img->getPath());
                            imagejpeg($source, $imageFilePath.$imageFileName);
                            break;
                        case 'gif':
                            $imageFileName .= '.gif';
                            $source = imagecreatefromgif($img->getPath());
                            imagejpeg($source, $imageFilePath.$imageFileName);
                            break;
                        case 'png':
                            $imageFileName .= '.png';
                            $source = imagecreatefrompng($img->getPath());
                            imagejpeg($source, \str_replace('./storage/','',$imageFilePath) .$imageFileName);
                            break;
                    }
                    $startColumn = \hexdec($startColumn);
                    $data[$startRow-1][$startColumn-10] = $imageFilePath . $imageFileName;

                }
            } catch(\Exception $e) {
                 die('加载文件发生错误：”'.pathinfo($inputFileName,PATHINFO_BASENAME).'”: '.$e->getMessage());
            }
            //形成数组
            $excel_data= $data;


            
            $insert_data_dist_level = array();
            $excel_data_dist_level = array();
            
            foreach($excel_data_dist_level as $k=>$v){    
                if($k>0){
                    $insert_data_dist_level[$k]['mall_id'] = isset($v[0]) ? $v[0] : '';
                    $insert_data_dist_level[$k]['level'] = isset($v[1]) ? $v[1] : '';
                    $insert_data_dist_level[$k]['name'] = isset($v[2]) ? $v[2] : '';
                    $insert_data_dist_level[$k]['condition_type'] = isset($v[3]) ? $v[3] : '';
                    $insert_data_dist_level[$k]['condition'] = isset($v[4]) ? $v[4] : '';
                    $insert_data_dist_level[$k]['price_type'] = isset($v[5]) ? $v[5] : '';
                    $insert_data_dist_level[$k]['first'] = isset($v[6]) ? $v[6] : '';
                    $insert_data_dist_level[$k]['second'] = isset($v[7]) ? $v[7] : '';
                    $insert_data_dist_level[$k]['third'] = isset($v[8]) ? $v[8] : '';
                    $insert_data_dist_level[$k]['is_auto_level'] = isset($v[9]) ? $v[9] : '';
                    $insert_data_dist_level[$k]['rule'] = isset($v[10]) ? $v[10] : '';
                    $insert_data_dist_level[$k]['status'] = isset($v[11]) ? $v[11] : '';
                    
                }
                $insert_result=$this->model->saveAll($insert_data_dist_level,false);
                
            }

   	   		if( $insert_result){
   	   			$this->success(__('Add successful'));
   	   		}else{
   	   			$this->error(__('Add failed'));
   	   		}
   	   	}
   		    $malls = \think\facade\Db::name('mall')->field('id,name')->select();
       View::assign('malls',$malls);
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
		$malls = \think\facade\Db::name('mall')->field('id,name')->select();
View::assign('malls',$malls);
	   	View::assign('dist_level',$info);
        return View::fetch('edit');
   }

   public function delete(){
   		$idsStr = $this->request->param('idsStr');
   		if(!$idsStr){
   			$this->success(__('Parameter error'));
   		}
   		$pk=$this->model->getPk();
   		if( $this->model->where($pk,'in',$idsStr)->delete()){
   			$this->success(__('Delete successful'));
   		}else{
   			$this->error(__('Delete error'));
   		}
   }

   public function sw(){
      	$data = $this->request->param();
            if( $this->model->where('id',$data['id'])->update($data)){
                 $this->success(__('Editor successful'));
            }else{
                 $this->error(__('Editor failed'));
            }
      }

}
