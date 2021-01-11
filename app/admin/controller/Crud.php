<?php
//  *+-----------------------------------------------------------------------
//  *                      .::::.
//  *                    .::::::::.            | Author: 丶长情
//  *                    :::::::::::           | Email: 582994784@qq.com
//  *                 ..:::::::::::'           | Datetime: 2020/09/30
//  *             '::::::::::::'               | Remarks: 自动api生成插件
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

use app\common\library\Menu;
use app\admin\model\MyCrud;
use think\Request;
use app\admin\validate\Applets as adminValidate;
use think\exception\PDOException;
use think\facade\Db;
use think\Exception;
use think\facade\View;

/**
 * Api自动生成
 *
 * @icon fa fa-circle-o
 */
class Crud extends Admin
{
    public $stubList = [];
    public $tableColumns;
    public $layuiAddonUsed = [];
    public $importFile = [];
    public function getList()
    {
        // 查询所有表
        $sql = "SELECT TABLE_NAME,TABLE_COMMENT FROM information_schema.TABLES WHERE table_schema='" . config('database.connections.mysql.database') . "'";
        $tables = Db::query($sql);
        $data = [];
        foreach ($tables as $k => $v) {
            $data[$k]['id'] = $k + 1;
            $name = explode('_', $v['TABLE_NAME']);
            array_shift($name);
            $data[$k]['name'] = implode('_', $name);
            $data[$k]['comment'] = $v['TABLE_COMMENT'];
        }
        return json([
            'code' => 0,
            'count' => count($tables),
            'data' => $data,
            'msg' => '查询成功'
        ]);
    }
    public function controlName($str, $ucwords = true)
    {
        $array = explode('_', $str);
        $result = $array[0];
        $len = count($array);
        if ($len > 1) {
            for ($i = 1; $i < $len; $i++) {
                $result .= ucfirst($array[$i]);
            }
        }
        if ($ucwords) {
            return ucwords($result);
        } else {
            return $result;
        }
    }

    public function getTableColumn($table)
    {
        if ($this->tableColumns) {
            return $this->tableColumns;
        }
        $this->tableColumns = Db::query('SHOW FULL FIELDS FROM ' . config('database.connections.mysql.prefix') . $table);
        $this->tableColumns = array_map('array_change_key_case', $this->tableColumns);
        return $this->tableColumns;
    }

