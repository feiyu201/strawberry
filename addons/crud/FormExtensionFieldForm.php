<?php
namespace common\models;

use Yii;
use yii\base\Model;	
use common\models\FormExtensionField;
use common\models\FormClass;
use common\models\FormExtensionMetadata;
use common\encrypt\ApiMask;
use common\models\FormRelevancy;
use common\models\TRegion;
use common\service\CustomerAssignService;
use common\models\contract\Contract;
use backend\models\AuthAssignment;
use common\service\SessionService;
use api\modules\v1\document\models\Upload;
/**
 * 自定义扩展属性核心类
 */
class FormExtensionFieldForm extends Model
{
	// 自定义属性模型
	public $fieldModel = null;
	//系统id
	public $companyid     = null;
	//模块id
	public $module     = null;
	/**
	 * 构造函数 查询模块自定义字段
	 * @param string $companyid 系统id
	 * @param string $module 模块id
	 */
	public function __construct($companyid=null,$module=null)
	{
		// 获取这个模块自定义属性列表
		if($companyid && $module){
			$fields = FormExtensionField::find()
				->where(['companyid' => $companyid,'module'=>$module,"status"=>1])->orderBy(['sort' => SORT_ASC])
				->asArray()
				->all();
			$this->fieldModel = $fields;
			$this->companyid     = $companyid;
			$this->module     = $module;
		}

	}
	/**
	 * 获取这个模块自定义属性列表
	 * @param array $type 自定义属性显示条件
	 */
	public function setFieldModelType($type){
		$where = array_merge($type,['companyid' => $this->companyid,'module'=>$this->module,"status"=>1]); 
		$fields = FormExtensionField::find()
			->where($where)->orderBy(['sort' => SORT_ASC])
			->asArray()
			->all();
		if($fields){ 
			$this->fieldModel = $fields;
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 获取这个模块自定义属性列表	
	 * @param  string $key 设置数组主键
	 * @return array      自定义属性列表
	 */
	public function getFieldModel($key=null){
		if($key){
			foreach ($this->fieldModel as  $value) {
				$k = $value[$key];
				$return[$k] = $value;
			}
		}else{
			$return = $this->fieldModel;
		}
		return $return;

	}
	/**
	 * 获取扩展属性Model
	 * @return object         
	 */
	public function getExtensionFieldModel()
    {
		$fields          = $this->fieldModel;
		$required        = [];
		$integer         = [];
		$max             = [];
		$number          = [];
		$attributeLabels = [];
		$attributeValue  = [];
		// 统计自定义属性规则
		foreach ($fields as $key => $value) {
			if($value['required']){
				$required[] = $value['name'];
			}
			switch ($value['type'])
			{
			case 'integer':
			  $integer[] = $value['name'];
			  break;
			case 'number':
			  $number[] = $value['name'];
			  break;
			case 'date':
			  break;
			default:
				if($value['max']){
					 $max[$value['max']][] = $value['name'];
				}
			}
			$k = $value['name'];
			$attributeLabels["$k"] = $value['label'];
			if($value['value']){
				$attributeValue["$k"] = $value['value'];
			}
		}
		$formRules[] = [$required,'required']; 
		$formRules[] = [$integer,'integer']; 
		$formRules[] = [$number,'number']; 
		foreach ($max as $key => $value) {
			$formRules[] = [$value,'string','max'=>$key];
		}
		// 生成模板对象
		$model = new FormClass($formRules,$attributeLabels,$attributeValue);
		return $model;
    }

    /**
	 * 获取自定义字段
	 * @return array         自定义属性列表
	 */
	public function getExtensionField()
    {
		return $this->fieldModel;
    }

    /**
     * 添加一个实体(用户)
     * @param array $data 实体属性数据
    [
        'name' => '联系人2'
        'sex' => '0'
        'mobile' => '13933334444'
        'uid' => '1456919557'
    ]

	 * @return bool         成功|失败
     */
    public function addEntity($data){
    	if(!$data['uid']){
    		return false;
    	}
    	$fieldForm = $this->getExtensionField();	
    	if(!$fieldForm){
    		return false;
    	}
        $insert = [];
    	$time = time();
    	foreach ($fieldForm as $key => $value) {
    		$dataKey = $value['name'];
    		if(!empty($data["$dataKey"])){
                    $valueStr = null;
                    $keyStr   = null;
                    $tmp      = null;
                    if($value['type'] == 'checkbox'){
                        $checkboxsettings = [];
                        $tmp = [];
                        if(!empty($value['setting'])){
                            $checkboxsetting = json_decode($value['setting']); 
                            if(!empty($checkboxsetting)){
                                foreach($checkboxsetting as $k=>$v){
                                    $checkboxsettings[$v->id] = $v->value;
                                }
                            }
                        }
                        if(!empty($checkboxsettings)){
                            foreach ($data["$dataKey"] as $d) {
                                    $tmp[] = $checkboxsettings[$d];
                            }
                        }
                        if(!empty($tmp)){
                            $valueStr = implode(',', $tmp);
                        }

                        $keyStr   = implode(',', $data["$dataKey"]);

                    }elseif($value['type'] == 'radio'){
                            
                                if(!empty($value['setting'])){
                                    $radiosetting = json_decode($value['setting']); 
                                    if(!empty($radiosetting)){
                                        foreach($radiosetting as $k1=>$v1){
                                            if($v1->id == $data["$dataKey"]){
                                                $valueStr = $v1->value;
                                            }
                                        }
                                    }
                                }

                                $keyStr   = $data["$dataKey"];
                            
                    }elseif($value['type'] == 'dropDownList'){
                            if($data["$dataKey"]){
                                    $valueStr = explode(',', $value['setting'])[$data["$dataKey"]];
                                    $keyStr   = $data["$dataKey"];
                            }
                    }else{
                            $valueStr = $data["$dataKey"];
                    }
                    $insert[] = [$data['uid'],$value['id'],$valueStr,$keyStr,$time,$value['module'],$value['companyid']];	
    		}
                
    	}
    	// FormExtensionMetadata 数据表字段
    	$metadataField[] = 'uid';
    	$metadataField[] = 'field_id';
    	$metadataField[] = 'value';
    	$metadataField[] = 'value_key';
    	$metadataField[] = 'create_time';
        $metadataField[] = 'module';
    	$metadataField[] = 'companyid';
    	$connection = Yii::$app->db;
    	// 属性值插入数据表
		if($connection->createCommand()->batchInsert(FormExtensionMetadata::tableName(), $metadataField, $insert)->execute()){
			return true;
		}else{
			return false;
		}
    }
    
    /**
     * 添加一个实体(用户)
     * @param array $data 实体属性数据
    [
        'name' => '联系人2'
        'sex' => '0'
        'mobile' => '13933334444'
        'uid' => '1456919557'
    ]

	 * @return bool         成功|失败
     */
    public function addEntity1($data){
        
    	if(!$data['uid']){
    		return false;
    	}
         
    	$fieldForm = $this->getExtensionField();
       // var_dump($data);exit;
    	if(!$fieldForm){
    		return false;
    	}
    	$time = time();
      
    	foreach ($fieldForm as $key => $value) {    
            $dataKey = $value['name'];
            if(!empty($data["$dataKey"])){
                $valueStr = null;
                $keyStr   = null;
                $tmp      = null;
                if($value['type'] == 'checkbox'){
                    $checkboxsettings = [];
                    $tmp = [];
                    if(!empty($value['setting'])){
                        $checkboxsetting = json_decode($value['setting']); 
                        if(!empty($checkboxsetting)){
                            foreach($checkboxsetting as $k=>$v){
                                $checkboxsettings[$v->id] = $v->value;
                            }
                        }
                    }
                   // var_dump(!empty($data["$dataKey"]));exit;
                    if(!empty($checkboxsettings)&&!empty($data["$dataKey"])){
                        foreach ($data["$dataKey"] as $d) {
                                $tmp[] = $checkboxsettings[$d];
                        }
                    }
                    if(!empty($tmp)){
                        $valueStr = implode(',', $tmp);
                    }

                    $keyStr   = implode(',', $data["$dataKey"]);

                }elseif($value['type'] == 'radio'){
                    if(!empty($value['setting'])){
                        $radiosetting = json_decode($value['setting']); 
                        
                        if(!empty($radiosetting)){
                            foreach($radiosetting as $k1=>$v1){
                                if($v1->id == $data["$dataKey"]){
                                    $valueStr = $v1->value;
                                }
                            }
                        }
                    }
                    $keyStr   = $data["$dataKey"];
                    
                }elseif($value['type'] == 'dropDownList'){
                    if($data["$dataKey"]){
                            $valueStr = explode(',', $value['setting'])[$data["$dataKey"]];
                            $keyStr   = $data["$dataKey"];
                    }
                }else{
                        $valueStr = $data["$dataKey"];
                }
                $FormExtensionMetadata = new FormExtensionMetadata;
                $FormExtensionMetadata->uid = (string)$data['uid'];
                $FormExtensionMetadata->field_id = $value['id'];
                $FormExtensionMetadata->value = (string)$valueStr;
                $FormExtensionMetadata->value_key = (string)$keyStr;
                $FormExtensionMetadata->create_time = $time;
                $FormExtensionMetadata->module = $value['module'];
                $FormExtensionMetadata->companyid = $value['companyid'];
                $FormExtensionMetadata->no_data_num = !empty($data['no_data_num'])?$data['no_data_num']:'';
              //  var_dump($FormExtensionMetadata);
                if (!$FormExtensionMetadata->save()) {
                    return array_values($FormExtensionMetadata->getFirstErrors())[0];
                }	
            }
    	}
        return true;
//        }
    }
    
    
    /**
     * 添加一个实体(用户)
     * @param array $data 实体属性数据
    [
        'name' => '联系人2'
        'sex' => '0'
        'mobile' => '13933334444'
        'uid' => '1456919557'
    ]

	 * @return bool         成功|失败
     */
    public function addEntity2($data){
        
    	if(!$data['uid']){
    		return false;
    	}
    	$fieldForm = $this->getExtensionField();
       // var_dump($data);
    	if(!$fieldForm){
    		return false;
    	}
    	$time = time();
      
    	foreach ($fieldForm as $key => $value) {    
            $dataKey = $value['name'];
            if(!empty($data["$dataKey"])){
                $valueStr = null;
                $keyStr   = null;
                $tmp      = null;
                if($value['type'] == 'checkbox'){
                    if($data["$dataKey"]){
                        $checkboxsettings = [];
                        $tmp = [];
                        if(!empty($value['setting'])){
                            $checkboxsetting = json_decode($value['setting']); 
                            if(!empty($checkboxsetting)){
                                foreach($checkboxsetting as $k=>$v){
                                    $checkboxsettings[$v->id] = $v->value;
                                }
                            }
                        }
                        
                        if(!empty($checkboxsettings)&&!empty($data["$dataKey"])){
                            $checkboxsettings = array_flip($checkboxsettings);
                            $res = explode(',', $data["$dataKey"]);
                            foreach ($res as $d) {
                                    $tmp[] = $checkboxsettings[$d];
                            }
                        }
                        if(!empty($tmp)){
                            $keyStr = implode(',', $tmp);
                        }
                        $valueStr   = $data["$dataKey"];
                    }
                    
                }elseif($value['type'] == 'radio' ){
                    if($data["$dataKey"]){
                        if(!empty($value['setting'])){
                            $radiosetting = json_decode($value['setting']); 
                            if(!empty($radiosetting)){
                                foreach($radiosetting as $k1=>$v1){
                                    
                                    if($v1->value == $data["$dataKey"]){
                                        $valueStr = $v1->value;
                                    }
                                }
                            }
                        }
                        $keyStr   = $data["$dataKey"];
                    }
                }elseif($value['type'] == 'dropDownList'){
                    if($data["$dataKey"]){
                        $valueStr = explode(',', $value['setting'])[$data["$dataKey"]];
                        $keyStr   = $data["$dataKey"];
                    }
                }else{
                        $valueStr = $data["$dataKey"];
                }
                
                $FormExtensionMetadata = new FormExtensionMetadata;
                $FormExtensionMetadata->uid = (string)$data['uid'];
                $FormExtensionMetadata->field_id = $value['id'];
                $FormExtensionMetadata->value = (string)$valueStr;
                $FormExtensionMetadata->value_key = (string)$keyStr;
                $FormExtensionMetadata->create_time = $time;
                $FormExtensionMetadata->module = $value['module'];
                $FormExtensionMetadata->companyid = $value['companyid'];
                $FormExtensionMetadata->no_data_num = !empty($data['no_data_num'])?$data['no_data_num']:'';
              //  var_dump($FormExtensionMetadata);
                if (!$FormExtensionMetadata->save()) {
                    return array_values($FormExtensionMetadata->getFirstErrors())[0];
                }	
            }
    	}
        return true;
//        }
    }
    
    
     /**
     * 添加一个实体(导入用)
     * @param array $data 实体属性数据
     * @return bool         成功|失败
     */
    public function addEntityUploading($data){
        
    	if(!$data['uid']){
    		return false;
    	}
        $insert_arr = []; 
    	$fieldForm = $this->getExtensionField();
       // var_dump($data);
    	if(!$fieldForm){
    		return false;
    	}
    	$time = time();
      
    	foreach ($fieldForm as $key => $value) {    
            $dataKey = $value['name'];
            if(!empty($data["$dataKey"])){
                $valueStr = null;
                $keyStr   = null;
                $tmp      = null;
                if($value['type'] == 'checkbox'){
                    if($data["$dataKey"]){
                        $checkboxsettings = [];
                        $tmp = [];
                        if(!empty($value['setting'])){
                            $checkboxsetting = json_decode($value['setting']); 
                            if(!empty($checkboxsetting)){
                                foreach($checkboxsetting as $k=>$v){
                                    $checkboxsettings[$v->id] = $v->value;
                                }
                            }
                        }
                        
                        if(!empty($checkboxsettings)&&!empty($data["$dataKey"])){
                            $checkboxsettings = array_flip($checkboxsettings);
                            $res = explode(',', $data["$dataKey"]);
                            foreach ($res as $d) {
                                    $tmp[] = $checkboxsettings[$d];
                            }
                        }
                        if(!empty($tmp)){
                            $keyStr = implode(',', $tmp);
                        }
                        $valueStr   = $data["$dataKey"];
                    }
                    
                }elseif($value['type'] == 'radio' ){
                    if($data["$dataKey"]){
                        if(!empty($value['setting'])){
                            $radiosetting = json_decode($value['setting']); 
                            if(!empty($radiosetting)){
                                foreach($radiosetting as $k1=>$v1){
                                    
                                    if($v1->value == $data["$dataKey"]){
                                        $valueStr = $v1->value;
                                    }
                                }
                            }
                        }
                        $keyStr   = $data["$dataKey"];
                    }
                }elseif($value['type'] == 'dropDownList'){
                    if($data["$dataKey"]){
                        $valueStr = explode(',', $value['setting'])[$data["$dataKey"]];
                        $keyStr   = $data["$dataKey"];
                    }
                }else{
                        $valueStr = $data["$dataKey"];
                }
                
                $insert_arr[] = array((string)$data['uid'],$value['id'],(string)$valueStr,(string)$keyStr,$time,$value['module'],$value['companyid'],$data['no_data_num']);
            
            }
    	}
        if(!empty($insert_arr)){
            Yii::$app->db->createCommand()->batchInsert(FormExtensionMetadata::tableName(), ['uid','field_id','value','value_key','create_time','module','companyid','no_data_num'],$insert_arr)->execute();
        }
        return true;
//        }
    }
    
    
    
    

    /**
     * 获取实体（用户）列表
     * @param  object  $where  查询条件
     * @param  array  $fieldType    字段类型 默认全部
     * @return array         实体列表
     */
    public function getEntity($where=null,$fieldType=[]){
    	if($fieldType){
    		$this->setFieldModelType($fieldType);
    	}
    	
		$fieldForm = $this->getExtensionField();	
		$quertSql  = '';
		if($where){
			if(count($where->where) > 0){
				$quertSql =' having  '.$this->getQuery($where);
			}else{
				$quertSql =' '.$this->getQuery($where);
			}
		}
    	$sqlField = [];
    	//使用group by进行分组查询
    	foreach ($fieldForm as $key => $value) {
    		$sqlField[] = "max(case `field_id` when ".$value['id']." then value else NULL end) '".$value['name']."'";
    	}
    	if(empty($sqlField)){
            $sqlStr = "select `uid` as 'id'".implode(',',$sqlField)."from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid` $quertSql";
        }else{
            $sqlStr = "select `uid` as 'id',".implode(',',$sqlField)."from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid` $quertSql";
        }
		$connection = Yii::$app->db;
		$command    = $connection->createCommand($sqlStr);
		$entityList = $command->queryAll();
	//	print_r($entityList);
		return $entityList;


    }
    
    
    /**
     * 获取实体（用户）列表
     * @param  object  $where  查询条件
     * @param  array  $fieldType    字段类型 默认全部
     * @return array         实体列表
     */
    public function getEntity1($where=null,$fieldType=[]){
    	if($fieldType){
    		$this->setFieldModelType($fieldType);
    	}
    	
		$fieldForm = $this->getExtensionField();	
		$quertSql  = '';
		if($where){
			if(count($where->where) > 0){
				$quertSql =' having  '.$this->getQuery($where);
			}else{
				$quertSql =' '.$this->getQuery($where);
			}
		}
    	$sqlField = [];
    	//使用group by进行分组查询
    	foreach ($fieldForm as $key => $value) {
    		$sqlField[] = "max(case `field_id` when ".$value['id']." then value else NULL end) '".$value['name']."'";
    	}
        if(empty($sqlField)){
            $sqlStr = "select `uid` as 'id'".implode(',',$sqlField)."from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid` $quertSql";
        }else{
            $sqlStr = "select `uid` as 'id',".implode(',',$sqlField)."from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid` $quertSql";
        }
    	
	//echo $sqlStr;exit;
        $connection = Yii::$app->db;
		$command    = $connection->createCommand($sqlStr);
		$entityList = $command->queryAll();
	//	print_r($entityList);
		return $entityList;


    }
    
    
    /**
     * 获取实体（用户）列表
     * @param  object  $where  查询条件
     * @param  array  $fieldType    字段类型 默认全部
     * @return array         实体列表
     */
    public function getEntity2($where=null,$fieldType=[]){
    	if($fieldType){
    		$this->setFieldModelType($fieldType);
    	}
    	
		$fieldForm = $this->getExtensionField();	
		$quertSql  = '';
		if($where){
			if(count($where->where) > 0){
				$quertSql =' having  '.$this->getQuery($where);
			}else{
				$quertSql =' '.$this->getQuery($where);
			}
		}
    	$sqlField = [];
    	//使用group by进行分组查询
    	foreach ($fieldForm as $key => $value) {
    		$sqlField[] = "max(case `field_id` when ".$value['id']." then value else NULL end) '".$value['name']."'";
    	}
        
        if(empty($sqlField)){
            $sqlStr = "select `uid` as 'uid',`no_data_num` ".implode(',',$sqlField)."from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid`  $quertSql";
        }else{
            $sqlStr = "select `uid` as 'uid',`no_data_num`, ".implode(',',$sqlField)."from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid`   $quertSql";
        }
		$connection = Yii::$app->db;
		$command    = $connection->createCommand($sqlStr);
		$entityList = $command->queryAll();
		//print_r($entityList);
                foreach($entityList as $key=>$val){
                    $entityList[$key]['uid'] = ApiMask::encrypt($val['uid']);
                }
                
		return $entityList;
    }
    
     /**
     * 获取实体（用户）列表
     * @param  object  $where  查询条件
     * @param  array  $fieldType    字段类型 默认全部
     * @return array         实体列表
     */
    public function getEntity22($where=null,$fieldType=[]){
    	if($fieldType){
    		$this->setFieldModelType($fieldType);
    	}
    	
		$fieldForm = $this->getExtensionField();	
		$quertSql  = '';
		if($where){
			if(count($where->where) > 0){
				$quertSql =' having  '.$this->getQuery($where);
			}else{
				$quertSql =' '.$this->getQuery($where);
			}
		}
    	$sqlField = [];
    	//使用group by进行分组查询
    	foreach ($fieldForm as $key => $value) {
    		$sqlField[] = "max(case `field_id` when ".$value['id']." then value else NULL end) '".$value['name']."'";
    	}
        
        if(empty($sqlField)){
            $sqlStr = "select `uid` as 'uid',`no_data_num` ".implode(',',$sqlField)."from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid`  $quertSql";
        }else{
            $sqlStr = "select `uid` as 'uid',`no_data_num`, ".implode(',',$sqlField)."from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid`   $quertSql";
        }
		$connection = Yii::$app->db;
		$command    = $connection->createCommand($sqlStr);
		$entityList = $command->queryAll();
		return $entityList;
    }
    
    
    
    
    /**
     * 获取实体（用户）列表
     * @param  object  $where  查询条件
     * @param  array  $fieldType    字段类型 默认全部
     * @return array         实体列表
     */
    public function getEntity3($where=null,$fieldType=[]){
    	if($fieldType){
    		$this->setFieldModelType($fieldType);
    	}
    	
		$fieldForm = $this->getExtensionField();	
		$quertSql  = '';
		if($where){
			if(count($where->where) > 0){
				$quertSql =' having  '.$this->getQuery($where);
			}else{
				$quertSql =' '.$this->getQuery($where);
			}
		}
    	$sqlField = [];
        if(!empty($fieldForm)){
            //使用group by进行分组查询
            foreach ($fieldForm as $key => $value) {
                if($value['type'] == 'checkbox' || $value['type'] == 'radio'){
                    $sqlField[] = "max(case `field_id` when ".$value['id']." then value_key else NULL end) '".$value['name']."'";
                }else{
                    $sqlField[] = "max(case `field_id` when ".$value['id']." then value else NULL end) '".$value['name']."'";
                }
            }
        }
        
        if(empty($sqlField)){
            $sqlStr = "select `uid` as 'id' ".implode(',',$sqlField)."from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid` ORDER BY `id` desc $quertSql";
        }else{
            $sqlStr = "select `uid` as 'id', ".implode(',',$sqlField)."from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid` ORDER BY `id` desc $quertSql";
        }
      //  echo $sqlStr;exit;
		$connection = Yii::$app->db;
		$command    = $connection->createCommand($sqlStr);
		$entityList = $command->queryAll();
		//print_r($entityList);
               
		return $entityList;
    }
    
    /**
     * 获取实体（用户）列表
     * @param  object  $where  查询条件
     * @param  array  $fieldType    字段类型 默认全部
     * @return array         实体列表
     */
    public function getEntity4($where=null,$fieldType=[]){
    	if($fieldType){
    		$this->setFieldModelType($fieldType);
    	}
    	
		$fieldForm = $this->getExtensionField();	
		$quertSql  = '';
		if($where){
			if(count($where->where) > 0){
				$quertSql =' having  '.$this->getQuery($where);
			}else{
				$quertSql =' '.$this->getQuery($where);
			}
		}
    	$sqlField = [];
        if(!empty($fieldForm)){
            //使用group by进行分组查询
            foreach ($fieldForm as $key => $value) {
                if($value['type'] == 'checkbox' || $value['type'] == 'radio'){
                    $sqlField[] = "max(case `field_id` when ".$value['id']." then value_key else NULL end) '".$value['name']."'";
                }else{
                    $sqlField[] = "max(case `field_id` when ".$value['id']." then value else NULL end) '".$value['name']."'";
                }
            }
        }
        
        if(empty($sqlField)){
            $sqlStr = "select `uid` as 'id' ".implode(',',$sqlField)."from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid`  $quertSql";
        }else{
            $sqlStr = "select `uid` as 'id', ".implode(',',$sqlField)."from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid` $quertSql";
        }
		$connection = Yii::$app->db;
		$command    = $connection->createCommand($sqlStr);
		$entityList = $command->queryAll();
		//print_r($entityList);
               
		return $entityList;
    }
    
    /**
     * 获取实体（用户）列表特殊
     * @param  object  $where  查询条件
     * @param  array  $fieldType    字段类型 默认全部
     * @return array         实体列表
     */
    public function getEntitySpecial($where=null,$fieldType=[],$where1=[]){
    	if($fieldType){
    		$this->setFieldModelType($fieldType);
    	}
    	
        $fieldForm = $this->getExtensionField();	
        $quertSql  = '';
        $quertTime = '';
        $quertUid  = '';
        $users     = [];
        if($where){
            if(count($where->where) > 0){
                $quertSql =' having  '.$this->getQuery($where);
            }else{
                $quertSql =' '.$this->getQuery($where);
            }
        }
        if($where1){
            if(!empty($where1['create_time'])){
                $create_time = strtotime($where1['create_time']);
                if($create_time){
                    $quertTime  = ' and create_time = '.$create_time;
                }
            }
            if(!empty($where1['user_name'])){
                $users = [];
                $FormRelevancy = FormRelevancy::find()->where(['like','name',$where1['user_name']])->asarray()->all();
                if($FormRelevancy){
                    foreach($FormRelevancy as $k=>$v){
                        $users[] =  $v['id'];
                    }
                    if($users){
                        $users = implode(',', $users);
                        $quertUid  = ' and `uid` in ('.$users.')';
                    }else{
                        $quertUid  = ' and `uid` = -1`';
                    }
                }else{
                    $quertUid  = ' and `uid` = -1';
                }
            }
        }
    	$sqlField = [];
    	//使用group by进行分组查询
    	foreach ($fieldForm as $key => $value) {
    		$sqlField[] = "max(case `field_id` when ".$value['id']." then value else NULL end) '".$value['name']."'";
    	}
        
        if(empty($sqlField)){
            $sqlStr = "select `uid` as 'uid'".implode(',',$sqlField).",`value_key`,`create_time`  from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module." $quertTime $quertUid )) group by `uid`  $quertSql";
        }else{
            $sqlStr = "select `uid` as 'uid',".implode(',',$sqlField).",`value_key`,`create_time`  from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module." $quertTime $quertUid )) group by `uid`   $quertSql";
        }
		$connection = Yii::$app->db;
		$command    = $connection->createCommand($sqlStr);
		$entityList = $command->queryAll();
                
                foreach($entityList as $key=>$val){
                    $entityList[$key]['uid'] = ApiMask::encrypt($val['uid']);
                    $entityList[$key]['value_key'] = FormRelevancy::find()->where(['id'=>$val['uid']])->select('name')->scalar();
                    $entityList[$key]['create_time']  = date('Y-m-d H:i:s',$val['create_time']);
                }
		return $entityList;
    }
    
    
    
     /**
     * 获取实体（用户）列表
     * @param  object  $where  查询条件
     * @param  array  $fieldType    字段类型 默认全部
     * @return array         实体列表
     */
    public function getEntitylist($where=null,$fieldType=[]){
    	if($fieldType){
    		$this->setFieldModelType($fieldType);
    	}
    	
		$fieldForm = $this->getExtensionField();	
		$quertSql  = '';
		if($where){
			if(count($where->where) > 0){
				$quertSql =' having  '.$this->getQuery($where);
			}else{
				$quertSql =' '.$this->getQuery($where);
			}
		}
    	$sqlField = [];
       
    	//使用group by进行分组查询
    	foreach ($fieldForm as $key => $value) {
           //     if($value['list_show']==1){
                    $sqlField[] = "max(case `field_id` when ".$value['id']." then value else NULL end) '".$value['name']."'";
           //     }
//                if($value['list_show']==3){
//                    
//                    $sqlField[] = "max(case `field_id` when ".$value['id']." then value else NULL end) '".$value['hint']."'";
//                }
    	}
        
        if(empty($sqlField)){
            $sqlStr = "select `uid` as 'uid'".implode(',',$sqlField)."from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid` ORDER BY `create_time` desc $quertSql";
        }else{
            $sqlStr = "select `uid` as 'uid',".implode(',',$sqlField)."from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid` ORDER BY `create_time` desc $quertSql";
        }
        
        $connection = Yii::$app->db;
        $command    = $connection->createCommand($sqlStr);
        $entityList = $command->queryAll();
       // print_r($entityList);exit;
        foreach($entityList as $key=>$val){
            foreach($val as $k=>$v){
                
                if($k != 'uid'){
                    $Form =FormExtensionField::find()->where(['name'=>$k,'companyid'=>$this->companyid,'module'=>$this->module])->asArray()->one();
                   
                    if($Form && $Form['list_show'] == 3){
                        
                        $entityList[$key][$k] = $Form['hint'];
                    }
                }
            }
            $entityList[$key]['uid'] = ApiMask::encrypt($val['uid']);
        }
        return $entityList;
    }
    
    
      /**
     * 获取一个用户实体
     * @param  string $uid    实体（用户）id
     * @param  array  $fieldType    字段类型 默认全部
     * @param  bool  $showKey   可选择类型的属性 false返回属性值，true返回键值 
     * @return object         返回实体对象
     */
    public function getEntityModellist($uid,$fieldType=[],$showKey=false){
    	if(count($fieldType)){
    		$this->setFieldModelType($fieldType);
    	}
    	//创建一个ActiveRecord ，使用yii封装的查询语句
    	$query = $this->query();
        //$query->Where(['module'=> $module,'companyid'=> $companyid]);
    	$query->andFilterWhere(['uid'=>$uid]);
       
       // var_dump($query);EXIT;
    	//查询用户信息
    	$userInfo = $this->getEntity($query);
    //    var_dump($userInfo);exit;
       	$fields = $this->getExtensionField();
		$required        = [];
		$integer         = [];
		$max             = [];
		$number          = [];
		$attributeLabels = [];
		$attributeValue  = [];
		foreach ($fields as $key => $value) {
			if($value['required'] ){
				$required[] = $value['name'];
			}
			switch ($value['type'])
			{
			case 'integer':
			  $integer[] = $value['name'];
			  break;
			case 'number':
			  $number[] = $value['name'];
			  break;
			case 'date':
			  break;
			default:
                        if($value['max']){
                                 $max[$value['max']][] = $value['name'];
                        }
			}
			$k = $value['name'];
                        $k1 = $value['hint'];
                       // echo $k1;exit;
                        if($value['list_show'] == 1){
                            $attributeLabels["$k"] = $value['label'];
                            $attributeValue["$k"] = isset($userInfo[0]["$k"])?$userInfo[0]["$k"]:"";
                        }
                        if($value['list_show'] == 3){
                            $attributeLabels["$k"] = $value['label'];
                            $attributeValue["$k"] =  $value['hint'];
                        }
			
		}
		$formRules[] = [$required,'required']; 
		$formRules[] = [$integer,'integer']; 
		$formRules[] = [$number,'number']; 
		foreach ($max as $key => $value) {
			$formRules[] = [$value,'string','max'=>$key];
		}
		// 构造一个实体对象
		$model = new FormClass($formRules,$attributeLabels,$attributeValue);
		$model->uid    = $uid;
		$model->companyid = $this->companyid;
		$model->module = $this->module;
		if($showKey){
			$model = $this->valueKey($model);
		}
		return $model;
    }
    
    
    
    
    /**
     * 获取实体（用户）数量
     * @param  object  $where  查询条件
     * @param  array  $fieldType    字段类型 默认全部
     * @return array         实体列表
     */
    public function getEntityCount($where=null,$fieldType=[]){
    	if($fieldType){
    		$this->setFieldModelType($fieldType);
    	}
        $fieldForm = $this->getExtensionField();	
        $quertSql  = '';
        if($where){
            if(count($where->where) > 0){
                $quertSql =' having  '.$this->getQuery($where);
            }else{
                $quertSql =' '.$this->getQuery($where);
            }
        }
    	$sqlField = [];
    	//使用group by进行分组查询
       // var_dump($fieldForm);exit;
    	foreach ($fieldForm as $key => $value) {
            $sqlField[] = "max(case `field_id` when ".$value['id']." then value else NULL end) '".$value['name']."'";
    	}
        if(empty($sqlField)){
           $sqlStr = "select count(t.uid) from (select `uid` as 'uid'".implode(',',$sqlField)." from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid` $quertSql) t";
        }else{
            $sqlStr = "select count(t.uid) from (select `uid` as 'uid',".implode(',',$sqlField)." from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module.")) group by `uid` $quertSql) t";
        }
