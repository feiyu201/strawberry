<?php
namespace app\admin\controller;

use think\facade\View;
use think\facade\Db;

class User extends AdminBase
{

    public function initialize(){
		parent::initialize();
        $this->model = new \app\admin\model\User();
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
                $query->dateRange('prevtime',$this->request->param('prevtime',null))
                ->dateRange('logintime',$this->request->param('logintime',null))
                ->dateRange('jointime',$this->request->param('jointime',null))
                ->dateRange('past_time',$this->request->param('past_time',null))
                ->dateRange('begin_time',$this->request->param('begin_time',null))
                ->dateRange('createtime',$this->request->param('createtime',null))
                ->dateRange('updatetime',$this->request->param('updatetime',null));
                
                    $id = $this->request->param('id',null);
                    if($id){
                        $query->whereLike('id',"%{$id}%");
                    }
                    

                    $username = $this->request->param('username',null);
                    if($username){
                        $query->whereLike('username',"%{$username}%");
                    }
                    

                    $openid = $this->request->param('openid',null);
                    if($openid){
                        $query->whereLike('openid',"%{$openid}%");
                    }
                    

                    $nickname = $this->request->param('nickname',null);
                    if($nickname){
                        $query->whereLike('nickname',"%{$nickname}%");
                    }
                    

                    $password = $this->request->param('password',null);
                    if($password){
                        $query->whereLike('password',"%{$password}%");
                    }
                    

                    $salt = $this->request->param('salt',null);
                    if($salt){
                        $query->whereLike('salt',"%{$salt}%");
                    }
                    

                    $email = $this->request->param('email',null);
                    if($email){
                        $query->whereLike('email',"%{$email}%");
                    }
                    

                    $mobile = $this->request->param('mobile',null);
                    if($mobile){
                        $query->whereLike('mobile',"%{$mobile}%");
                    }
                    

                    $avatar = $this->request->param('avatar',null);
                    if($avatar){
                        $query->whereLike('avatar',"%{$avatar}%");
                    }
                    

                    $level = $this->request->param('level',null);
                    if($level){
                        $query->whereLike('level',"%{$level}%");
                    }
                    

                    $gender = $this->request->param('gender',null);
                    if($gender){
                        $query->whereLike('gender',"%{$gender}%");
                    }
                    

                    $birthday = $this->request->param('birthday',null);
                    if($birthday){
                        $query->whereLike('birthday',"%{$birthday}%");
                    }
                    

                    $bio = $this->request->param('bio',null);
                    if($bio){
                        $query->whereLike('bio',"%{$bio}%");
                    }
                    

                    $money = $this->request->param('money',null);
                    if($money){
                        $query->whereLike('money',"%{$money}%");
                    }
                    

                    $score = $this->request->param('score',null);
                    if($score){
                        $query->whereLike('score',"%{$score}%");
                    }
                    

                    $successions = $this->request->param('successions',null);
                    if($successions){
                        $query->whereLike('successions',"%{$successions}%");
                    }
                    

                    $maxsuccessions = $this->request->param('maxsuccessions',null);
                    if($maxsuccessions){
                        $query->whereLike('maxsuccessions',"%{$maxsuccessions}%");
                    }
                    

                    $loginip = $this->request->param('loginip',null);
                    if($loginip){
                        $query->whereLike('loginip',"%{$loginip}%");
                    }
                    

                    $loginfailure = $this->request->param('loginfailure',null);
                    if($loginfailure){
                        $query->whereLike('loginfailure',"%{$loginfailure}%");
                    }
                    

                    $joinip = $this->request->param('joinip',null);
                    if($joinip){
                        $query->whereLike('joinip',"%{$joinip}%");
                    }
                    

                    

                    $status = $this->request->param('status',null);
                    if($status){
                        $query->whereLike('status',"%{$status}%");
                    }
                    

                    $verification = $this->request->param('verification',null);
                    if($verification){
                        $query->whereLike('verification',"%{$verification}%");
                    }
                    

                    $inviter_mem_info_id = $this->request->param('inviter_mem_info_id',null);
                    if($inviter_mem_info_id){
                        $query->where('inviter_mem_info_id',$inviter_mem_info_id);
                    }
                    

                    $inviter_code = $this->request->param('inviter_code',null);
                    if($inviter_code){
                        $query->whereLike('inviter_code',"%{$inviter_code}%");
                    }
                    
            })
            ->order('id','desc')
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
            $query->dateRange('prevtime',$this->request->param('prevtime',null))