    public function buildModel($table, $deep = 0)
    {
        $filedName = "../app/admin/model/" . $this->controlName($table) . ".php";
        $tableColumns = $this->getTableColumn($table);
        // 生成model
        $modelFile = fopen($filedName, "w");

        fwrite($modelFile, $this->getReplacedStub('model/body.stub', [
            'className' => $this->controlName($table),
            'scopeTpl'=>$this->buildScopeTpl($tableColumns),
            'filedNameAttrTpl' => $this->buildTableFiledNameAttrTpl($tableColumns),
        ]));
        fclose($modelFile);
    }
    public function buildScopeTpl($tableColumns)
    {
        $arr = [];
        foreach ($tableColumns as $elt => $item) {
            $demo = $item['field'];
            $s = explode('_', $item['field']);
            if (end($s) === 'ids') {
               
            } else if (endWith($demo, '_id')) {
               
            } else if (startWith($demo, 'set')) {
               
            } else if (startWith($demo, 'select')) {
              
            } else if (explode('(', $item['type'])[0] === 'enum') {
              
            } else if (endWith($demo, 'time') || endWith($demo, '_at')) {
                if(!isset($arr['daterange'])){
                    $arr['daterange'] = $this->getReplacedStub('model/scope/daterange.stub', [
                        ]);
                }
              
            } 
        }
        return implode(PHP_EOL,$arr);
    }
    public function buildSearchCode($table,$tableColumns)
    {
        $arr = [];
        $ifarr = [];
        foreach ($tableColumns as $elt => $item) {
            $demo = $item['field'];
            $s = explode('_', $item['field']);
            if (endWith($item['field'],'_ids')) {
                $ifarr[] ="
                    \${$item['field']} = \$this->request->param('{$item['field']}',null);
                    if(\${$item['field']}){
                        \$query->whereFindInSet('{$item['field']}',\${$item['field']}.'');
                    }
                    ";
            }else if (endWith($item['field'],'_id')) {
                $ifarr[] ="
                    \${$item['field']} = \$this->request->param('{$item['field']}',null);
                    if(\${$item['field']}){
                        \$query->where('{$item['field']}',\${$item['field']});
                    }
                    ";
            } else  if (end($s) === 'img' || end($s) === 'image' || end($s) === 'images' || end($s) === 'imgs') {
               
            }else if (explode('(', $item['type'])[0] === 'text' && endWith($item['field'], 'content')){

            } else  if (endWith($item['field'], 'city') && explode('(', $item['type'])[0] === 'varchar'){

            }else if (startWith($demo, 'select')) {
                $ifarr[] ="
                \${$item['field']} = \$this->request->param('{$item['field']}',null);
                if(\${$item['field']}){
                    \$query->where('{$item['field']}',\${$item['field']});
                }
                ";
            }else if (explode('(', $item['type'])[0] === 'enum') {
                $ifarr[] ="
                \${$item['field']} = \$this->request->param('{$item['field']}',null);
                if(\${$item['field']}){
                    \$query->where('{$item['field']}',\${$item['field']});
                }
                ";
            }else if (startWith($demo, 'set')) {
                $ifarr[] ="
                    \${$item['field']} = \$this->request->param('{$item['field']}',null);
                    if(\${$item['field']}){
                        \$query->whereFindInSet('{$item['field']}',\${$item['field']}.'');
                    }
                    ";
            }else if (endWith($demo, 'time') || endWith($demo, '_at')) {
                    $arr[] ="->dateRange('{$item['field']}',\$this->request->param('{$item['field']}',null))";
            } else{
                $ifarr[] ="
                    \${$item['field']} = \$this->request->param('{$item['field']}',null);
                    if(\${$item['field']}){
                        \$query->whereLike('{$item['field']}',\"%{\${$item['field']}}%\");
                    }
                    ";
               
            } 
        }
        return count($arr)>0?'->where(function($query){
            $query'.implode(PHP_EOL,$arr).';
            '.implode(PHP_EOL,$ifarr).'
        })':'';
    }
    public function buildTableFiledNameAttrTpl($tableColumns)
    {
        $str = '';
        foreach ($tableColumns as $elt => $item) {
            $demo = $item['field'];
            $s = explode('_', $item['field']);
            if (end($s) === 'ids') {
                $tableName = str_replace('_ids', '', $demo);

                $str .= $this->getReplacedStub('model/fieldNameAttr/ids.stub', [
                    'fieldName' => $this->controlName($demo, true),
                    'relation' => $this->controlName($demo, false),
                    'relationTable' => $tableName,
                    'fieldNameList' => $this->controlName($demo, false) . 'List',
                ]) . "\n";
            } else if (endWith($demo, '_id')) {
                $tableName = str_replace('_id', '', $demo);

                $str .= $this->getReplacedStub('model/fieldNameAttr/id.stub', [
                    'fieldName' => $this->controlName($demo, true),
                    'relation' => $this->controlName($demo, false),
                    'relationTable' => $tableName,
                    'fieldNameList' => $this->controlName($demo, false) . 'List',
                ]) . "\n";
            } else if (startWith($demo, 'set')) {
                $arr = explode(',', explode(':', $item['comment'])[1]);
                $data = [];
                foreach ($arr as $k => $v) {
                    $array = explode('=', $v);
                    $data[$array[0]] = $array[1];
                }
                $str .= $this->getReplacedStub('model/fieldNameAttr/set.stub', [
                    'fieldName' => $this->controlName($demo, true),
                    'otherFieldName' => $demo . '_name',
                    'data' => var_export($data, true)
                ]) . "\n";
            } else if (startWith($demo, 'select')) {
                $arr = explode(',', explode(':', $item['comment'])[1]);
                $data = [];
                foreach ($arr as $k => $v) {
                    $array = explode('=', $v);
                    $data[$array[0]] = $array[1];
                }
                $str .= $this->getReplacedStub('model/fieldNameAttr/select.stub', [
                    'fieldName' => $this->controlName($demo, true),
                    'otherFieldName' => $demo . '_name',
                    'data' => var_export($data, true)
                ]) . "\n";
            } else if (explode('(', $item['type'])[0] === 'enum') {
                $arr = explode(',', explode(':', $item['comment'])[1]);
                $data = [];
                foreach ($arr as $k => $v) {
                    $array = explode('=', $v);
                    $data[$array[0]] = $array[1];
                }
                $str .= $this->getReplacedStub('model/fieldNameAttr/radio.stub', [
                    'fieldName' => $this->controlName($demo, true),
                    'otherFieldName' => $demo . '_name',
                    'data' => var_export($data, true)
                ]) . "\n";
            } else if (endWith($demo, 'time') || endWith($demo, '_at')) {
                $str .= $this->getReplacedStub('model/fieldNameAttr/time.stub', [
                    'fieldName' => $this->controlName($demo, true),
                ]) . "\n";
            } else if (endWith($demo, 'imgs') || endWith($demo, 'images')) {
                $str .= $this->getReplacedStub('model/fieldNameAttr/imgs.stub', [
                    'fieldName' => $this->controlName($demo, true),
                    'delimiter' => '|',
                ]) . "\n";
            } else if (endWith($demo, 'img') || endWith($demo, 'image')) {
                $str .= $this->getReplacedStub('model/fieldNameAttr/img.stub', [
                    'fieldName' => $this->controlName($demo, true),
                    'delimiter' => '|',
                ]) . "\n";
            }
        }
        return $str;
    }
    protected function getReplacedStub($stubname, $data)
    {
        foreach ($data as $index => &$datum) {
            $datum = is_array($datum) ? '' : $datum;
        }
        unset($datum);
        $search = $replace = [];
        foreach ($data as $k => $v) {
            $search[] = "{%{$k}%}";
            $replace[] = $v;
        }
        $stubname = '../addons/crud/tpl/' . $stubname;
        if (isset($this->stubList[$stubname])) {
            $stub = $this->stubList[$stubname];
        } else {
            $this->stubList[$stubname] = $stub = file_get_contents($stubname);
        }
        $content = str_replace($search, $replace, $stub);
        return $content;
    }
    public function buildAddCode($tableColumns)
    {

        $str = "";
        $loadModel = [];
        foreach ($tableColumns as $elt => $item) {
            $demo = $item['field'];
            $s = explode('_', $item['field']);
            if (end($s) === 'ids' || endWith($demo, '_id')) {
                $fieldName = $this->controlName(str_replace(end($s) === 'ids' ? '_ids' : "_id", '', $demo), false) . 's';
                $className = $this->controlName(str_replace(end($s) === 'ids' ? '_ids' : "_id", '', $demo), true);
                $tableName = str_replace(end($s) === 'ids' ? '_ids' : "_id", '', $demo);
                if (!in_array($fieldName, $loadModel)) {
                    $str .= "\$$fieldName = \\think\\facade\\Db::name('$tableName')->field('id,name')->select();\n";
                    $str .= "View::assign('$fieldName',\$$fieldName);";
                    $loadModel[] = $fieldName;
                }
            }
        }
        return $str;
    }
    public function buildEditCode($tableColumns)
    {
        $str = "";
        $loadModel = [];
        foreach ($tableColumns as $elt => $item) {
            $demo = $item['field'];
            $s = explode('_', $item['field']);
            if (end($s) === 'ids' || endWith($demo, '_id')) {
                $fieldName = $this->controlName(str_replace(end($s) === 'ids' ? '_ids' : "_id", '', $demo), false) . 's';
                $className = $this->controlName(str_replace(end($s) === 'ids' ? '_ids' : "_id", '', $demo), true);
                $tableName = str_replace(end($s) === 'ids' ? '_ids' : "_id", '', $demo);
                if (!in_array($fieldName, $loadModel)) {
                    $str .= "\$$fieldName = \\think\\facade\\Db::name('$tableName')->field('id,name')->select();\n";
                    $str .= "View::assign('$fieldName',\$$fieldName);";
                    $loadModel[] = $fieldName;
                }
            }
        }
        return $str;
    }
    public function buildController($table)
    {
        $tableColumns = $this->getTableColumn($table);
        $relation = [];
        $controllerFile = fopen("../app/admin/controller/" . $this->controlName($table) . ".php", "w");

        fwrite($controllerFile, $this->getReplacedStub('controller/body.stub', [
            'className' => $this->controlName($table),
            'modelClassName' => '\\app\\admin\\model\\' . $this->controlName($table),
            'witchMethod' =>  $this->getsWitchMethod($table),
            'addViewCode' => $this->buildAddCode($tableColumns),
            'editViewCode' => $this->buildEditCode($tableColumns),
            'table' => $table,
            'action'=> $this->buildSearchCode($table,$tableColumns),
            'relations' => json_encode($relation),
            'functions'=>$this->buildIndexFunction($table),
        ]));
        fclose($controllerFile);
    }
    public function crud(Request $request)
    {

        try {
            $createMenuError = null;
            $table = $request->post('name');
            $sql = 'show table status';
            $tableList = Db::query($sql);
            $tableList = array_map('array_change_key_case', $tableList);

            $fix = "";
            foreach ($tableList as $key => $value) {
                if ($value['name'] === config('database.connections.mysql.prefix') . $table) {
                    $fix = $value['comment'];
                }
            }
            $menu = [
                [
                    'name' => 'admin/' . $table . '/index',
                    'title' => $fix . '管理',
                    'icon' => 'fa-list',
                    'remark' => '',
                    'ismenu' => 1,
                    'sublist' => [
                        ['name' => 'admin/' . $table . '/add', 'title' => '添加'],
                        ['name' => 'admin/' . $table . '/edit', 'title' => '编辑 '],
                        ['name' => 'admin/' . $table . '/del', 'title' => '删除']
                    ]
                ]
            ];
            try{
                //生成菜单
                Menu::create($menu);
            }catch(Exception $e){
                $createMenuError = "生成代码成功，已自动忽略菜单生成";
            }
            


            // 生成controller

            $this->buildController($table);
            $this->buildModel($table);


            //生成添加编辑的公用view
            $this->buildIndexView($table);


            //生成添加编辑的公用view
            $this->buildEditView($table);

            $this->success($createMenuError??"生成成功");
        } catch (Exception $e) {
            // $this->error($e->getMessage() . '/' . $e->getFile() . ':' . $e->getLine());
            $this->error("生成失败");
        }
    }
   
    public function buildSeachFormItem($label,$type,$name,$item){
        $html = "";
        switch($type){
            case 'input':
                $html = '<input type="text" name="'.$name.'" id="'.$name.'" placeholder="请输入" autocomplete="off" class="layui-input">';
                break;
            case 'switch':
                $html = '
                <select name="'.$name.'"  id="'.$name.'">
                    <option value=""></option>
                    <option value="on">开启</option>
                    <option value="off">关闭</option>
                </select>
                ';
                break;
            case 'comment-select':
                $arr = explode(',', explode(':', $item['comment'])[1]);
                $str = "";
                foreach ($arr as $k => $v) {
                    $array = explode('=', $v);
                    $str .= "<option  value=\"$array[0]\">$array[1]</option>";
                }
                $html = '
                <select name="'.$name.'"  id="'.$name.'">
                    <option value=""></option>
                    '.$str.'
                </select>
                ';
                break;
            case 'select':
                $html = '
                <select name="'.$name.'"  id="'.$name.'">
                    <option value=""></option>
                </select>
                ';
                break;
            case 'datepicker':
                $html = '<input type="text" id="'.$name.'" name="'.$name.'" placeholder="请输入" autocomplete="off" class="layui-input">';
                break;
        }
        return '
        <div class="layui-inline">
            <label class="layui-form-label">'.$label.'</label>
            <div class="layui-input-inline">
            '.$html.'
            </div>
        </div>
      ';
    }
    public function getSearchFormHtml($table){
        $list = $this->getTableColumn($table);
        $arr = [];
        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            try {
                if ($item['field'] === Db::name("$table")->getPk()) {
                    $arr[] = $this->buildSeachFormItem(explode(':', $item['comment'])[0],'input',$item['field'],$item);
                } else if (explode('(', $item['type'])[0] === 'enum' && $item['field'] === 'state') {
                    $arr[] = $this->buildSeachFormItem(explode(':', $item['comment'])[0],'comment-select',$item['field'],$item);
                } else if (explode('(', $item['type'])[0] === 'enum') {
                    $arr[] = $this->buildSeachFormItem(explode(':', $item['comment'])[0],'comment-select',$item['field'],$item);
                }else if((explode('(', $item['type'])[0] === 'text'||explode('(', $item['type'])[0] === 'varchar') && (end($s) === 'img' || end($s) === 'image' || end($s) === 'images' || end($s) === 'imgs')){
                    
                }  else if (endWith($item['field'], '_id')) {
                    $arr[] = $this->buildSeachFormItem(explode(':', $item['comment'])[0],'select',$item['field'],$item);
                } else if (endWith($item['field'], '_ids')) {
                    $arr[] = $this->buildSeachFormItem(explode(':', $item['comment'])[0],'select',$item['field'],$item);
                }else if (endWith($item['field'],'switch')){
                    $arr[] = $this->buildSeachFormItem(explode(':', $item['comment'])[0],'switch',$item['field'],$item);
                } else if (explode('(', $item['type'])[0] === 'set') {
                    $arr[] = $this->buildSeachFormItem(explode(':', $item['comment'])[0],'comment-select',$item['field'],$item);
                } else if (explode('(', $item['type'])[0] === 'text' && endWith($item['field'], 'content')) {
                    continue;
                }else if (explode('(', $item['type'])[0] === 'int' && (end($s) === 'time' || end($s) === 'at' || endWith($item['field'], 'time'))) {
                    $arr[] = $this->buildSeachFormItem(explode(':', $item['comment'])[0],'datepicker',$item['field'],$item);
                } else if (explode('(', $item['type'])[0] === 'datetime') {
                    $arr[] = $this->buildSeachFormItem(explode(':', $item['comment'])[0],'datepicker',$item['field'],$item);
                } else if (endWith($item['field'], 'city') && explode('(', $item['type'])[0] === 'varchar') {
                } else{
                    $arr[] = $this->buildSeachFormItem(explode(':', $item['comment'])[0],'input',$item['field'],$item);
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        return implode(PHP_EOL,$arr);
    }
    public function buildIndexView($table)
    {
        // 生成view index
        $path = "../app/admin/view/" . $table;

        if (!file_exists($path))
            mkdir($path, 0777, true);

        $viewFile = fopen($path . "/" . "index.html", "w");
        $this->layuiAddonUsed = ['form', 'okLayer', 'okUtils'];
        fwrite($viewFile, $this->getReplacedStub('view/index.stub', [
            'table' => $table,
            'searchForm'=> $this->getSearchFormHtml($table),
            'tableCols' => $this->getViewFiledList($table),
            'modalWidth'=>'90%',
            'modalHeight'=>'90%',
            'layuiAddonUsed' => $this->getLayuiAddonUsed($table),
            'imageList'=>$this->getViewImgList($table),
            'formInit'=> $this->getViewIndexFormInit($table),
            'getListFunction'=> $this->getViewIndexGetListFunction($table),

        ]));
        $this->layuiAddonUsed = [];
        fclose($viewFile);
    }
    public function buildRelationListFunction($table){
        return $this->getReplacedStub('controller/relation/getList.stub', [
            'fieldName' => $this->controlName($table),
            'table'=>$table,

        ]);
    }
    public function buildIndexFunction($table){
        $list = $this->getTableColumn($table);
        $tables = [];
        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            if (endWith($item['field'],'_ids')||endWith($item['field'],'_id')) {
                $tableName = str_replace(endWith($item['field'],'_ids') ? '_ids' : "_id", '', $item['field']);
                if(!array_key_exists($tableName,$tables)){
                    $tables[$tableName] = $this->buildRelationListFunction($tableName);
                }
               
            }
        }
        return implode(PHP_EOL, $tables);
    }

    public function getViewIndexGetListFunction($table){
        $list = $this->getTableColumn($table);
        $arr = [];
        $list = $this->getTableColumn($table);
        $tables = [];
        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            if (endWith($item['field'],'_ids')||endWith($item['field'],'_id')) {
                $tableName = str_replace(endWith($item['field'],'_ids') ? '_ids' : "_id", '', $item['field']);
                $name = $this->controlName($tableName);
                if(!array_key_exists($tableName,$tables)){
                    $tables[$tableName] = $this->getReplacedStub('view/js/getList.stub', [
                        'fieldName' => $name,
                    ]);
                }

                if(!array_key_exists($item['field'],$tables)){
                    $tables[$item['field']] = "get{$name}List('{$item['field']}');";
                }
               
            }
        }
        return implode(PHP_EOL, $tables);
    }
    public function getViewIndexFormInit($table){
        $list = $this->getTableColumn($table);
        $arr = [];
        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            if (explode('(', $item['type'])[0] === 'datetime') {
                $this->addEditAddonUsed('laydate');
                $arr[] = "
                //渲染字段{$item['comment']}组件
                laydate.render({ 
                          elem: \"#" . $item['field'] . "\"
                          ,trigger:'click'
                          ,type: 'datetime'
                          ,range: true
                        });";
            } else if (explode('(', $item['type'])[0] === 'int' && (end($s) === 'time' || end($s) === 'at' || endWith($item['field'], 'time'))) {
                $this->addEditAddonUsed('laydate');
                $arr[] = "
                //=============渲染字段{$item['comment']}组件
                laydate.render({ 
                          elem: \"#" . $item['field'] . "\"
                          ,trigger:'click'
                          ,type: 'datetime'
                          ,range: true
                        });";
            } 
        }
        return implode(PHP_EOL, $arr);
    }
    public function buildEditView($table)
    {
        $path = "../app/admin/view/" . $table;
        if (!file_exists($path))
            mkdir($path, 0777, true);
        $viewFile = fopen($path . "/" . "edit.html", "w");
        $this->layuiAddonUsed = ['form', 'okLayer', 'okUtils'];
        $this->varInit = [];
        fwrite($viewFile, $this->getReplacedStub('view/edit.stub', [
            'table' => $table,
            'formHtml' => $this->getViewEditHtml($table),
            'extendAddons' => $this->getViewEditExtendAddons($table),
            'formInit' => $this->getEditAddonInitJs($table),
            'layuiAddonUsed' => $this->getLayuiAddonUsed($table),
            'varInit' => $this->getEditVarInitJs(),
            'importFile' => $this->getImportFile(),

        ]));
        $this->layuiAddonUsed = [];
        fclose($viewFile);
    }
    public function getImportFile()
    {
        $str = "";
        foreach ($this->importFile as $type => $files) {

            foreach ($files as $file) {
                if ($type == 'css') {
                    $str .= "<link href='$file' rel='stylesheet'/>" . PHP_EOL;
                } else if ($type == 'js') {
                    $str .= "<script src='$file' type='text/javascript'></script>" . PHP_EOL;
                }
            }
        }
        return $str;
    }
    public function addEditAddonUsed($addon)
    {
        if (!in_array($addon, $this->layuiAddonUsed)) {
            $this->layuiAddonUsed[] = $addon;
        }
    }
    public function getEditVarInitJs()
    {
        $str = "";
        foreach ($this->layuiAddonUsed as $value) {
            $str .= "let $value = layui.$value;" . PHP_EOL;
        }
        return $str;
    }
    public function import($type, $href)
    {
        if (!isset($this->importFile[$type])) {
            $this->importFile[$type] = [];
        }
        if (!in_array($href, $this->importFile[$type])) {
            $this->importFile[$type][] = $href;
        }
    }
    public function getEditAddonInitJs($table)
    {
        $list = $this->getTableColumn($table);
        $arr = [];
        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            if (explode('(', $item['type'])[0] === 'datetime') {
                $this->addEditAddonUsed('laydate');
                $arr[] = "
                //渲染字段{$item['comment']}组件
                laydate.render({ 
                          elem: \"#" . $item['field'] . "\"
                          ,trigger:'click'
                          ,type: 'datetime'
                        });";
            } else if (explode('(', $item['type'])[0] === 'int' && (end($s) === 'time' || end($s) === 'at' || endWith($item['field'], 'time'))) {
                $this->addEditAddonUsed('laydate');
                $arr[] = "
                //=============渲染字段{$item['comment']}组件
                laydate.render({ 
                          elem: \"#" . $item['field'] . "\"
                          ,trigger:'click'
                          ,type: 'datetime'
                        });";
            } else if (endWith($item['field'], 'city') && explode('(', $item['type'])[0] === 'varchar') {
                $this->import('js', '/static/lib/city-picker/city-picker.data.js');
                $this->import('css', '/static/lib/city-picker/city-picker.css');
                $this->addEditAddonUsed('citypicker');
                $filedName = $this->controlName($item['field'], false);
                $arr[] = "
                //===============渲染字段{$item['comment']}组件
                let $filedName =  new citypicker('#{$item['field']}', {
                    provincename:'provinceId',
                    cityname:'cityId',
                    districtname: 'districtId',
                    level: 'districtId',// 级别
                });
                $filedName.setValue('{\${$table}.{$item['field']}??\"\"}')";
            } else if (end($s) === 'ids') {
                $this->addEditAddonUsed('xmSelect');
                $filedName = $this->controlName(str_replace('_ids', '', $item['field']), false) . 's';
                $demo = $item['field'];
                $arr[] = "
                //==================渲染字段{$item['comment']}组件
                var data = JSON.parse('{:json_encode(\${$filedName})}');
                var ids = JSON.parse('[{:isset(\$$table)?\$" . $table . "[\"$demo\"]:null}]');
                var arr = [];
                for(var key in data){
                    arr[key] = {
                        'name':data[key].name,
                        'value':data[key].id,
                    }
                    if(ids.indexOf(data[key].id)!=-1){
                        arr[key]['selected'] = true;
                    }
                }
                var " . $demo . " = xmSelect.render({
                    el: '#" . $demo . "',
                    name: '" . $demo . "',
                    data:arr
                });
                ";
            }else if ((explode('(', $item['type'])[0] === 'json' || explode('(', $item['type'])[0] === 'text') && endWith($item['field'],'_fieldlist')) {
                $this->addEditAddonUsed('fieldList');
                $this->import('css', '/static/lib/field-list/field-list.css');
                $arr[] = "
                var {$item['field']} = fieldList.render({
                    el: '#{$item['field']}',
                    name: '{$item['field']}',
                });";
            } else if (explode('(', $item['type'])[0] === 'text' && endWith($item['field'], 'content')) {

                $this->addEditAddonUsed('layedit');
                $arr[] = "
                //创建{$item['field']}编辑器
                editorArr['{$item['field']}'] = layedit.build('{$item['field']}',{
                    uploadImage:{
                        url:\"{:url('admin/crud/upload')}\",
                        type:'post'
                    }
                });
                form.verify({
                    //content富文本域中的lay-verify值
                    '{$item['field']}': function(value) {
                        return layedit.sync(editorArr['{$item['field']}']);
                    }
                });";
            }
        }
        return implode(PHP_EOL, $arr);
    }
    public function getLayuiAddonUsed()
    {
        return json_encode($this->layuiAddonUsed);
    }
    public function getViewEditExtendAddons($table)
    {

        $list = $this->getTableColumn($table);
        $addons = [];

        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            if (end($s) === 'ids') {
                $addons['xmSelect'] = 'xm-select';
            }else if ((explode('(', $item['type'])[0] === 'json' || explode('(', $item['type'])[0] === 'text') && endWith($item['field'],'_fieldlist')) {
                $addons['fieldList'] = 'field-list/field-list';
            } else  if (endWith($item['field'], 'city') && explode('(', $item['type'])[0] === 'varchar') {
                $addons['citypicker'] = 'city-picker/city-picker';
            }
        }
        return json_encode($addons);
    }
    public function getViewFiledList($table)
    {
        $list = $this->getTableColumn($table);
        $str = "";

        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            if (endWith($item['field'],'switch') == 'switch') {
                $str .= "{field: '" . $item['field'] . "', title: '" . explode(':', $item['comment'])[0] . "',templet: function (d) {
        var state = \"\";
        if (d.switch == \"on\") {
            state = \"<input type='checkbox' value='\" + d.id + \"' id='switch' lay-filter='stat' checked='checked' name='switch'  lay-skin='switch' lay-text='开启|关闭' >\";
        } else {
            state = \"<input type='checkbox' value='\" + d.id + \"' id='switch' lay-filter='stat'  name='switch'  lay-skin='switch' lay-text='开启|关闭' >\";
        }
        return state;
    }}," . PHP_EOL;
            } else {

                if (end($s) === 'img' || end($s) === 'image' || end($s) === 'images' || end($s) === 'imgs') {
                    $str .= "{field: '" . $item['field'] . "', title: '" . explode(':', $item['comment'])[0] . "' , templet:'#logoTpl" . (strpos($item['field'], 's') != false ? 'More' : 'One') . "'}," . PHP_EOL;
                } else if (endWith($item['field'], '_id')) {
                    $str .= "{field: '" . $this->controlName($item['field'], false) . "', title: '" . explode(':', $item['comment'])[0] . "',templet: function (d) {return d." . ($this->controlName($item['field'], false) . 'List.name') . "} }," . PHP_EOL;
                } else if (end($s) === 'ids') {
                    $filedName = $this->controlName($item['field'], false) . 'List';
                    $str .= "{field: '" . $filedName . "', title: '" . explode(':', $item['comment'])[0] . "',templet: function (d) {
                        var data = d.{$filedName};
                        var arr = [];
                        for(var key in data){
                            arr.push(data[key].name);
                        }
                        return arr.join(',')
                    } }," . PHP_EOL;
                } else if (startWith($item['field'], 'set') || startWith($item['field'], 'select') || (explode('(', $item['type'])[0] === 'enum' && $item['field'] === 'state')) {
                    $str .= "{field: '" . $item['field'] . "_name', title: '" . explode(':', $item['comment'])[0] . "'}," . PHP_EOL;
                } else if (endWith($item['field'], 'city') && explode('(', $item['type'])[0] === 'varchar') {
                    $str .= "{field: '" . $item['field'] . "', title: '" . explode(':', $item['comment'])[0] . "'}," . PHP_EOL;
                } else {
                    $str .= "{field: '" . $item['field'] . "', title: '" . explode(':', $item['comment'])[0] . "'}," . PHP_EOL;
                }
            }
        }
        return $str;
    }
    public function getViewImgList($table)
    {
        $list = $this->getTableColumn($table);
        $str = "";
        $flag  = false;
        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            try {
                if (explode('(', $item['type'])[0] === 'varchar' && (end($s) === 'img' || end($s) === 'image')) {
                    $flag = true;
                    $str .= "<script type=\"text/html\" id=\"logoTplOne\">
                                <a href=\"javascript:amplificationImg('" . $item['comment'] . "','{{d." . $item['field'] . "}}')\">
                                <img src=\"{{d." . $item['field'] . "}}\" style=\"width: auto;height: 100%;\"/></a>
                            </script>";
                } else if (explode('(', $item['type'])[0] === 'text' && (end($s) === 'images' || end($s) === 'imgs')) {
                    $flag = true;
                    $str .= "<script type=\"text/html\" id=\"logoTplMore\">
                    {{# var img = d." . $item['field'] . "}}
                    {{# for(var i=0;i < img.length;i++){}}
                    <a href=\"javascript:amplificationImg('图片','{{img[i]}}')\">
                        <img src=\"{{img[i]}}\" style=\"width: auto;height: 100%;\"/>
                    </a>
                    {{# } }}
                </script>";
                }
            } catch (Exception $e) {
                return $str;
            }
        }
        if ($flag) {
            $str .= "<script type=\"text/javascript\">
            function amplificationImg(name, url) {
                let img = $(\"#ImgSrc\").attr(\"src\", url);
                layer.open({
                    type: 1,
                    title: false,
                    closeBtn: 0,
                    shadeClose: true,
                    area: ['70%', '70%'], //宽高
                    content: '<img style=\"display: inline-block; width: 100%; height: 100%;\" src=\"'+ url +'\">'
                });
            }
        </script>";
        }

        return $str;
    }
    public function getViewEditHtml($table)
    {
        $list = $this->getTableColumn($table);
        $str = "";
        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            try {
                if ($item['field'] === Db::name("$table")->getPk()) {
                    $str .= "
                    {if " . '$' . "" . $table . "." . $item['field'] . "??null}
                    <input type=\"hidden\" name=\"" . $item['field'] . "\" placeholder=\"\" autocomplete=\"off\" class=\"layui-input\" value=\"" . '{$' . "" . $table . "." . $item['field'] . "??''}\">
                    {/if}
                    ";
                } else if (explode('(', $item['type'])[0] === 'enum' && $item['field'] === 'state') {
                    $str .= "<div class=\"layui-form-item\">
                            <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
                            <div class=\"layui-input-block\">
                                " . $this->danxuanedit($table, $item['field'], $item) . "
                            </div>
                        </div>";
                } else if ((explode('(', $item['type'])[0] === 'json' || explode('(', $item['type'])[0] === 'text') && endWith($item['field'],'_fieldlist')) {
                    $str .= "<div class=\"layui-form-item\">
                            <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
                            <div class=\"layui-input-block\">
                                <textarea id=\"{$item['field']}\" name=\"{$item['field']}\"></textarea>
                            </div>
                        </div>";
                }else if (explode('(', $item['type'])[0] === 'enum') {
                    $str .= "        <div class=\"layui-form-item\">
            <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
            <div class=\"layui-input-block\">
                <select name=\"" . $item['field'] . "\" lay-verify=\"required\">
                    <option value=\"\"></option>
                    " . $this->xialaedit($table, $item['field'], $item) . "
                </select>
            </div>
        </div>";
                } else if ((explode('(', $item['type'])[0] === 'varchar' && (end($s) === 'img' || end($s) === 'image')) || (explode('(', $item['type'])[0] === 'text' && (end($s) === 'images' || end($s) === 'imgs'))) {

                    $fieldName = strpos($item['field'], 's') !== false ? ('{:implode(\'|\',$' . "" . $table . "." . $item['field'] . '??[])}') : ('{$' . "" . $table . "." . $item['field'] . '??null}');
                    $str .= "<div class=\"layui-form-item\">
                    <label class=\"layui-form-label\">" . $item['comment'] . "</label>
                      <div class=\"layui-input-block layui-upload\">
                        <input name=\"" . $item['field'] . "\" class=\"layui-input layui-col-xs6\" lay-verify=\"required\" placeholder=\"请上传图片\" value=\"" . $fieldName  . "\">
                        <div class=\"layui-upload-btn\" >
                            <span><a class=\"layui-btn\" data-upload=\"" . $item['field'] . "\" data-upload-number=\"" . (strpos($item['field'], 's') !== false ? 'more' : 'one') . "\" data-upload-exts=\"png|jpg|ico|jpeg\" data-upload-icon=\"image\"><i class=\"fa fa-upload\"></i> 上传</a></span>
                            <span><a class=\"layui-btn layui-btn-normal\" id=\"select_logo\" data-upload-select=\"" . $item['field'] . "\" data-upload-number=\"" . (strpos($item['field'], 's') === true ? 'more' : 'one') . "\" data-upload-mimetype=\"image/*\"><i class=\"fa fa-list\"></i> 选择</a></span>
                        </div>
                    </div>
                </div>";
                } else if (endWith($item['field'], '_id')) {
                    $filedName = $this->controlName(str_replace('_id', '', $item['field']), false) . 's';
                    $field  = $table . "." . $item['field'];
                    $str .= "  <div class=\"layui-form-item\">
            <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
            <div class=\"layui-input-block\">
                <select name=\"" . $item['field'] . "\" lay-verify=\"required\">
                    <option value=\"\"></option>
                    {foreach \${$filedName} as \$key=>\$vo } 
                        {if isset(\${$field}) && \${$field}==\$vo.id}}
                        <option value=\"{\$vo.id}\" selected>{\$vo.name}</option>
                        {else/}
                        <option value=\"{\$vo.id}\" >{\$vo.name}</option>
                        {/if}
                    {/foreach}
                </select>
            </div>
        </div>";
                } else if (endWith($item['field'], '_ids')) {
                    $filedName = $this->controlName(str_replace('_ids', '', $item['field']), false) . 's';
                    $str .= "  <div class=\"layui-form-item\">
            <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
            <div class=\"layui-input-block\">
            <div id=\"" . $item['field'] . "\" name=\"" . $item['field'] . "\"  ></div>
            </div>
        </div>";
                } else if (explode('(', $item['type'])[0] === 'set') {
                    $str .= "
<div class=\"layui-form-item\">
    <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
    <div class=\"layui-input-block\">
    " . $this->duoxuanedit($table, $item['field'], $item) . "
    </div>
</div>";
                } else if (explode('(', $item['type'])[0] === 'text' && endWith($item['field'], 'content')) {
                    $str .= " <div class=\"layui-form-item layui-form-text\">
    <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
    <div class=\"layui-input-block\">
      <textarea id=\"" . $item['field'] . "\" name=\"" . $item['field'] . "\" lay-verify=\"" . $item['field'] . "\" style=\"display: none;\">" . '{$' . "" . $table . "." . $item['field'] . "??''}</textarea>
    </div>
  </div>";
                } else if (endWith($item['field'],'switch')) {
                    $str .= "    <div class=\"layui-form-item\">
    <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
    <div class=\"layui-input-block\">
      <input type=\"checkbox\" name=\"" . $item['field'] . "\" lay-skin=\"switch\" lay-text=\"开启|关闭\" {if isset(\$$table) && $" . $table . "." . $item['field'] . " == 'on'}checked{/if}>
    </div>
  </div>";
                } else if (explode('(', $item['type'])[0] === 'int' && (end($s) === 'time' || end($s) === 'at' || endWith($item['field'], 'time'))) {
                    $fieldName = $table . "['{$item['field']}']";
                    $str .= "  <div class=\"layui-form-item\">
    <div class=\"layui-inline\">
      <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
      <div class=\"layui-input-block\">
        <input type=\"text\" name=\"" . $item['field'] . "\" id=\"" . $item['field'] . "\" autocomplete=\"off\" class=\"layui-input\" placeholder='yy-mm-dd HH:ii:ss' value=\"" . '{$' . "" . $table . "." . $item['field'] . "??''}\">
      </div>
    </div>
  </div>";
                } else if (explode('(', $item['type'])[0] === 'datetime') {
                    $str .= "  <div class=\"layui-form-item\">
    <div class=\"layui-inline\">
      <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
      <div class=\"layui-input-block\">
        <input type=\"text\" name=\"" . $item['field'] . "\" id=\"" . $item['field'] . "\" autocomplete=\"off\" class=\"layui-input\" placeholder='yy-mm-dd HH:ii:ss' value=\"" . '{$' . "" . $table . "." . $item['field'] . "??''}\">
      </div>
    </div>
  </div>";
                } else if (endWith($item['field'], 'city') && explode('(', $item['type'])[0] === 'varchar') {
                    $str .= "<div class=\"layui-form-item\">
                    <label class=\"layui-form-label\">" . $item['comment'] . "</label>
                    <div class=\"layui-input-block\">
                        <input type=\"text\" name=\"" . $item['field'] . "\" id=\"" . $item['field'] . "\" autocomplete=\"on\" placeholder=\"请输入" . $item['comment'] . "\"  class=\"layui-input\"
                        readonly=\"readonly\" data-toggle=\"city-picker\">
                    </div>
                </div>" . PHP_EOL;
                } else {
                    $str .= "<div class=\"layui-form-item\">
                    <label class=\"layui-form-label\">" . $item['comment'] . "</label>
                    <div class=\"layui-input-block\">
                        <input type=\"text\" name=\"" . $item['field'] . "\" placeholder=\"请输入" . $item['comment'] . "\" autocomplete=\"off\" class=\"layui-input\"
                               lay-verify=\"required\" value=\"" . '{$' . "" . $table . "." . $item['field'] . "??''}\">
                    </div>
                </div>";
                }
            } catch (Exception $e) {
                $str .= "<div class=\"layui-form-item\">
                    <label class=\"layui-form-label\">" . $item['comment'] . "</label>
                    <div class=\"layui-input-block\">

                        <input type=\"text\" name=\"" . $item['field'] . "\" placeholder=\"请输入" . $item['comment'] . "\" autocomplete=\"off\" class=\"layui-input\"
                               lay-verify=\"required\" value=\"" . '{$' . "" . $table . "." . $item['field'] . "??''}\">
                    </div>
                </div>" . $e->getMessage();
            }
        }
        return $str;
    }


    public  function wangEditor($field)
    {
        $str = "    
        layui.use('layedit', function(){
            var layedit = layui.layedit;
            layedit.build('" . $field . "'); //建立编辑器
        });";
        return $str;
    }



    public function danxuanedit($table, $filed, $item)
    {
        $arr = explode(',', explode(':', $item['comment'])[1]);
        $str = "";
        foreach ($arr as $k => $v) {
            $array = explode('=', $v);
            $str .= "<input type=\"radio\" name=\"" . $filed . "\" value=\"$array[0]\" title=\"$array[1]\" {if isset(\$$table) && $" . $table . "." . $filed . " == $array[0]}checked{/if}>";
        }
        return $str;
    }



    public function xialaedit($table, $filed, $item)
    {
        $arr = explode(',', explode(':', $item['comment'])[1]);
        $str = "";
        foreach ($arr as $k => $v) {
            $array = explode('=', $v);
            $str .= "<option {if \${$table}.{$filed}??null == $array[0]}selected=\"\"{/if} value=\"$array[0]\">$array[1]</option>";
        }
        return $str;
    }



    public function duoxuanedit($table, $filed, $item)
    {
        $arr = explode(',', explode(':', $item['comment'])[1]);
        $str = "";
        foreach ($arr as $k => $v) {
            $array = explode('=', $v);
            $str .= "<input {if isset(\$$table) && in_array('$array[0]',explode(',',\$$table.$filed))}checked=\"\"{/if} type=\"checkbox\" name=\"" . $filed . "[$array[0]" . "]\" title=\"$array[1]\">\n";
        }
        return $str;
    }
    public function getsWitchMethod($table)
    {
        $list = $this->getTableColumn($table);
        $str = "";
        foreach ($list as $key => $item) {
            if (endWith($item['field'],'switch') === 'switch') {
                $str .= "if (!isset(\$data['switch']))
                            \$data['switch'] = 'off';
                            ";
            }
        }
        return $str;
    }

    public function upload()
    {
        //{
        //    "code": 0 //0表示成功，其它失败
        //,"msg": "" //提示信息 //一般上传失败后返回
        //,"data": {
        //    "src": "图片路径"
        //,"title": "图片名称" //可选
        //}
        //}
        // file('文件域的字段名')
        $file = request()->file('file');

        // 上传到本地服务器 返回文件存储位置
        //
        // disk('磁盘配置名称') 该配置 在 config/filesystem.php中的 disks 中查看
        // disk('public') 代表使用的是 disks 中的 public 键名对应的磁盘配置
        // putFile('目录名', $file);
        //
        // $savename 执行上传 返回文件存储位置
        //
        // 当前文件存储位置：public/storage/topic/当前时间/文件名
        $savename = \think\facade\Filesystem::disk('public')->putFile('topic', $file);

        // 将上传后的文件位置返回给前端
        return json(['code' => 0, 'msg' => 'ok', 'data' => ['src' => $this->request->domain() . '/storage/' . $savename, 'title' => 'title']]);
    }
}