//echo $sqlStr;exit;
        $connection = Yii::$app->db;
        $command    = $connection->createCommand($sqlStr);
        $count = $command->queryAll();
       // print_r($count[0]['count(t.uid)']);exit;

        return $count[0]['count(t.uid)'];

    }
    
    /**
     * 获取特殊实体（用户）数量
     * @param  object  $where  查询条件
     * @param  array  $fieldType    字段类型 默认全部
     * @return array         实体列表
     */
    public function getEntitySpecialCount($where=null,$fieldType=[],$where1=[]){
    	if($fieldType){
    		$this->setFieldModelType($fieldType);
    	}
        $fieldForm = $this->getExtensionField();	
        $quertSql  = '';
        $quertTime = '';
        $quertUid  = '';
        $users     = [];
        if($where){
            if(count($where->where) > 0){
                $quertSql =' having  '.$this->getQuery($where);
            }else{
                $quertSql =' '.$this->getQuery($where);
            }
        }
        if($where1){
            if(!empty($where1['create_time'])){
                $create_time = strtotime($where1['create_time']);
                if($create_time){
                    $quertTime  = ' and create_time = '.$create_time;
                }
            }
            if(!empty($where1['user_name'])){
                $users = [];
                $FormRelevancy = FormRelevancy::find()->where(['like','name',$where1['user_name']])->asarray()->all();
                if($FormRelevancy){
                    foreach($FormRelevancy as $k=>$v){
                        $users[] =  $v['id'];
                    }
                    if($users){
                        $users = implode(',', $users);
                        $quertUid  = ' and `uid` in ('.$users.')';
                    }else{
                        $quertUid  = ' and `uid` = -1';
                    }
                }else{
                    $quertUid  = ' and `uid` = -1';
                }
            }
        }
        
    	$sqlField = [];
    	//使用group by进行分组查询
       // var_dump($fieldForm);exit;
    	foreach ($fieldForm as $key => $value) {
            $sqlField[] = "max(case `field_id` when ".$value['id']." then value else NULL end) '".$value['name']."'";
    	}
        if(empty($sqlField)){
           $sqlStr = "select count(t.uid) from (select `uid` as 'uid'".implode(',',$sqlField)." from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module." $quertTime $quertUid)) group by `uid` $quertSql) t";
        }else{
            $sqlStr = "select count(t.uid) from (select `uid` as 'uid',".implode(',',$sqlField)." from `form_extension_metadata` where (`field_id` in (select id from `form_extension_field` where companyid=".$this->companyid." and module=".$this->module." $quertTime $quertUid)) group by `uid` $quertSql) t";
        }