->dateRange('logintime',$this->request->param('logintime',null))
->dateRange('jointime',$this->request->param('jointime',null))
->dateRange('past_time',$this->request->param('past_time',null))
->dateRange('begin_time',$this->request->param('begin_time',null))
->dateRange('createtime',$this->request->param('createtime',null))
->dateRange('updatetime',$this->request->param('updatetime',null));
            
                    $id = $this->request->param('id',null);
                    if($id){
                        $query->whereLike('id',"%{$id}%");
                    }
                    

                    $username = $this->request->param('username',null);
                    if($username){
                        $query->whereLike('username',"%{$username}%");
                    }
                    

                    $openid = $this->request->param('openid',null);
                    if($openid){
                        $query->whereLike('openid',"%{$openid}%");
                    }
                    

                    $nickname = $this->request->param('nickname',null);
                    if($nickname){
                        $query->whereLike('nickname',"%{$nickname}%");
                    }
                    

                    $password = $this->request->param('password',null);
                    if($password){
                        $query->whereLike('password',"%{$password}%");
                    }
                    

                    $salt = $this->request->param('salt',null);
                    if($salt){
                        $query->whereLike('salt',"%{$salt}%");
                    }
                    

                    $email = $this->request->param('email',null);
                    if($email){
                        $query->whereLike('email',"%{$email}%");
                    }
                    

                    $mobile = $this->request->param('mobile',null);
                    if($mobile){
                        $query->whereLike('mobile',"%{$mobile}%");
                    }
                    

                    $avatar = $this->request->param('avatar',null);
                    if($avatar){
                        $query->whereLike('avatar',"%{$avatar}%");
                    }
                    

                    $level = $this->request->param('level',null);
                    if($level){
                        $query->whereLike('level',"%{$level}%");
                    }
                    

                    $gender = $this->request->param('gender',null);
                    if($gender){
                        $query->whereLike('gender',"%{$gender}%");
                    }
                    

                    $birthday = $this->request->param('birthday',null);
                    if($birthday){
                        $query->whereLike('birthday',"%{$birthday}%");
                    }
                    

                    $bio = $this->request->param('bio',null);
                    if($bio){
                        $query->whereLike('bio',"%{$bio}%");
                    }
                    

                    $money = $this->request->param('money',null);
                    if($money){
                        $query->whereLike('money',"%{$money}%");
                    }
                    

                    $score = $this->request->param('score',null);
                    if($score){
                        $query->whereLike('score',"%{$score}%");
                    }
                    

                    $successions = $this->request->param('successions',null);
                    if($successions){
                        $query->whereLike('successions',"%{$successions}%");
                    }
                    

                    $maxsuccessions = $this->request->param('maxsuccessions',null);
                    if($maxsuccessions){
                        $query->whereLike('maxsuccessions',"%{$maxsuccessions}%");
                    }
                    

                    $loginip = $this->request->param('loginip',null);
                    if($loginip){
                        $query->whereLike('loginip',"%{$loginip}%");
                    }
                    

                    $loginfailure = $this->request->param('loginfailure',null);
                    if($loginfailure){
                        $query->whereLike('loginfailure',"%{$loginfailure}%");
                    }
                    

                    $joinip = $this->request->param('joinip',null);
                    if($joinip){
                        $query->whereLike('joinip',"%{$joinip}%");
                    }
                    

            

                    $status = $this->request->param('status',null);
                    if($status){
                        $query->whereLike('status',"%{$status}%");
                    }
                    

                    $verification = $this->request->param('verification',null);
                    if($verification){
                        $query->whereLike('verification',"%{$verification}%");
                    }
                    

                    $inviter_mem_info_id = $this->request->param('inviter_mem_info_id',null);
                    if($inviter_mem_info_id){
                        $query->where('inviter_mem_info_id',$inviter_mem_info_id);
                    }
                    

                    $inviter_code = $this->request->param('inviter_code',null);
                    if($inviter_code){
                        $query->whereLike('inviter_code',"%{$inviter_code}%");
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
           
        $insert_data[$k]['username'] = isset($v[0]) ? $v[0] : '';
$insert_data[$k]['openid'] = isset($v[1]) ? $v[1] : '';
$insert_data[$k]['nickname'] = isset($v[2]) ? $v[2] : '';
$insert_data[$k]['password'] = isset($v[3]) ? $v[3] : '';
$insert_data[$k]['salt'] = isset($v[4]) ? $v[4] : '';
$insert_data[$k]['email'] = isset($v[5]) ? $v[5] : '';
$insert_data[$k]['mobile'] = isset($v[6]) ? $v[6] : '';
$insert_data[$k]['avatar'] = isset($v[7]) ? $v[7] : '';
$insert_data[$k]['level'] = isset($v[8]) ? $v[8] : '';
$insert_data[$k]['gender'] = isset($v[9]) ? $v[9] : '';
$insert_data[$k]['birthday'] = isset($v[10]) ? $v[10] : '';
$insert_data[$k]['bio'] = isset($v[11]) ? $v[11] : '';
$insert_data[$k]['money'] = isset($v[12]) ? $v[12] : '';
$insert_data[$k]['score'] = isset($v[13]) ? $v[13] : '';
$insert_data[$k]['successions'] = isset($v[14]) ? $v[14] : '';
$insert_data[$k]['maxsuccessions'] = isset($v[15]) ? $v[15] : '';
$insert_data[$k]['loginip'] = isset($v[18]) ? $v[18] : '';
$insert_data[$k]['loginfailure'] = isset($v[19]) ? $v[19] : '';
$insert_data[$k]['joinip'] = isset($v[20]) ? $v[20] : '';

$insert_data[$k]['status'] = isset($v[27]) ? $v[27] : '';
$insert_data[$k]['verification'] = isset($v[28]) ? $v[28] : '';
$insert_data[$k]['inviter_mem_info_id'] = isset($v[29]) ? $v[29] : '';
$insert_data[$k]['inviter_code'] = isset($v[30]) ? $v[30] : '';
}
            }


   	   		if( $this->model->saveAll($insert_data,false)){
   	   			$this->success(__('Add successful'));
   	   		}else{
   	   			$this->error(__('Add failed'));
   	   		}
   	   	}
   		$inviterMemInfos = \think\facade\Db::name('inviter_mem_info')->field('id,name')->select();
View::assign('inviterMemInfos',$inviterMemInfos);
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
		
	   	View::assign('user',$info);
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
