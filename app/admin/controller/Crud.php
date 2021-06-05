<?php
namespace app\admin\controller;

use app\common\library\Menu;
use app\admin\model\MyCrud;
use think\Request;
use app\admin\validate\Applets as adminValidate;
use think\exception\PDOException;
use think\facade\Db;
use think\Exception;
use think\facade\Lang;
use think\facade\View;
use think\helper\Str;

/**
 * Api自动生成
 *
 * 
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
    private function getName($comment, $field)
    {
        $comment = explode(":", $comment ?? '');
        if (empty($comment)) {
            $comment = $field;
        } else {
            $comment = $comment[0];
        }
        return $comment;
    }
    public function buildLang($table)
    {
        $tableColumns = $this->getTableColumn($table);
        $langPath = "../app/admin/lang/" . Lang::getLangSet();
        if (!file_exists($langPath)) {
            mkdir($langPath);
        }
        $langPath .=  '/' . Str::snake($table);
        if (!file_exists($langPath)) {
            mkdir($langPath);
        }
        $data = [];
        foreach ($tableColumns as $elt => $item) {
            $field = $item['field'];
            $comment = $this->getName($item['comment'], $field);
            $data[ucfirst($field)] = $comment;
        }
        $filedName = $langPath . '/' . "common.php";
        $commonFile = fopen($filedName, "w");
        fwrite($commonFile, sprintf("<?php\n return %s;", var_export($data, true)));
        fclose($commonFile);
    }
    public function buildModel($table, $deep = 0)
    {
        $filedName = "../app/admin/model/" . $this->controlName($table) . ".php";
        $tableColumns = $this->getTableColumn($table);
        // 生成model
        $modelFile = fopen($filedName, "w");

        fwrite($modelFile, $this->getReplacedStub('model/body.stub', [
            'className' => $this->controlName($table),
            'scopeTpl' => $this->buildScopeTpl($tableColumns),
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
            } elseif (endWith($demo, '_id')) {
            } elseif (startWith($demo, 'set')) {
            } elseif (startWith($demo, 'select')) {
            } elseif (explode('(', $item['type'])[0] === 'enum') {
            } elseif (endWith($demo, 'time') || endWith($demo, '_at')) {
                if (!isset($arr['daterange'])) {
                    $arr['daterange'] = $this->getReplacedStub('model/scope/daterange.stub', []);
                }
            }
        }
        return implode(PHP_EOL, $arr);
    }
    public function buildSearchCode($table, $tableColumns)
    {
        $arr = [];
        $ifarr = [];
        foreach ($tableColumns as $elt => $item) {
            $demo = $item['field'];
            $s = explode('_', $item['field']);
            if (endWith($item['field'], '_ids')) {
                $ifarr[] = "
                    \${$item['field']} = \$this->request->param('{$item['field']}',null);
                    if(\${$item['field']}){
                        \$query->whereFindInSet('{$item['field']}',\${$item['field']}.'');
                    }
                    ";
            } elseif (endWith($item['field'], '_id')) {
                $ifarr[] = "
                    \${$item['field']} = \$this->request->param('{$item['field']}',null);
                    if(\${$item['field']}){
                        \$query->where('{$item['field']}',\${$item['field']});
                    }
                    ";
            } elseif (endWith($item['field'], 'images') || endWith($item['field'], 'image') || endWith($item['field'], 'img') || endWith($item['field'], 'imgs')) {
            } elseif (endWith($item['field'], 'content')) {
            } elseif (endWith($item['field'], 'city') && explode('(', $item['type'])[0] === 'varchar') {
            } elseif (endWith($item['field'], 'file')) {
            } elseif (startWith($demo, 'select')) {
                $ifarr[] = "
                \${$item['field']} = \$this->request->param('{$item['field']}',null);
                if(\${$item['field']}){
                    \$query->where('{$item['field']}',\${$item['field']});
                }
                ";
            } elseif (explode('(', $item['type'])[0] === 'enum') {
                $ifarr[] = "
                \${$item['field']} = \$this->request->param('{$item['field']}',null);
                if(\${$item['field']}){
                    \$query->where('{$item['field']}',\${$item['field']});
                }
                ";
            } elseif (startWith($demo, 'set')) {
                $ifarr[] = "
                    \${$item['field']} = \$this->request->param('{$item['field']}',null);
                    if(\${$item['field']}){
                        \$query->whereFindInSet('{$item['field']}',\${$item['field']}.'');
                    }
                    ";
            } elseif (endWith($demo, 'time') || endWith($demo, '_at')) {
                $arr[] = "->dateRange('{$item['field']}',\$this->request->param('{$item['field']}',null))";
            } else {
                $ifarr[] = "
                    \${$item['field']} = \$this->request->param('{$item['field']}',null);
                    if(\${$item['field']}){
                        \$query->whereLike('{$item['field']}',\"%{\${$item['field']}}%\");
                    }
                    ";
            }
        }
        return count($arr) > 0 ? '->where(function($query){
            $query' . implode(PHP_EOL, $arr) . ';
            ' . implode(PHP_EOL, $ifarr) . '
        })' : '';
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
            } elseif (endWith($demo, '_id')) {
                $tableName = str_replace('_id', '', $demo);

                $str .= $this->getReplacedStub('model/fieldNameAttr/id.stub', [
                    'fieldName' => $this->controlName($demo, true),
                    'relation' => $this->controlName($demo, false),
                    'relationTable' => $tableName,
                    'fieldNameList' => $this->controlName($demo, false) . 'List',
                ]) . "\n";
            } elseif ((explode('(', $item['type'])[0] === 'json' || explode('(', $item['type'])[0] === 'text') && endWith($item['field'], '_fieldlist')) {
                $str .= $this->getReplacedStub('model/fieldNameAttr/array.stub', [
                    'fieldName' => $this->controlName($demo, true),
                ]) . "\n";
            } elseif (explode('(', $item['type'])[0] === 'json') {
                $str .= $this->getReplacedStub('model/fieldNameAttr/json.stub', [
                    'fieldName' => $this->controlName($demo, true),
                ]) . "\n";
            } elseif (startWith($demo, 'set')) {
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
            } elseif (startWith($demo, 'select')) {
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
            } elseif (explode('(', $item['type'])[0] === 'enum') {
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
            } elseif (endWith($demo, 'time') || endWith($demo, '_at')) {
                //##
                $str .= $this->getReplacedStub('model/fieldNameAttr/time.stub', [
                    'fieldName' => $this->controlName($demo, true),
                ]) . "\n";
            } elseif (endWith($demo, 'imgs') || endWith($demo, 'images')) {
                $str .= $this->getReplacedStub('model/fieldNameAttr/imgs.stub', [
                    'fieldName' => $this->controlName($demo, true),
                    'delimiter' => '|',
                ]) . "\n";
            } elseif (endWith($item['field'], 'file')) {
                $str .= $this->getReplacedStub('model/fieldNameAttr/files.stub', [
                    'fieldName' => $this->controlName($demo, true),
                    'delimiter' => '|',
                ]) . "\n";
            } elseif (endWith($demo, 'img') || endWith($demo, 'image')) {
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
            'action' => $this->buildSearchCode($table, $tableColumns),
            'relations' => json_encode($relation),
            'functions' => $this->buildIndexFunction($table),
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
                    'name' => '' . $table . '/index',
                    'title' => $fix . '管理',
                    'icon' => 'fa-list',
                    'remark' => '',
                    'ismenu' => 1,
                    'sublist' => [
                        ['name' => '' . $table . '/add', 'title' => '添加'],
                        ['name' => '' . $table . '/edit', 'title' => '编辑 '],
                        ['name' => '' . $table . '/del', 'title' => '删除']
                    ]
                ]
            ];
            try {
                //生成菜单
                Menu::create($menu);
            } catch (Exception $e) {
                $createMenuError = "生成代码成功，已自动忽略菜单生成";
            }



            // 生成controller

            $this->buildController($table);
            $this->buildLang($table);
            $this->buildModel($table);


            //生成添加编辑的公用view
            $this->buildIndexView($table);


            //生成添加编辑的公用view
            $this->buildEditView($table);

            $this->success($createMenuError ?? "生成成功");
        } catch (Exception $e) {
            $this->error($e->getMessage() . '/' . $e->getFile() . ':' . $e->getLine());
            $this->error("生成失败");
        }
    }

    public function buildSeachFormItem($label, $type, $name, $item)
    {
        $html = "";
        $comment = sprintf("{:__('%s')}", ucfirst($item['field']));
        switch ($type) {
            case 'input':
                $html = '<input type="text" name="' . $name . '" id="' . $name . '"  autocomplete="off" class="layui-input" placeholder="' . $comment . '">';
                break;
            case 'switch':
                $html = '
                <select name="' . $name . '"  id="' . $name . '">
                    <option value="">{:__(\'Select\')}</option>
                    <option value="on">{:__(\'Open\')}</option>
                    <option value="off">{:__(\'Close\')}</option>
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
                <select name="' . $name . '"  id="' . $name . '"  >
                    <option value="">{:__(\'Select\')}</option>
                    ' . $str . '
                </select>
                ';
                break;
            case 'select':
                $html = '
                <select name="' . $name . '"  id="' . $name . '">
                    <option value="">{:__(\'Select\')}</option>
                </select>
                ';
                break;
            case 'datepicker':
                $html = '<input type="text" id="' . $name . '" name="' . $name . '"  autocomplete="off" class="layui-input" placeholder="' . $comment . '">';
                break;
        }
        return '
        <div class="layui-inline">
            <label class="layui-form-label">' . $label . '</label>
            <div class="layui-input-inline">
            ' . $html . '
            </div>
        </div>
      ';
    }
    public function getSearchFormHtml($table)
    {
        $list = $this->getTableColumn($table);
        $arr = [];
        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            $comment = sprintf("{:__('%s')}", ucfirst($item['field']));
            try {
                if ($item['field'] === Db::name("$table")->getPk()) {
                    $arr[] = $this->buildSeachFormItem($comment, 'input', $item['field'], $item);
                } elseif (explode('(', $item['type'])[0] === 'enum' && $item['field'] === 'state') {
                    $arr[] = $this->buildSeachFormItem($comment, 'comment-select', $item['field'], $item);
                } elseif (explode('(', $item['type'])[0] === 'enum') {
                    $arr[] = $this->buildSeachFormItem($comment, 'comment-select', $item['field'], $item);
                } elseif (endWith($item['field'], 'images') || endWith($item['field'], 'image') || endWith($item['field'], 'img') || endWith($item['field'], 'imgs')) {
                } elseif ((explode('(', $item['type'])[0] === 'json' || explode('(', $item['type'])[0] === 'text') && endWith($item['field'], '_fieldlist')) {
                } elseif (endWith($item['field'], '_id')) {
                    $arr[] = $this->buildSeachFormItem($comment, 'select', $item['field'], $item);
                } elseif (endWith($item['field'], 'file')) {
                } elseif (endWith($item['field'], '_ids')) {
                    $arr[] = $this->buildSeachFormItem($comment, 'select', $item['field'], $item);
                } elseif (endWith($item['field'], 'switch')) {
                    $arr[] = $this->buildSeachFormItem($comment, 'switch', $item['field'], $item);
                } elseif (explode('(', $item['type'])[0] === 'set') {
                    $arr[] = $this->buildSeachFormItem($comment, 'comment-select', $item['field'], $item);
                } elseif (endWith($item['field'], 'content')) {
                    continue;
                } elseif (explode('(', $item['type'])[0] === 'int' && (end($s) === 'time' || end($s) === 'at' || endWith($item['field'], 'time'))) {
                    $arr[] = $this->buildSeachFormItem($comment, 'datepicker', $item['field'], $item);
                } elseif (explode('(', $item['type'])[0] === 'datetime') {
                    $arr[] = $this->buildSeachFormItem($comment, 'datepicker', $item['field'], $item);
                } elseif (endWith($item['field'], 'city') && explode('(', $item['type'])[0] === 'varchar') {
                } else {
                    $arr[] = $this->buildSeachFormItem($comment, 'input', $item['field'], $item);
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        return implode(PHP_EOL, $arr);
    }
    public function buildIndexView($table)
    {
        // 生成view index
        $path = "../app/admin/view/" . $table;

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $viewFile = fopen($path . "/" . "index.html", "w");
        $this->layuiAddonUsed = ['form', 'okLayer', 'okUtils', 'table', '$' => 'jquery'];
        fwrite($viewFile, $this->getReplacedStub('view/index.stub', [
            'table' => $table,
            'searchForm' => $this->getSearchFormHtml($table),
            'tableCols' => $this->getViewFiledList($table),
            'modalWidth' => '90%',
            'modalHeight' => '90%',
            'formInit' => $this->getViewIndexFormInit($table),
            'imageList' => $this->getViewImgList($table),
            'layuiAddonUsed' => $this->getLayuiAddonUsed($table),
            'getListFunction' => $this->getViewIndexGetListFunction($table),
            'varInit' => $this->getEditVarInitJs(),

        ]));
        $this->layuiAddonUsed = [];
        fclose($viewFile);
    }
    public function buildRelationListFunction($table)
    {
        return $this->getReplacedStub('controller/relation/getList.stub', [
            'fieldName' => $this->controlName($table),
            'table' => $table,

        ]);
    }
    public function buildIndexFunction($table)
    {
        $list = $this->getTableColumn($table);
        $tables = [];
        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            if (endWith($item['field'], '_ids') || endWith($item['field'], '_id')) {
                $tableName = str_replace(endWith($item['field'], '_ids') ? '_ids' : "_id", '', $item['field']);
                if (!array_key_exists($tableName, $tables)) {
                    $tables[$tableName] = $this->buildRelationListFunction($tableName);
                }
            }
        }
        return implode(PHP_EOL, $tables);
    }

    public function getViewIndexGetListFunction($table)
    {
        $list = $this->getTableColumn($table);
        $arr = [];
        $list = $this->getTableColumn($table);
        $tables = [];
        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            if (endWith($item['field'], '_ids') || endWith($item['field'], '_id')) {
                $tableName = str_replace(endWith($item['field'], '_ids') ? '_ids' : "_id", '', $item['field']);
                $name = $this->controlName($tableName);
                if (!array_key_exists($tableName, $tables)) {
                    $tables[$tableName] = $this->getReplacedStub('view/js/getList.stub', [
                        'fieldName' => $name,
                    ]);
                }

                if (!array_key_exists($item['field'], $tables)) {
                    $tables[$item['field']] = "get{$name}List('{$item['field']}');";
                }
            }
        }
        return implode(PHP_EOL, $tables);
    }
    public function getViewIndexFormInit($table)
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
                          ,range: true
                        });";
            } elseif (explode('(', $item['type'])[0] === 'int' && (end($s) === 'time' || end($s) === 'at' || endWith($item['field'], 'time'))) {
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
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
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
                } elseif ($type == 'js') {
                    $str .= "<script src='$file' type='text/javascript'></script>" . PHP_EOL;
                }
            }
        }
        return $str;
    }
    public function addEditAddonUsed($addon, $alias = null)
    {
        if (!in_array($addon, $this->layuiAddonUsed)) {
            $this->layuiAddonUsed[$alias ?? $addon] = $addon;
        }
    }
    public function getEditVarInitJs()
    {
        $str = "";
        foreach ($this->layuiAddonUsed as $key => $value) {
            $str .= "let " . (is_numeric($key) ? $value : $key) . " = layui.$value;" . PHP_EOL;
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
        $initFileManage = false;
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
            } elseif (explode('(', $item['type'])[0] === 'int' && (end($s) === 'time' || end($s) === 'at' || endWith($item['field'], 'time'))) {
                $this->addEditAddonUsed('laydate');
                $arr[] = "
                //=============渲染字段{$item['comment']}组件
                laydate.render({ 
                          elem: \"#" . $item['field'] . "\"
                          ,trigger:'click'
                          ,type: 'datetime'
                        });";
            } elseif (endWith($item['field'], 'file')) {
                $this->addEditAddonUsed('jquery', '$');
                if (!$initFileManage) {
                    $arr[] = <<<EOF
                $(".filemanage .upload").click(function () {
                    let id = $(this).parents(".filemanage").attr("data-id");
                    filemangeWindowId = okLayer.open("附件管理", "{:url('attachment/filemanage')}", "94%", "94%", function (layero) {
                      $(layero).find("iframe")[0].contentWindow.getImages = function (images) {
                        
                        let data = [];
                        if($("#" + id).val()!=''){
                            data = $("#" + id).val().split('|');
                        }
                        for(let key in images){
                            if(data.indexOf(images[key])==-1 ){
                                data.push(images[key]);
                            }
                        }
                        $("#" + id).val(data.join('|'))
                        let content = '';
                        for (let item of data) {
                          content += '<tr data-path="'+item+'">';
                          content += '<td style="word-break : break-all;">' + item + '</td>';
                          content += '<td> <button type="button" class="layui-btn layui-btn-danger delete">删除</button></td >';
                          content += '</tr>';
                        }
                        $("#" + id + "_list").html(content);
                      }
                    })
                  })
                  $(".filemanage").on("click",".delete",function(){
                        let id = $(this).parents(".filemanage").attr("data-id");
                        let path = $(this).parents('tr').attr('data-path');
                        $(this).parents('tr').remove();
                        let data = $("#" + id).val().split('|');
                        let index = data.indexOf(path);
                        if(index==-1) return;
                        data.splice( index, 1); 
                        $("#" + id).val(data.join('|'))
                  })
EOF;

                    $initFileManage = true;
                }
            } elseif (endWith($item['field'], 'city') && explode('(', $item['type'])[0] === 'varchar') {
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
            } elseif (end($s) === 'ids') {
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
            } elseif ((explode('(', $item['type'])[0] === 'json' || explode('(', $item['type'])[0] === 'text') && endWith($item['field'], '_fieldlist')) {
                $this->addEditAddonUsed('FieldList');
                $this->import('css', '/static/lib/field-list/field-list.css');
                $list = $this->controlName($item['field'] . '_list', false);
                $arr[] = "
                var {$list} = '{:isset(\${$table})?json_encode(\${$table}.{$item['field']}):null}';
                try{
                    {$list} = JSON.parse({$list});
                }catch(e){
                    {$list} = [];
                }
                var {$item['field']} = new FieldList('{$item['field']}',{
                    el: '#{$item['field']}',
                    name: '{$item['field']}',
                    data:{$list}
                });";
            }
        }
        return implode(PHP_EOL, $arr);
    }
    public function getLayuiAddonUsed()
    {
        return json_encode(array_values($this->layuiAddonUsed));
    }
    public function getViewEditExtendAddons($table)
    {
        $list = $this->getTableColumn($table);
        $addons = [];

        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            if (end($s) === 'ids') {
                $addons['xmSelect'] = 'xm-select';
            } elseif ((explode('(', $item['type'])[0] === 'json' || explode('(', $item['type'])[0] === 'text') && endWith($item['field'], '_fieldlist')) {
                $addons['FieldList'] = 'field-list/field-list';
            } elseif (endWith($item['field'], 'city') && explode('(', $item['type'])[0] === 'varchar') {
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
            $comment = sprintf('{:__("%s")}', ucfirst($item['field']));
            if (endWith($item['field'], 'switch') == 'switch') {
                $str .= "{field: '" . $item['field'] . "', title: '" . $comment . "',templet: function (d) {
        var state = \"\";
        if (d.switch == \"on\") {
            state = \"<input type='checkbox' value='\" + d.id + \"' id='switch' lay-filter='stat' checked='checked' name='switch'  lay-skin='switch' lay-text='{:__(\"Open\")}|{:__(\"Close\")}' >\";
        } else {
            state = \"<input type='checkbox' value='\" + d.id + \"' id='switch' lay-filter='stat'  name='switch'  lay-skin='switch' lay-text='{:__(\"Open\")}|{:__(\"Close\")}' >\";
        }
        return state;
    }}," . PHP_EOL;
            } else {
                if (endWith($item['field'], 'images') || endWith($item['field'], 'image') || endWith($item['field'], 'img') || endWith($item['field'], 'imgs')) {
                    $field  = $item['field'];
                    $name  = $comment;
                    $this->addEditAddonUsed('laytpl');
                    $code = (endWith($item['field'], 'images') || endWith($item['field'], 'imgs')) ? '' : "d.{$field} = [d.{$field}];";
                    $str .= <<<EOF
                    {
                        field: '{$field}', title: '{$name}', templet: function (d) {
                            d.imageField = '{$field}';
                            {$code}
                            return laytpl($("#imageTpl").html()).render(d);
                        }
                    },
EOF;
                } elseif (endWith($item['field'], 'file')) {
                } elseif (endWith($item['field'], '_id')) {
                    $str .= "{field: '" . $this->controlName($item['field'], false) . "', title: '" . $comment . "',templet: function (d) {return d." . ($this->controlName($item['field'], false) . 'List.name') . "} }," . PHP_EOL;
                } elseif (end($s) === 'ids') {
                    $filedName = $this->controlName($item['field'], false) . 'List';
                    $str .= "{field: '" . $filedName . "', title: '" . $comment . "',templet: function (d) {
                        var data = d.{$filedName};
                        var arr = [];
                        for(var key in data){
                            arr.push(data[key].name);
                        }
                        return arr.join(',')
                    } }," . PHP_EOL;
                } elseif ((explode('(', $item['type'])[0] === 'json' || explode('(', $item['type'])[0] === 'text') && endWith($item['field'], '_fieldlist')) {
                    $str .= "{field: '" . $item['field'] . "_name', title: '" . $comment . "',templet: function (d) {
                        var data = d.{$item['field']};
                        var arr = [];
                        for(var item of data){
                            arr.push(item.key+':'+item.value);
                        }
                        return arr.join(',')
                    } }," . PHP_EOL;
                   } elseif (explode('(', $item['type'])[0] === 'enum' || $item['field'] === 'state') {
                    $str .= "{field: '" . $item['field'] . "_name', title: '" . $comment . "'}," . PHP_EOL;
                } elseif (endWith($item['field'], 'city') && explode('(', $item['type'])[0] === 'varchar') {
                    $str .= "{field: '" . $item['field'] . "', title: '" . $comment . "'}," . PHP_EOL;
                } else {
                    $str .= "{field: '" . $item['field'] . "', title: '" . $comment . "'}," . PHP_EOL;
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
                if (endWith($item['field'], 'image') || endWith($item['field'], 'image') || endWith($item['field'], 'images')  || endWith($item['field'], 'imgs')) {
                    if (!$flag) {
                        $str .= "<script type=\"text/html\" id=\"imageTpl\">
                        {{# var img = d[d.imageField] }}
                        {{# for(var i=0;i < img.length;i++){}}
                        <a href=\"javascript:amplificationImg('图片','{{img[i]}}')\">
                            <img src=\"{{img[i]}}\" style=\"width: auto;height: 100%;\"/>
                        </a>
                        {{# } }}
                    </script>";
                    }
                    $flag = true;
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
            $comment = sprintf('{:__("%s")}', ucfirst($item['field']));
            try {
                if ($item['field'] === Db::name("$table")->getPk()) {
                    $str .= "
                    {if " . '$' . "" . $table . "." . $item['field'] . "??null}
                    <input type=\"hidden\" name=\"" . $item['field'] . "\" placeholder=\"\" autocomplete=\"off\" class=\"layui-input\" value=\"" . '{$' . "" . $table . "." . $item['field'] . "??''}\">
                    {/if}
                    ";
                } elseif (explode('(', $item['type'])[0] === 'enum' && $item['field'] === 'state') {
                    $str .= "<div class=\"layui-form-item\">
                            <label class=\"layui-form-label\">" . $comment . "</label>
                            <div class=\"layui-input-block\">
                                " . $this->danxuanedit($table, $item['field'], $item) . "
                            </div>
                        </div>";
                } elseif ((explode('(', $item['type'])[0] === 'json' || explode('(', $item['type'])[0] === 'text') && endWith($item['field'], '_fieldlist')) {
                    $str .= "<div class=\"layui-form-item\">
                            <label class=\"layui-form-label\">" . $comment . "</label>
                            <div class=\"layui-input-block\">
                                <div id=\"{$item['field']}\" name=\"{$item['field']}\"></div>
                            </div>
                        </div>";
                } elseif (explode('(', $item['type'])[0] === 'enum') {
                    $str .= "        <div class=\"layui-form-item\">
            <label class=\"layui-form-label\">" . $comment . "</label>
            <div class=\"layui-input-block\">
                <select name=\"" . $item['field'] . "\" lay-verify=\"required\">
                    <option value=\"\">{:__('Select')}</option>
                    " . $this->xialaedit($table, $item['field'], $item) . "
                </select>
            </div>
        </div>";
                } elseif (endWith($item['field'], 'images') || endWith($item['field'], 'image') || endWith($item['field'], 'img') || endWith($item['field'], 'imgs')) {
                    $fieldName = strpos($item['field'], 's') !== false ? ('{:implode(\'|\',$' . "" . $table . "." . $item['field'] . '??[])}') : ('{$' . "" . $table . "." . $item['field'] . '??null}');
                    $str .= "<div class=\"layui-form-item\">
                    <label class=\"layui-form-label\">" . $item['comment'] . "</label>
                      <div class=\"layui-input-block layui-upload\">
                        <input name=\"" . $item['field'] . "\" class=\"layui-input layui-col-xs6\" lay-verify=\"required\" placeholder=\"{:__('Please upload')}\" value=\"" . $fieldName  . "\">
                        <div class=\"layui-upload-btn\" >
                            <span><a class=\"layui-btn\" data-url=\"{:url('upload/uploadfile')}\" data-upload=\"" . $item['field'] . "\" data-upload-number=\"" . (strpos($item['field'], 's') !== false ? 'more' : 'one') . "\" data-upload-exts=\"png|jpg|ico|jpeg\" data-upload-icon=\"image\"><i class=\"fa fa-upload\"></i>{:__('Upload')}</a></span>
                            <span><a class=\"layui-btn layui-btn-normal\" id=\"select_logo_" . $item['field'] . "\" data-upload-select=\"" . $item['field'] . "\" data-upload-number=\"" . (strpos($item['field'], 's') === true ? 'more' : 'one') . "\" data-upload-mimetype=\"image/*\"><i class=\"fa fa-list\"></i>{:__('Select')}</a></span>
                        </div>
                    </div>
                </div>";
                } elseif (endWith($item['field'], 'file')) {
                    $fieldName = '$' . $table . "." . $item['field'];
                    $field  = $table . "." . $item['field'];
                    $str .= <<<EOF
                    <div class="layui-form-item">
                        <label class="layui-form-label">{$item['comment']}</label>
                        <div class="layui-input-block layui-upload filemanage" data-id="{$item['field']}">
                            <input id="{$item['field']}" name="{$item['field']}" type="text" style="display: none;" value="{:implode('|',{$fieldName}??[])}"/>
                            <div class="layui-upload-list">
                            <table class="layui-table" style="margin:0 10px;table-layout:fixed">
                                <thead>
                                <tr>
                                    <th>文件名</th>
                                    <th width="50">操作</th>
                                </tr>
                                </thead>
                                <tbody id="{$item['field']}_list">
                                    {if \${$table}??null}
                                    {foreach {$fieldName} as \$key=>\$vo } 
                                    <tr data-path="{\$vo}">
                                        <td style="word-break: break-all;">{\$vo}</td>
                                        <td>
                                        <button type="button" class="layui-btn layui-btn-danger delete">删除</button>
                                        </td>
                                    </tr>
                                    {/foreach}
                                    {/if}
                                </tbody>
                            </table>
                            <button type='button' class='layui-btn upload'  style="margin: 10px;">上传文件</button>
                            </div>
                        </div>
                    </div>
EOF;
                } elseif (endWith($item['field'], '_id')) {
                    $filedName = $this->controlName(str_replace('_id', '', $item['field']), false) . 's';
                    $field  = $table . "." . $item['field'];
                    $str .= "  <div class=\"layui-form-item\">
            <label class=\"layui-form-label\">" . $comment . "</label>
            <div class=\"layui-input-block\">
                <select name=\"" . $item['field'] . "\" lay-verify=\"required\">
                    <option value=\"\">{:__('Select')}</option>
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
                } elseif (endWith($item['field'], '_ids')) {
                    $filedName = $this->controlName(str_replace('_ids', '', $item['field']), false) . 's';
                    $str .= "  <div class=\"layui-form-item\">
            <label class=\"layui-form-label\">" . $comment . "</label>
            <div class=\"layui-input-block\">
            <div id=\"" . $item['field'] . "\" name=\"" . $item['field'] . "\"  ></div>
            </div>
        </div>";
                } elseif (explode('(', $item['type'])[0] === 'set') {
                    $str .= "
<div class=\"layui-form-item\">
    <label class=\"layui-form-label\">" . $comment . "</label>
    <div class=\"layui-input-block\">
    " . $this->duoxuanedit($table, $item['field'], $item) . "
    </div>
</div>";
                } elseif (endWith($item['field'], 'content')) {
                    $str .= " <div class=\"layui-form-item\">
    <label class=\"layui-form-label\">" . $comment . "</label>
    <div class=\"layui-input-block\">
      <textarea class=\"layui-textarea editor\" id=\"" . $item['field'] . "\" name=\"" . $item['field'] . "\" lay-verify=\"" . $item['field'] . "\">" . '{$' . "" . $table . "." . $item['field'] . "??''}</textarea>
    </div>
  </div>";
                } elseif (endWith($item['field'], 'switch')) {
                    $str .= "    <div class=\"layui-form-item\">
    <label class=\"layui-form-label\">" . $comment . "</label>
    <div class=\"layui-input-block\">
      <input type=\"checkbox\" name=\"" . $item['field'] . "\" lay-skin=\"switch\" lay-text=\"{:__('Open')}|{:__('Close')}\" {if isset(\$$table) && $" . $table . "." . $item['field'] . " == 'on'}checked{/if}>
    </div>
  </div>";
                } elseif (explode('(', $item['type'])[0] === 'int' && (end($s) === 'time' || end($s) === 'at' || endWith($item['field'], 'time'))) {
                    $fieldName = $table . "['{$item['field']}']";
                    $str .= "  <div class=\"layui-form-item\">
    <div class=\"layui-inline\">
      <label class=\"layui-form-label\">" . $comment . "</label>
      <div class=\"layui-input-block\">
        <input type=\"text\" name=\"" . $item['field'] . "\" id=\"" . $item['field'] . "\" autocomplete=\"off\" class=\"layui-input\" placeholder='yy-mm-dd HH:ii:ss' value=\"" . '{$' . "" . $table . "." . $item['field'] . "??date('Y-m-d H:i:s')}\">
      </div>
    </div>
  </div>";
                } elseif (explode('(', $item['type'])[0] === 'datetime') {
                    $str .= "  <div class=\"layui-form-item\">
    <div class=\"layui-inline\">
      <label class=\"layui-form-label\">" . $comment . "</label>
      <div class=\"layui-input-block\">
        <input type=\"text\" name=\"" . $item['field'] . "\" id=\"" . $item['field'] . "\" autocomplete=\"off\" class=\"layui-input\" placeholder='yy-mm-dd HH:ii:ss' value=\"" . '{$' . "" . $table . "." . $item['field'] . "??date('Y-m-d H:i:s')}\">
      </div>
    </div>
  </div>";
                } elseif (endWith($item['field'], 'city') && explode('(', $item['type'])[0] === 'varchar') {
                    $str .= "<div class=\"layui-form-item\">
                    <label class=\"layui-form-label\">" . $item['comment'] . "</label>
                    <div class=\"layui-input-block\">
                        <input type=\"text\" name=\"" . $item['field'] . "\" id=\"" . $item['field'] . "\" autocomplete=\"on\" placeholder=\"" . $comment . "\"  class=\"layui-input\"
                        readonly=\"readonly\" data-toggle=\"city-picker\">
                    </div>
                </div>" . PHP_EOL;
                } else {
                    $str .= "<div class=\"layui-form-item\">
                    <label class=\"layui-form-label\">" . $item['comment'] . "</label>
                    <div class=\"layui-input-block\">
                        <input type=\"text\" name=\"" . $item['field'] . "\" placeholder=\"" . $comment . "\" autocomplete=\"off\" class=\"layui-input\"
                               lay-verify=\"required\" value=\"" . '{$' . "" . $table . "." . $item['field'] . "??''}\">
                    </div>
                </div>";
                }
            } catch (Exception $e) {
                $str .= "<div class=\"layui-form-item\">
                    <label class=\"layui-form-label\">" . $item['comment'] . "</label>
                    <div class=\"layui-input-block\">

                        <input type=\"text\" name=\"" . $item['field'] . "\" placeholder=\"" . $comment . "\" autocomplete=\"off\" class=\"layui-input\"
                               lay-verify=\"required\" value=\"" . '{$' . "" . $table . "." . $item['field'] . "??''}\">
                    </div>
                </div>" . $e->getMessage();
            }
        }
        return $str;
    }


    public function wangEditor($field)
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
            if (endWith($item['field'], 'switch') === 'switch') {
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