//echo $sqlStr;exit;
        $connection = Yii::$app->db;
        $command    = $connection->createCommand($sqlStr);
        $count = $command->queryAll();
       // print_r($count[0]['count(t.uid)']);exit;

        return $count[0]['count(t.uid)'];

    }
    
    

    /**
     * 删除一个实体（用户）
     * @param  string $uid 用户id
     * @return int      1|0
     */
    public function deleteEntity($uid){
        if(FormExtensionMetadata::deleteAll(['uid' => $uid])){
        	return 1;
        }else{
        	return 0;
        }
    }

    /**
     * 获取一个用户实体
     * @param  string $uid    实体（用户）id
     * @param  array  $fieldType    字段类型 默认全部
     * @param  bool  $showKey   可选择类型的属性 false返回属性值，true返回键值 
     * @return object         返回实体对象
     */
    public function getEntityModel($uid,$fieldType=[],$showKey=false){
    	if(count($fieldType)){
    		$this->setFieldModelType($fieldType);
    	}
    	//创建一个ActiveRecord ，使用yii封装的查询语句
    	$query = $this->query();
        //$query->Where(['module'=> $module,'companyid'=> $companyid]);
    	$query->andFilterWhere(['uid'=>$uid]);
       
       // var_dump($query);EXIT;
    	//查询用户信息
    	$userInfo = $this->getEntity($query);
       	$fields = $this->getExtensionField();
		$required        = [];
		$integer         = [];
		$max             = [];
		$number          = [];
		$attributeLabels = [];
		$attributeValue  = [];
		foreach ($fields as $key => $value) {
			if($value['required'] ){
				$required[] = $value['name'];
			}
			switch ($value['type'])
			{
			case 'integer':
			  $integer[] = $value['name'];
			  break;
			case 'number':
			  $number[] = $value['name'];
			  break;
			case 'date':
			  break;
			default:
                        if($value['max']){
                                 $max[$value['max']][] = $value['name'];
                        }
			}
			$k = $value['name'];
			$attributeLabels["$k"] = $value['label'];
			$attributeValue["$k"] = isset($userInfo[0]["$k"])?$userInfo[0]["$k"]:"";
		}
		$formRules[] = [$required,'required']; 
		$formRules[] = [$integer,'integer']; 
		$formRules[] = [$number,'number']; 
		foreach ($max as $key => $value) {
			$formRules[] = [$value,'string','max'=>$key];
		}
		// 构造一个实体对象
		$model = new FormClass($formRules,$attributeLabels,$attributeValue);
		$model->uid    = $uid;
		$model->companyid = $this->companyid;
		$model->module = $this->module;
		if($showKey){
			$model = $this->valueKey($model);
		}
		return $model;
    }
    
    
    /**
     * 获取一个用户实体
     * @param  string $uid    实体（用户）id
     * @param  array  $fieldType    字段类型 默认全部
     * @param  bool  $showKey   可选择类型的属性 false返回属性值，true返回键值 
     * @return object         返回实体对象
     */
    public function getEntityModelSpecial($uid,$fieldType=[],$showKey=false){
    	if(count($fieldType)){
    		$this->setFieldModelType($fieldType);
    	}
    	//创建一个ActiveRecord ，使用yii封装的查询语句
    	$query = $this->query();
        //$query->Where(['module'=> $module,'companyid'=> $companyid]);
    	$query->andFilterWhere(['uid'=>$uid]);
       
       // var_dump($query);EXIT;
    	//查询用户信息
    	$userInfo = $this->getEntitySpecial($query);
       	$fields = $this->getExtensionField();
		$required        = [];
		$integer         = [];
		$max             = [];
		$number          = [];
		$attributeLabels = [];
		$attributeValue  = [];
		foreach ($fields as $key => $value) {
			if($value['required'] ){
				$required[] = $value['name'];
			}
			switch ($value['type'])
			{
			case 'integer':
			  $integer[] = $value['name'];
			  break;
			case 'number':
			  $number[] = $value['name'];
			  break;
			case 'date':
			  break;
			default:
                        if($value['max']){
                                 $max[$value['max']][] = $value['name'];
                        }
			}
			$k = $value['name'];
			$attributeLabels["$k"] = $value['label'];
			$attributeValue["$k"] = isset($userInfo[0]["$k"])?$userInfo[0]["$k"]:"";
		}
		$formRules[] = [$required,'required']; 
		$formRules[] = [$integer,'integer']; 
		$formRules[] = [$number,'number']; 
                
		foreach ($max as $key => $value) {
			$formRules[] = [$value,'string','max'=>$key];
		}
                $attributeLabels['user_name'] = '用户';
                $attributeLabels['create_time'] = '添加时间';

                $attributeValue['user_name'] = $userInfo[0]['value_key'];
                $attributeValue['create_time'] = $userInfo[0]['create_time'];
                
		// 构造一个实体对象
		$model = new FormClass($formRules,$attributeLabels,$attributeValue);
		$model->uid    = $uid;
		$model->companyid = $this->companyid;
		$model->module = $this->module;
		if($showKey){
			$model = $this->valueKey($model);
		}
		return $model;
    }
    
    
    
    
    /**
     * 将查询条件组成sql语句 
     * @param  array $where 查询条件
     * @return string        查询语句
     */
    public function getWhereSql($where){
    	$sql = '';
    	$fieldModel = $this->getFieldModel('name');
    	foreach ($where['where'] as $key => $value) {
    		if($value[1] == 'uid'){
    			$uidSql = '';
    			if(is_array($value[2])){
    				foreach ($value[2] as $uid) {
			    		$uidSql.= "or uid=".$uid;
    				}
    			}else{
			    	$uidSql.= "or uid=".$value[2];
			    }
    			$sql .= "and (".substr($uidSql,2).")";
    		}else{
    			$field_id = $fieldModel[$value[1]]['id'];
    			if($value[0] == '='){
	    			$sql .= "and (field_id=".$field_id." and value='".$value[2]."')";
    			}elseif($value[0] == 'like'){
	    			$sql .= "and (field_id=".$field_id." and value like '%".$value[2]."%')";
    			}
    		}
    	}
    	return substr($sql,3);
    }

    /**
     * 获取对象 ActiveRecord
     * @return object ActiveRecord对象
     */
	public function query()
	{
		return FormClass::find();
	}
	/**
	 * 获取ActiveRecord 查询语句
	 * @param  object $query ActiveRecord对象	
	 * @return string        sql语句
	 */
	public function getQuery($query){
	//	print_r($query);
    	$sql = $query->createCommand()->getRawSql();
    	$sql = str_replace("SELECT * FROM `form_extension_field`","",$sql);
    	$sql = str_replace("WHERE","",$sql);

    //	print_r($sql);
    	
    	return $sql;
	}
	/**
	 * 更新一个实体
	 * @param  string $uid  用户id
	 * @param  array $data 更新数据 
	 *                  [
						    'name' => '联系人2'
						    'sex' => '0'
						    'mobile' => '13933334444'
						    'uid' => '1456919557'
						]
	 * @return int       1 or 0
	 */
	public function updateEntity($uid,$data){
		$metadata = $this->getEntityMetadata($data);
		foreach ($metadata as $key => $value) {
                    $field = [];
                    $field = FormExtensionMetadata::findOne(["uid"=>$uid,"field_id"=>$key,"companyid"=>$this->companyid,"module"=>$this->module]);

                    if(empty($field)){
                        if(!empty($value['value'])){
                            $field1              = new FormExtensionMetadata();
                            $field1->uid         = (string)$uid;
                            $field1->field_id    = $key;
                            $field1->value       = !empty($value['value'])?(string)$value['value']:"";
                            $field1->value_key   = !empty($value['value_key'])?(string)$value['value_key']:'';
                            $field1->create_time = $value['create_time'];
                            $field1->module      = $this->module;
                            $field1->companyid      = $this->companyid;
                            $field1->save();
                        }
                    }else{
                        if($field->value != $value['value']){
                            $field->value     = (string)$value['value'];
                            $field->value_key = (string)$value['value_key'];
                            if(!$field->save()){
                                    return false;
                            }
                        }
                    } 
		}
		return true;
	}
	/**
	 * 将用户数据与自定义字段进行匹配
	 * @param  array $data 用户数据
	 * @return array       用户数据
	 */
	public function getEntityMetadata($data){
		$time = time();
    	foreach ($this->fieldModel as $key => $value) {
    		$dataKey = $value['name'];
    		if(!empty($data["$dataKey"])){
				$valueStr = null;
				$keyStr   = null;
				$tmp      = null;
    			if($value['type'] == 'checkbox'){
    				if($data["$dataKey"]){
                                    $checkboxsettings = [];
                                    $tmp = [];
                                    if(!empty($value['setting'])){
                                        $checkboxsetting = json_decode($value['setting']); 
                                        
                                        if(!empty($checkboxsetting)){
                                            foreach($checkboxsetting as $k=>$v){
                                                $checkboxsettings[$v->id] = $v->value;
                                            }
                                        }
                                    }
                                    if(!empty($checkboxsettings)){
                                        foreach ($data["$dataKey"] as $d) {
                                                $tmp[] = $checkboxsettings[$d];
                                        }
                                    }
                                    if(!empty($tmp)){
                                        $valueStr = implode(',', $tmp);
                                    }
		    			$keyStr = implode(',', $data["$dataKey"]);
    				}
                                
    			}elseif($value['type'] == 'radio'){
                            if($data["$dataKey"]){
    				if(!empty($value['setting'])){
                                    $radiosetting = json_decode($value['setting']); 
                                    if(!empty($radiosetting)){
                                        foreach($radiosetting as $k1=>$v1){
                                            if($v1->id == $data["$dataKey"]){
                                                $valueStr = $v1->value;
                                            }
                                        }
                                    }
                                }

                                $keyStr   = $data["$dataKey"];
                            }
    			}elseif($value['type'] == 'dropDownList'){
                            if($data["$dataKey"]){
    				$valueStr = explode(',', $value['setting'])[$data["$dataKey"]];
	    			$keyStr = $data["$dataKey"];
                            }
    			}else{
    				$valueStr = $data["$dataKey"];
    			}
    			$metadata[$value['id']] = ["uid"=>isset($data['uid'])?$data['uid']:'',
                                                    'field_id'    =>$value['id'],
                                                    'value'       =>$valueStr,
                                                    'value_key'   =>$keyStr,
                                                    'create_time' =>$time
                                    ];	
    		}else{
                    $valueStr = null;
                    $keyStr   = null;
                    $metadata[$value['id']] = ["uid"=>isset($data['uid'])?$data['uid']:'',
                                                    'field_id'    =>$value['id'],
                                                    'value'       =>$valueStr,
                                                    'value_key'   =>$keyStr,
                                                    'create_time' =>$time
                                    ];	
                }
    	}
    	return $metadata;
	}
	/**
	 * 用户属性中删除字段
	 * @param  int $field_id 字段id
	 * @return bool           
	 */
	public static function deleteField($field_id){
		if(FormExtensionMetadata::deleteAll('field_id = :field_id', [':field_id' => $field_id])){
			return true;
		}else{
			return false;	
		}
	}

	public function valueKey($model){
		foreach ($this->fieldModel as $key => $value) {
			if($value['type'] == "radio"){
                                $radiosetting = [];
                                $trans = [];
                                if(!empty($value['setting'])){
                                    $radiosetting = json_decode($value['setting']); 
                                    if(!empty($radiosetting)){
                                        foreach($radiosetting as $k=>$v){
                                            $trans[$v->id] = $v->value;
                                        }
                                    }
                                }
				$trans = array_flip($trans);
				$attr = $model->__get($value['name']);
                                
                                if($attr && !empty($trans[$attr])){
                                    $model->__set($value['name'],$trans[$attr]);
                                }else{
                                    $model->__set($value['name'],'');
                                }
			}
			if($value['type'] == 'checkbox'){
                                $checkboxsetting = [];
                                $trans = [];
                                if(!empty($value['setting'])){
                                    $checkboxsetting = json_decode($value['setting']); 
                                    if(!empty($checkboxsetting)){
                                        foreach($checkboxsetting as $k=>$v){
                                            $trans[$v->id] = $v->value;
                                        }
                                    }
                                }
                            
				$trans = array_flip($trans);
				$attr = $model->__get($value['name']);
                                if($attr){
                                    $attrValue =[];
                                    foreach (explode(',', $attr) as $k => $v) {
                                            $attrValue[] = $trans[$v];
                                    }
                                    $model->__set($value['name'],$attrValue);
                                }else{
                                    $model->__set($value['name'],'');
                                }
			}
		}
		return $model;
	}
        
        
        
        /**
	 * 更新一个实体
	 * @param  string $uid  用户id
	 * @param  array $data 更新数据 
	 *                  [
						    'name' => '联系人2'
						    'sex' => '0'
						    'mobile' => '13933334444'
						    'uid' => '1456919557'
						]
	 * @return int       1 or 0
	 */
	public function updateEntity1($uid,$data){
		$metadata = $this->getEntityMetadata1($data);
               // var_dump($metadata);exit;
		foreach ($metadata as $key => $value) {
                    $field = [];
                    $field = FormExtensionMetadata::findOne(["uid"=>$uid,"field_id"=>$key]);
                    if(empty($field)){
                        if(!empty($value['value'])){
                            $field1              = new FormExtensionMetadata();
                            $field1->uid         = (string)$uid;
                            $field1->field_id    = $key;
                            $field1->value       = !empty($value['value'])?(string)$value['value']:"";
                            $field1->value_key   = !empty($value['value_key'])?(string)$value['value_key']:'';
                            $field1->create_time = $value['create_time'];
                            $field1->module      = $this->module;
                            $field1->companyid      = $this->companyid;
                            $field1->save();
                        }
                    }else{
                        if($field->value != $value['value']){
                            $field->value     = (string)$value['value'];
                            $field->value_key = (string)$value['value_key'];
                            if(!$field->save()){
                                    return false;
                            }
                        }
                    } 
		}
		return true;
	}
	/**
	 * 将用户数据与自定义字段进行匹配
	 * @param  array $data 用户数据
	 * @return array       用户数据
	 */
	public function getEntityMetadata1($data){
            $time = time();
            $metadata = [];
            $sales = CustomerAssignService::belongs_sales();
            $C = Contract::ContPublic();//获取所属合同
            $contracts=!empty($C['contracts'])?$C['contracts']:[];
    	foreach ($this->fieldModel as $key => $value) {
    		$dataKey = $value['name'];
    		if(!empty($data["$dataKey"])){
                    $valueStr = null;
                    $keyStr   = null;
                    $tmp      = null;
                if($value['type'] == 'checkbox'){
                        if($data["$dataKey"]){
                            $res = explode(',',$data["$dataKey"]);
                            $checkboxsetting = [];
                            foreach ($res as $d) {
                                $checkboxsetting = json_decode($value['setting']); 
                                foreach($checkboxsetting as $k=>$v){
                                     if($v->value == $d){
                                          $tmp[] = $v->value;
                                          $tmp1[] = $v->id;
                                     }
                                }
                            }
                            $valueStr = !empty($tmp)?implode(',', $tmp):'';
                            $keyStr   = !empty($tmp1)?implode(',', $tmp1):'';
                        }
                }else if($value['type'] == 'radio' ){
                        if($data["$dataKey"]){
                                $radiosetting = [];
                                $radiosetting = json_decode($value['setting']); 
                                foreach($radiosetting as $k1=>$v1){
                                    if($v1->value == $data["$dataKey"]){
                                        $keyStr  = $v1->id;
                                        $valueStr = $v1->value;
                                    }
                                } 
                        }
                }else if($value['type'] == 'team_sales' ){
                        if($data["$dataKey"]){
                                $radiosetting = [];
                                $radiosetting = $sales; 
                                foreach($radiosetting as $k3=>$v3){
                                   
                                    if($v3 == $data["$dataKey"]){
                                        $keyStr  = $k3;
                                        $valueStr   = $k3;
                                    }
                                } 
                        }
                }else if($value['type'] == 'team_contract' ){
                        if($data["$dataKey"]){
                                $radiosetting = [];
                                $radiosetting = $contracts; 
                                foreach($radiosetting as $k4=>$v4){
                                    if($v4 == $data["$dataKey"]){
                                        $keyStr  = $k4;
                                        $valueStr   = $k4;
                                    }
                                } 
                        }
                }else if($value['type'] == 'relate_province'){
                    $tmp = '';
                    if($data["$dataKey"]){
                        if(preg_match('/省/',$data["$dataKey"])){
                            $data["$dataKey"] = substr($data["$dataKey"], 0, -3);
                        }
                         
                        $TRegion_province = TRegion::find()->where(['level'=>0])->andWhere(['like','name',$data["$dataKey"]])->one();
                        $tmp  = !empty($TRegion_province)?$TRegion_province->id:0;
                        $keyStr = $tmp;
                        $valueStr  = $tmp;
                    }
                }else if($value['type'] == 'relate_city'){
                    $tmp = '';
                    if($data["$dataKey"]){
                        if(preg_match('/市/',$data["$dataKey"])){
                            $data["$dataKey"] = substr($data["$dataKey"], 0, -3);
                        }
                        $TRegion_city = TRegion::find()->where(['level'=>1])->andWhere(['like','name',$data["$dataKey"]])->one();
                        $tmp  = !empty($TRegion_city)?$TRegion_city->id:0;
                        
                        $keyStr = $tmp;
                        $valueStr  = $tmp;
                    }    
                    
                }else if($value['type'] == 'relate_district'){
                    $tmp = '';
                    if($data["$dataKey"]){
                        if(preg_match('/市/',$data["$dataKey"])){
                            $data["$dataKey"] = substr($data["$dataKey"], 0, -3);
                        }
                        $TRegion_district = TRegion::find()->where(['level'=>2])->andWhere(['like','name',$data["$dataKey"]])->one();
                        $tmp  = !empty($TRegion_district)?$TRegion_district->id:0;
                        
                        $keyStr = $tmp;
                        $valueStr  = $tmp;
                    }          
                }else if($value['type'] == 'dropDownList'){
                    if($data["$dataKey"]){
                            $valueStr = explode(',', $value['setting'])[$data["$dataKey"]];
                            $keyStr   = $data["$dataKey"];
                    }
                }else{
                        $valueStr = $data["$dataKey"];
                }
    			$metadata[$value['id']] = ["uid"=>isset($data['uid'])?$data['uid']:'',
                                                    'field_id'    =>$value['id'],
                                                    'value'       =>$valueStr,
                                                    'value_key'   =>$keyStr,
                                                    'create_time' =>$time
                                    ];	
    		}else{
                    $valueStr = null;
                    $keyStr   = null;
                    $metadata[$value['id']] = ["uid"=>isset($data['uid'])?$data['uid']:'',
                                                    'field_id'    =>$value['id'],
                                                    'value'       =>$valueStr,
                                                    'value_key'   =>$keyStr,
                                                    'create_time' =>$time
                                    ];	
                }
    	}
    	return $metadata;
	}
        

        
        
         /**
	 * 添加一个实体
	 */
	public function addCustom($Relevancy_id,$custom,$companyid,$module){
            SessionService::_setFormCustomSession();
            $session = \Yii::$app->session;
            $sales = $session->get('_sales');
            $contracts = $session->get('_contracts');
            if(!empty($custom)){
                foreach($custom as $k=>$v){
                    $ExtensionField = new  FormExtensionField();
                    $FormExtensionMetadata = new FormExtensionMetadata ();
                    $Field=$ExtensionField->find()->where(['name'=>$k,'companyid' => $companyid,'module'=>$module,"status"=>1])->asArray()->one();
                    $value = $v;
                    if($v){
                        if($Field['type'] == 'radio'){
                            $radiosetting = json_decode($Field['setting']); 
                            if($radiosetting){
                                foreach($radiosetting as  $k1=>$v1){
                                    if($v1->value == $v){
                                        $FormExtensionMetadata->value_key = (string)$v1->id;
                                    }
                                }
                            }
                            $value = $v;
                        }
                        if($Field['type'] == 'team_sales'){
                            $value = '';
                            $radiosetting = $sales;
                            if($radiosetting){
                                foreach($radiosetting as  $k3=>$v3){
                                    if($v3 == $v){
                                        $FormExtensionMetadata->value_key = (string)$k3;
                                        $value = $k3;
                                    }
                                }
                            }

                        }
                        if($Field['type'] == 'team_contract'){
                           $value = '';
                           $radiosetting = $contracts;
                            if($radiosetting){
                                foreach($radiosetting as  $k4=>$v4){
                                    if($v4 == $v){
                                        $FormExtensionMetadata->value_key = (string)$k4;
                                        $value = $k4;
                                    }
                                }
                            }
                        }
                        if($Field['type'] == 'checkbox'){
                            
                            $tmp = array();
                            $c = explode(',', $v);
                            foreach ($c as  $k2=>$v2) {
                                $checkboxsetting = json_decode($Field['setting']); 
                                if($checkboxsetting){
                                     foreach ($checkboxsetting as  $k3=>$v3) {
                                         if($v2 == $v3->value){
                                             $tmp[] = $v3->id;
                                         }
                                     }
                                }
                            }
                            $value = $v;
                            $FormExtensionMetadata->value_key  = implode(',', $tmp);
                        }
                        if($Field['type'] == 'relate_province'){
                            $tmp = '';
                            if(!empty($v)){
                                if(preg_match('/省/',$v)){
                                    $v = substr($v, 0, -3);
                                }
                                $TRegion_province = TRegion::find()->where(['level'=>0])->andWhere(['like','name',$v])->one();
                                $tmp  = !empty($TRegion_province)?$TRegion_province->id:0;
                            }
                            $value = $tmp;
                            $FormExtensionMetadata->value_key  = $tmp;
                        }
                        if($Field['type'] == 'relate_city'){
                            $tmp = '';
                            if(!empty($v)){
                                if(preg_match('/市/',$v)){
                                    $v = substr($v, 0, -3);
                                }
                                $TRegion_city = TRegion::find()->where(['level'=>1])->andWhere(['like','name',$v])->one();
                               // $TCity = TCity::find()->where(['like','city_name',$v])->one();
                                $tmp  = !empty($TRegion_city)?$TRegion_city->id:0;

                            }
                            $value = $tmp;
                            $FormExtensionMetadata->value_key  = $tmp;
                           // var_dump($FormExtensionMetadata->value_key );exit;
                        }
                        if($Field['type'] == 'relate_district'){
                            $tmp = '';
                            if(!empty($v)){
                                $TRegion_district = TRegion::find()->where(['level'=>2])->andWhere(['like','name',$v])->one();
                                $tmp  = !empty($TRegion_district)?$TRegion_district->id:0;
                            }
                            $value = $tmp;
                            $FormExtensionMetadata->value_key  = $tmp;
                        }
                    }

                    $FormExtensionMetadata->uid = (string)$Relevancy_id;
                    $FormExtensionMetadata->field_id = $Field['id'];
                    $FormExtensionMetadata->value = (string)$value;
                    $FormExtensionMetadata->create_time = time();
                    $FormExtensionMetadata->module = $module;
                    $FormExtensionMetadata->companyid = $companyid;
                    $FormExtensionMetadata->no_data_num = !empty($custom->no_data_num)?$custom->no_data_num:'';
                    if (!$FormExtensionMetadata->save()) {
                         return array_values($FormExtensionMetadata->getFirstErrors())[0];
                    }
                }
            }
            
	}
        
        
        /**
	 * 显示一个实体
	 */
	public function infoCustom($Relevancy_id,$fields,$companyid,$module){
            $model1   = $this->getEntityModel($Relevancy_id);
            $attrValue = $model1->attrValue;
            $uid = $attrValue['uid'];
            $session = \Yii::$app->session;
            $provinces = $session->get('_provinces');
            $citys = $session->get('_citys');
            $districts = $session->get('_districts');
            $sales = $session->get('_sales');
            $contracts = $session->get('_contracts');
            $item_name = $session->get('_item_name');
          //  var_dump($attrValue);exit;
            if($model1){
                foreach($fields as $k=>$v){
                    $visible = 1;$only = 2;$permission = [];
                    if(!empty($item_name)){
                        if(!empty($v['permission_all'])){
                            $permission = @unserialize($v['permission_all']);
                        } 
                        if(!empty($item_name) && !empty($permission)  && is_array($permission)){
                            $visible = !empty ($permission[$item_name]['visible'])?$permission[$item_name]['visible']:2;
                            $only = !empty ($permission[$item_name]['only'])?$permission[$item_name]['only']:2;
                        }
                    }
                    if($attrValue){
                        foreach($attrValue as $k1=>$v1){
//                            if($v['type'] == 'upload'){
//                                unset($fields[$k]);
//                            }
                            
                            
                            if($k1 == $v['name'] && $v['type']!='partition'){
                                if($v['type'] == 'team_sales'){
                                    $salessettings = [];
                                    $salessetting = [];
                                    $s_setting = '';
                                    $salessetting = $sales;
                                    if(!empty($salessetting)){
                                        foreach($salessetting as $k4=>$v4){
                                            $salessettings[] = $v4;
                                        } 
                                    }
                                    if(!empty($salessettings)){
                                        $s_setting = implode(',', $salessettings);
                                    }
                                    $fields[$k]['setting'] = $s_setting;
                                }
                                if($v['type'] == 'team_contract'){
                                    $contractsettings = [];
                                    $contractsetting = [];
                                    $c_setting = '';
                                    $contractsetting = $contracts;
                                    if(!empty($contractsetting)){
                                        foreach($contractsetting as $k5=>$v5){
                                            $contractsettings[] = $v5;
                                        } 
                                    }
                                    if(!empty($contractsettings)){
                                        $c_setting = implode(',', $contractsettings);
                                    }
                                    $fields[$k]['setting'] = $c_setting;
                                }
                                if($v['type'] == 'radio'){
                                    $radiosettings = [];
                                    $radiosetting = [];
                                    $r_setting = '';
                                    if(!empty($v['setting'])){
                                        $radiosetting = json_decode($v['setting']); 
                                        foreach($radiosetting as $k2=>$v2){
                                            $radiosettings[] = $v2->value;
                                        }
                                        if(!empty($radiosettings)){
                                            $r_setting = implode(',', $radiosettings);
                                        }
                                        $fields[$k]['setting'] = $r_setting;
                                    } 
                                }
                                if($v['type'] == 'checkbox'){
                                     $checkboxsettings = [];
                                     $checkboxsetting = [];
                                     $c_setting = '';
                                     if(!empty($v['setting'])){
                                         $checkboxsetting = json_decode($v['setting']); 
                                         foreach($checkboxsetting as $k3=>$v3){
                                             $checkboxsettings[] = $v3->value;
                                         }
                                         if(!empty($checkboxsettings)){
                                             $c_setting = implode(',', $checkboxsettings);
                                         }
                                         $fields[$k]['setting'] = $c_setting;
                                     } 
                                }
                                if($v['type'] == 'team_sales'){
                                    $fields[$k]['value'] = !empty($sales[$v1])?$sales[$v1]:'';
                                    $fields[$k]['type'] = 'radio';
                                }else if($v['type'] == 'team_contract'){
                                    $fields[$k]['value'] = !empty($contracts[$v1])?$contracts[$v1]:'';
                                    $fields[$k]['type'] = 'radio';
                                }else if($v['type'] == 'relate_province'){
                                    $fields[$k]['value'] = !empty($provinces[$v1])?$provinces[$v1]:'';
                                    $fields[$k]['type'] = 'radio';
                                }else if($v['type'] == 'relate_city'){
                                    $fields[$k]['value'] = !empty($citys[$v1])?$citys[$v1]:'';
                                    $fields[$k]['type'] = 'radio';
                                }else if($v['type'] == 'relate_district'){
                                    $fields[$k]['value'] = !empty($districts[$v1])?$districts[$v1]:'';
                                    $fields[$k]['type'] = 'radio';
                                }else if($v['type'] == 'upload'){
                                    $Upload = Upload::find()->andWhere(['task_id'=>$uid,'modules_name'=>'form-custom'])->select('id,name,path,size,type,upload_time,thumb,modules_name')->asarray()->all();
                                    if(!empty($Upload)){
                                        foreach($Upload as $u_k=>$u_v){
                                            if(!empty($u_v['path'])){
                                                $Upload[$u_k]['path'] = Yii::$app->params['img_oss'].$u_v['path'];
                                                $Upload[$u_k]['size'] = round($u_v['size']/1024/1024,3);
                                            }
                                        }
                                    }
                                    $fields[$k]['value'] = !empty($Upload)?$Upload:[];
                                }else{
                                    $fields[$k]['value'] = $v1;
                                }
                            }
                        }
                    }
                   $fields[$k]['is_redact'] = $only;
                }
            }
            return $fields;
	}
        
        
        /**
	 * 显示列表
	 */
	public function listCustom($fields,$userList,$company_id,$id){
            $data = [];
          //  var_dump($userList);exit;
            if(!empty($userList)){
                foreach($userList  as $k=>$v){
                    $model1 = $this->infoCustomList($fields,$company_id,$id,$userList,$v['uid']);
                    $model2 = new FormRelevancy;
                    $FormRelevancy = $model2::findone($v['uid']);
                    $data[$k]['form_data'] = $FormRelevancy;
                    $data[$k]['custom'] = $model1;
                }
            }
            return $data;
	}
        
        
        
        /**
	 * 显示一个实体
	 */
	public function infoCustomList($fields,$companyid,$module,$userList,$Relevancy_id){
            $session = \Yii::$app->session;
            $provinces = $session->get('_provinces');
            $citys = $session->get('_citys');
            $districts = $session->get('_districts');
            $sales = $session->get('_sales');
            $contracts = $session->get('_contracts');
            $item_name = $session->get('_item_name');
            
            foreach($fields as $k=>$v){
                $visible = 1;$only = 2;$permission = [];
                if(!empty($item_name)){
                    if(!empty($v['permission_all'])){
                        $permission = @unserialize($v['permission_all']);
                    } 
                    if(!empty($item_name) && !empty($permission)  && is_array($permission)){
                        $visible = !empty ($permission[$item_name]['visible'])?$permission[$item_name]['visible']:2;
                        $only = !empty ($permission[$item_name]['only'])?$permission[$item_name]['only']:2;
                    }
                }
                if($userList){
                    foreach($userList as $userlist){
                        if($userlist['uid'] == $Relevancy_id){
                            foreach($userlist as $k1=>$v1){
                                if($k1 == $v['name'] && $v['type']!='partition' ){
                                    if($v['type'] == 'team_sales'){
                                        $salessettings = [];
                                        $salessetting = [];
                                        $s_setting = '';
                                        $salessetting = $sales;
                                        if(!empty($salessetting)){
                                            foreach($salessetting as $k4=>$v4){
                                                $salessettings[] = $v4;
                                            } 
                                        }
                                        if(!empty($salessettings)){
                                            $s_setting = implode(',', $salessettings);
                                        }
                                        $fields[$k]['setting'] = $s_setting;
                                        $fields[$k]['value'] = !empty($sales[$v1])?$sales[$v1]:'';
                                        $fields[$k]['type'] = 'radio';
                                    }else 
                                    if($v['type'] == 'team_contract'){
                                        $contractsettings = [];
                                        $contractsetting = [];
                                        $c_setting = '';
                                        $contractsetting = $contracts;
                                        if(!empty($contractsetting)){
                                            foreach($contractsetting as $k5=>$v5){
                                                $contractsettings[] = $v5;
                                            } 
                                        }
                                        if(!empty($contractsettings)){
                                            $c_setting = implode(',', $contractsettings);
                                        }
                                        $fields[$k]['setting'] = $c_setting;
                                        $fields[$k]['value'] = !empty($contracts[$v1])?$contracts[$v1]:'';
                                        $fields[$k]['type'] = 'radio';
                                    }else 
                                    if($v['type'] == 'radio'){
                                        $radiosettings = [];
                                        $radiosetting = [];
                                        $r_setting = '';
                                        if(!empty($v['setting'])){
                                            $radiosetting = json_decode($v['setting']); 
                                            foreach($radiosetting as $k2=>$v2){
                                                if($v1 == $v2->value){
                                                    $fields[$k]['value'] = $v2->value;
                                                } 

                                                $radiosettings[] = $v2->value;
                                            }
                                            if(!empty($radiosettings)){
                                                $r_setting = implode(',', $radiosettings);
                                            }

                                            $fields[$k]['setting'] = $r_setting;
                                        } 
                                    }else 
                                    if($v['type'] == 'checkbox'){
                                         $checkboxsettings = [];
                                         $checkboxsetting = [];
                                         $c_setting = '';
                                         $vvs = [];
                                         $vvv = '';
                                         if(!empty($v['setting'])){
                                             $checkboxsetting = json_decode($v['setting']); 
                                             if(!empty($v1)){
                                                 $v1 = explode(',', $v1);

                                                foreach($checkboxsetting as $k3=>$v3){
                                                    foreach($v1 as $v_k=>$v_v){
                                                        if($v_v == $v3->value){
                                                           $vvs[] = $v3->value;
                                                        }
                                                    }
                                                    $checkboxsettings[] = $v3->value;
                                                }
                                             }

                                             if(!empty($checkboxsettings)){
                                                 $c_setting = implode(',', $checkboxsettings);
                                             }
                                             if(!empty($vvs)){
                                                 $vvv = implode(',', $vvs);
                                             }
                                             $fields[$k]['setting'] = $c_setting;
                                             $fields[$k]['value']   = $vvv;
                                         } 
                                    }else if($v['type'] == 'relate_province'){
                                        $fields[$k]['value'] = !empty($provinces[$v1])?$provinces[$v1]:'';
                                        $fields[$k]['type'] = 'radio';
                                    }else if($v['type'] == 'relate_city'){
                                        $fields[$k]['value'] = !empty($citys[$v1])?$citys[$v1]:'';
                                        $fields[$k]['type'] = 'radio';
                                    }else if($v['type'] == 'relate_district'){
                                        $fields[$k]['value'] = !empty($districts[$v1])?$districts[$v1]:'';
                                        $fields[$k]['type'] = 'radio';
                                    }else{
                                        
                                        $fields[$k]['value'] = !empty($v1)?$v1:'';
                                    }
                                }
                            }
                        }
                            
                    }
                }
               $fields[$k]['is_redact'] = $only;
            }
            return $fields;
	}
        
        
         /**
	 * 获取序号
	 */
	public function getdata_num($FormCustom){
            
            $data = '';
            if(!empty($FormCustom)  && $FormCustom->no_status == 1){
                if(!empty($FormCustom->no_rule)){
                    $nos = json_decode($FormCustom->no_rule);
                }

                $no_date = !empty($nos->no_date)?$nos->no_date:1;
                $no_digits = !empty($nos->no_digits)?$nos->no_digits:5;
                $no_period = !empty($nos->no_period)?$nos->no_period:1;
                $no_preposition = !empty($nos->no_preposition)?$nos->no_preposition:'';
                $no_postposition = !empty($nos->no_postposition)?$nos->no_postposition:'';     
                
                if(!empty($FormCustom->no_data_num)){
                    $data = self::addnocreate($no_date,$no_digits,$no_preposition,$no_postposition,$no_period,$FormCustom->no_data_num);
                }else{
                    $data = self::nocreate($no_date,$no_digits,$no_preposition,$no_postposition,$no_period);
                }
            }
            return $data;
	}
     
        
    //生成新的序号                    
    public static function nocreate($no_date,$no_digits,$no_preposition,$no_postposition,$no_period)
    {
        $no_dates = '';
        $digits = '';
        $data = '';
        if($no_date == 1){
            $no_dates = date('Y',time());
        }
        if($no_date == 2){
            $no_dates = date('Ym',time());
        }
        if($no_date == 3){
            $no_dates = date('Ymd',time());
        }

        if($no_digits){
            $digits=sprintf("%0".$no_digits."d", 1);
        }
        $no_preposition  = !empty($no_preposition)?$no_preposition:'';
        $no_postposition = !empty($no_postposition)?$no_postposition:'';
        $data = $no_preposition.$no_dates.$digits.$no_postposition;
       
        return $data;
    }
    
    //生成新的序号+                    
    public static function addnocreate($no_date,$no_digits,$no_preposition,$no_postposition,$no_period,$no_data_num)
    {
        $num = 0;
        $no_data_num = self::intercept_str($no_preposition,$no_postposition,$no_data_num);
        $date = '';
      //  var_dump($no_period);exit;
        if($no_period == 1){
            $date = date('Y',time());
        }
        if($no_period == 2){
            $date = date('Ym',time());
        }
        if($no_period == 3){
            $date = date('Ymd',time());
        }
       // $no_data_num = '2016033000001';
        if(!empty($date)){
            if(preg_match("/".$date."/",$no_data_num)){
                if($no_data_num){
                    $no_data_num=$no_data_num+1;
                    $data = $no_preposition.$no_data_num.$no_postposition;
                }
            }else{
                $data = self::nocreate($no_date,$no_digits,$no_preposition,$no_postposition,$no_period);
            }
        }
        
        return $data;
    }
    
    
    public static function intercept_str($start,$end,$str)
    {
        if(!empty($start) && !empty($end)){
            $strarr=explode($start,$str);
            $str=$strarr[1];
            $strarr=explode($end,$str);
            $data = $strarr[0];
        }
        
        if(!empty($start) && empty($end)){
            $strarr=explode($start,$str);
            $data = $strarr[1];
        }
        
        if(empty($start) && !empty($end)){
            $strarr=explode($end,$str);
            $data = $strarr[0];
        }
        
        if(empty($start) && empty($end)){
            
            $data = $str;
        }
        
        return $data;
    }
        
    
     /**
        * 获取序号
        */
       public function getintercept_str($FormCustom){

           $data = '';
           if(!empty($FormCustom) && $FormCustom->no_status == 1){
               if(!empty($FormCustom->no_rule)){
                   $nos = json_decode($FormCustom->no_rule);
               }

               $no_date = !empty($nos->no_date)?$nos->no_date:1;
               $no_digits = !empty($nos->no_digits)?$nos->no_digits:5;
               $no_period = !empty($nos->no_period)?$nos->no_period:1;
               $no_preposition = !empty($nos->no_preposition)?$nos->no_preposition:'';
               $no_postposition = !empty($nos->no_postposition)?$nos->no_postposition:'';     

                if(!empty($FormCustom->no_data_num)){
                    $res = self::addnocreate($no_date,$no_digits,$no_preposition,$no_postposition,$no_period,$FormCustom->no_data_num);
                }else{
                    $res = self::nocreate($no_date,$no_digits,$no_preposition,$no_postposition,$no_period);
                }

               $data = self::intercept_str($no_preposition,$no_postposition,$res);
           }
           return $data;
       }
       
       /**
        * 获取序号
        */
       public function addintercept_str($FormCustom,$no_data_num){

           $data = '';
           if(!empty($FormCustom) && $FormCustom->no_status == 1){
               if(!empty($FormCustom->no_rule)){
                   $nos = json_decode($FormCustom->no_rule);
               }

               $no_date = !empty($nos->no_date)?$nos->no_date:1;
               $no_digits = !empty($nos->no_digits)?$nos->no_digits:5;
               $no_period = !empty($nos->no_period)?$nos->no_period:1;
               $no_preposition = !empty($nos->no_preposition)?$nos->no_preposition:'';
               $no_postposition = !empty($nos->no_postposition)?$nos->no_postposition:'';     

               $data = $no_preposition.$no_data_num.$no_postposition;
           }
           return $data;
       }
       
       
       /**
	 * 显示列表
	 */
	public function getCustomList($fields,$userList){
            $data = [];
          // var_dump($userList);exit;
            if(!empty($userList) && !empty($fields) ){
                
                foreach($userList  as $userlist){
                    $userdata = [];
                    
                    foreach($fields  as $f_k=>$f_v){
                         foreach($userlist as $k1=>$v1){
                            if($k1 != 'uid' && $k1 == $f_v['name']){
                                $userdata[$k1]['id']    = !empty($userlist['uid'])?$userlist['uid']:'';
                                $userdata[$k1]['name']  = !empty($f_v['label'])?$f_v['label']:'';
                                $userdata[$k1]['value'] = $v1;
                            }  
                         }
                    }
                    $data[] = $userdata;
                } 
            }
            return $data;
	}
}
?>
