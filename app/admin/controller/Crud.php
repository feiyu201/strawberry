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
    public static function controlName($str, $ucwords = true)
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
        $list = Db::query('SHOW FULL FIELDS FROM ' . config('database.connections.mysql.prefix') . $table);
        $list = array_map('array_change_key_case', $list);
        return $list;
    }

    public function buildModel($table,$deep=0)
    {
        $filedName = "../app/admin/model/" . self::controlName($table) . ".php";
        $tableColumns = $this->getTableColumn($table);
        // 生成model
        $modelFile = fopen($filedName, "w");

        fwrite($modelFile, $this->getReplacedStub('model/body.stub', [
            'className' => self::controlName($table),
            'filedNameAttrTpl' => $this->buildTableFiledNameAttrTpl($tableColumns),
        ]));
        fclose($modelFile);
       
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
                    'fieldName' => self::controlName($demo, true),
                    'relation' => self::controlName($demo, false),
                    'relationTable' => $tableName,
                    'fieldNameList' => self::controlName($demo, false).'List',
                ]) . "\n";
            }else if (endWith($demo, '_id')) {
                $tableName = str_replace('_id', '', $demo);

                $str .= $this->getReplacedStub('model/fieldNameAttr/id.stub', [
                    'fieldName' => self::controlName($demo, true),
                    'relation' => self::controlName($demo, false),
                    'relationTable' => $tableName,
                    'fieldNameList' => self::controlName($demo, false).'List',
                ]) . "\n";
            }else if (startWith($demo, 'set')) {
                $arr = explode(',', explode(':', $item['comment'])[1]);
                $data = [];
                foreach ($arr as $k => $v) {
                    $array = explode('=', $v);
                    $data[$array[0]] = $array[1];
                }
                $str .= $this->getReplacedStub('model/fieldNameAttr/set.stub', [
                    'fieldName' => self::controlName($demo, true),
                    'otherFieldName'=>$demo.'_name',
                    'data'=>var_export($data,true)
                ]) . "\n";
            }else if (startWith($demo, 'select')) {
                $arr = explode(',', explode(':', $item['comment'])[1]);
                $data = [];
                foreach ($arr as $k => $v) {
                    $array = explode('=', $v);
                    $data[$array[0]] = $array[1];
                }
                $str .= $this->getReplacedStub('model/fieldNameAttr/select.stub', [
                    'fieldName' => self::controlName($demo, true),
                    'otherFieldName'=>$demo.'_name',
                    'data'=>var_export($data,true)
                ]) . "\n";
            }else if (explode('(', $item['type'])[0] === 'enum') {
                $arr = explode(',', explode(':', $item['comment'])[1]);
                $data = [];
                foreach ($arr as $k => $v) {
                    $array = explode('=', $v);
                    $data[$array[0]] = $array[1];
                }
                $str .= $this->getReplacedStub('model/fieldNameAttr/radio.stub', [
                    'fieldName' => self::controlName($demo, true),
                    'otherFieldName'=>$demo.'_name',
                    'data'=>var_export($data,true)
                ]) . "\n";
            }else if (endWith($demo, 'time') || endWith($demo, '_at')) {
                $str .= $this->getReplacedStub('model/fieldNameAttr/time.stub', [
                    'fieldName' => self::controlName($demo, true),
                ]) . "\n";
            }else if (endWith($demo, 'imgs') || endWith($demo, 'images')) {
                $str .= $this->getReplacedStub('model/fieldNameAttr/imgs.stub', [
                    'fieldName' => self::controlName($demo, true),
                    'delimiter' => '|',
                ]) . "\n";
            }else if (endWith($demo, 'img') || endWith($demo, 'image')) {
                $str .= $this->getReplacedStub('model/fieldNameAttr/img.stub', [
                    'fieldName' => self::controlName($demo, true),
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
    public static function buildAddCode($tableColumns)
    {

        $str = "";
        $loadModel = [];
        foreach ($tableColumns as $elt => $item) {
            $demo = $item['field'];
            $s = explode('_', $item['field']);
            if (end($s) === 'ids' || endWith($demo, '_id')) {
                $fieldName = self::controlName(str_replace(end($s) === 'ids' ? '_ids' : "_id", '', $demo), false) . 's';
                $className = self::controlName(str_replace(end($s) === 'ids' ? '_ids' : "_id", '', $demo), true);
                $tableName = str_replace('_id', '', $demo);
                if (!in_array($fieldName, $loadModel)) {
                    $str .= "\$$fieldName = \\think\\facade\\Db::name('$tableName')->field('id,name')->select();\n";
                    $str .= "View::assign('$fieldName',\$$fieldName);";
                    $loadModel[] = $fieldName;
                }
            }
        }
        return $str;
    }
    public static function buildEditCode($tableColumns)
    {
        $str = "";
        $loadModel = [];
        foreach ($tableColumns as $elt => $item) {
            $demo = $item['field'];
            $s = explode('_', $item['field']);
            if (end($s) === 'ids' || endWith($demo, '_id')) {
                $fieldName = self::controlName(str_replace(end($s) === 'ids' ? '_ids' : "_id", '', $demo), false) . 's';
                $className = self::controlName(str_replace(end($s) === 'ids' ? '_ids' : "_id", '', $demo), true);
                $tableName = str_replace('_id', '', $demo);
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
        $controllerFile = fopen("../app/admin/controller/" . self::controlName($table) . ".php", "w");

        fwrite($controllerFile, $this->getReplacedStub('controller/body.stub', [
            'className' => self::controlName($table),
            'modelClassName' => '\\app\\admin\\model\\' . self::controlName($table),
            'witchMethod' =>  self::getsWitchMethod($table),
            'addViewCode' => self::buildAddCode($tableColumns),
            'editViewCode' => self::buildEditCode($tableColumns),
            'table' => $table,
            'relations' => json_encode($relation),
        ]));
        fclose($controllerFile);
    }
    public function crud(Request $request)
    {

        try {
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
            //生成菜单
             Menu::create($menu);


            // 生成controller



            $this->buildController($table);
            $this->buildModel($table);


            // 生成view index
            $path = "../app/admin/view/" . $table;

            if (!file_exists($path))
                mkdir($path, 0777, true);

            $viewFile = fopen($path . "/" . "index.html", "w");
            $viewText = sprintf(
                file_get_contents('../addons/crud/index.txt'),
                self::getViewFiledList($table),
                '90%',
                '90%',
                '90%',
                '90%',
                self::getViewImgList($table)
            );
            // dump($viewFile);
            fwrite($viewFile, $viewText);
            fclose($viewFile);

            // 生成view add
            $path = "../app/admin/view/" . $table;
            if (!file_exists($path))
                mkdir($path, 0777, true);

            $viewFile = fopen($path . "/" . "add.html", "w");
            $viewText = sprintf(
                file_get_contents('../addons/crud/add.txt'),
                self::getViewAddHtml($table),
                self::xm($table),
                self::timejsadd($table)
            );
            fwrite($viewFile, $viewText);
            fclose($viewFile);

            // 生成view edit
            $path = "../app/admin/view/" . $table;
            if (!file_exists($path))
                mkdir($path, 0777, true);

            $viewFile = fopen($path . "/" . "edit.html", "w");
            $viewText = sprintf(
                file_get_contents('../addons/crud/edit.txt'),
                self::getViewEditHtml($table),
                self::xm($table),
                self::timejs($table)
            );
            fwrite($viewFile, $viewText);
            fclose($viewFile);
            $this->success("生成成功");
        } catch (Exception $e) {
            $this->error($e->getMessage() . '/' . $e->getFile() . ':' . $e->getLine());
            // $this->error("生成失败,请先删除原有菜单");
        }
    }

    public static function getViewFiledList($table)
    {
        $list = Db::query('SHOW FULL FIELDS FROM ' . config('database.connections.mysql.prefix') . $table);
        $list = array_map('array_change_key_case', $list);
        $str = "";

        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            if ($item['field'] == 'switch') {
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
                    $str .= "{field: '" . self::controlName($item['field'], false) . "', title: '" . explode(':', $item['comment'])[0] . "',templet: function (d) {return d." . (self::controlName($item['field'], false) . 'List.name') . "} }," . PHP_EOL;
                } else if (end($s) === 'ids') {
                    $filedName = self::controlName($item['field'], false).'List';
                    $str .= "{field: '" . $filedName . "', title: '" . explode(':', $item['comment'])[0] . "',templet: function (d) {
                        var data = d.{$filedName};
                        var arr = [];
                        for(var key in data){
                            arr.push(data[key].name);
                        }
                        return arr.join(',')
                    } }," . PHP_EOL;
                }else if (startWith($item['field'], 'set') ||startWith($item['field'], 'select')|| (explode('(', $item['type'])[0] === 'enum' && $item['field'] === 'state')) {
                    $str .= "{field: '" . $item['field'] . "_name', title: '" . explode(':', $item['comment'])[0] . "'}," . PHP_EOL;

                } else {
                    $str .= "{field: '" . $item['field'] . "', title: '" . explode(':', $item['comment'])[0] . "'}," . PHP_EOL;
                }
            }
        }
        return $str;
    }
    public static function getViewImgList($table)
    {
        $list = Db::query('SHOW FULL FIELDS FROM ' . config('database.connections.mysql.prefix') . $table);
        $list = array_map('array_change_key_case', $list);
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
    public static function getViewAddHtml($table)
    {

        $list = Db::query('SHOW FULL FIELDS FROM ' . config('database.connections.mysql.prefix') . $table);
        $list = array_map('array_change_key_case', $list);
        $str = "";



        foreach ($list as $elt => $item) {


            $s = explode('_', $item['field']);
            try {
                if ($item['key'] === 'PRI') {
                    continue;
                } else if (explode('(', $item['type'])[0] === 'enum' && $item['field'] === 'state') {
                    $str .= "<div class=\"layui-form-item\">
    <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
    <div class=\"layui-input-block\">
        " . self::danxuan($item['field'], $item) . "
    </div>
  </div>";
                } else if (explode('(', $item['type'])[0] === 'enum') {
                    $str .= "        <div class=\"layui-form-item\">
            <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
            <div class=\"layui-input-block\">
                <select name=\"" . $item['field'] . "\" lay-verify=\"required\">
                    <option value=\"\"></option>
                    " . self::xiala($item) . "
                </select>
            </div>
        </div>";
                } else if (explode('(', $item['type'])[0] === 'set') {
                    $str .= "<div class=\"layui-form-item\">
    <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
    <div class=\"layui-input-block\">
    " . self::duoxuan($item['field'], $item) . "
    </div>
  </div>";
                } else if ($item['field'] === 'switch') {
                    $str .= "    <div class=\"layui-form-item\">
    <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
    <div class=\"layui-input-block\">
      <input type=\"checkbox\" name=\"" . $item['field'] . "\" lay-skin=\"switch\" lay-text=\"开启|关闭\">
    </div>
  </div>";
                } else if (explode('(', $item['type'])[0] === 'int' && (end($s) === 'time' || end($s) === 'at' || endWith($item['field'], 'time'))) {
                    $str .= "  <div class=\"layui-form-item\">
    <div class=\"layui-inline\">
      <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
      <div class=\"layui-input-block\">
        <input type=\"text\" name=\"" . $item['field'] . "\" id=\"" . $item['field'] . "\" autocomplete=\"off\" class=\"layui-input\" placeholder='yy-mm-dd HH:ii:ss'>
      </div>
    </div>
  </div>";
                } else if (endWith($item['field'], '_id')) {
                    $filedName = self::controlName(str_replace('_id', '', $item['field']), false) . 's';
                    $str .= "  <div class=\"layui-form-item\">
            <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
            <div class=\"layui-input-block\">
                <select name=\"" . $item['field'] . "\" lay-verify=\"required\">
                    <option value=\"\"></option>
                    {foreach \${$filedName} as \$key=>\$vo } 
                        <option value=\"{\$vo.id}\">{\$vo.name}</option>
                    {/foreach}
                </select>
            </div>
        </div>";
                } else if ((explode('(', $item['type'])[0] === 'varchar' && (end($s) === 'img' || end($s) === 'image')) || (explode('(', $item['type'])[0] === 'text' && (end($s) === 'images' || end($s) === 'imgs'))) {
                    $str .= "<div class=\"layui-form-item\">
                    <label class=\"layui-form-label\">" . $item['comment'] . "</label>
                      <div class=\"layui-input-block layui-upload\">
                        <input name=\"" . $item['field'] . "\" class=\"layui-input layui-col-xs6\" lay-verify=\"required\" placeholder=\"请上传图片\" value=\"\">
                        <div class=\"layui-upload-btn\" >
                            <span><a class=\"layui-btn\" data-upload=\"" . $item['field'] . "\" data-upload-number=\"" . (strpos($item['field'], 's') !== false ? 'more' : 'one') . "\" data-upload-exts=\"png|jpg|ico|jpeg\" data-upload-icon=\"image\"><i class=\"fa fa-upload\"></i> 上传</a></span>
                            <span><a class=\"layui-btn layui-btn-normal\" id=\"select_logo\" data-upload-select=\"" . $item['field'] . "\" data-upload-number=\"one\" data-upload-mimetype=\"image/*\"><i class=\"fa fa-list\"></i> 选择</a></span>
                        </div>
                    </div>
                </div>";
                } else if (end($s) === 'ids') {
                    $str .= "  <div class=\"layui-form-item\">
            <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
            <div class=\"layui-input-block\">
                <div id=\"" . $item['field'] . "\" name=\"" . $item['field'] . "\"  ></div>
            </div>
        </div>";
                } else if (explode('(', $item['type'])[0] === 'text' && endWith($item['field'],'content')) {
                    $str .= " <div class=\"layui-form-item layui-form-text\">
    <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
    <div class=\"layui-input-block\">
      <textarea id=\"" . $item['field'] . "\" name=\"" . $item['field'] . "\"  lay-verify=\"" . $item['field'] . "\" style=\"display: none;\"></textarea>
    </div>
  </div>";
                } else if (explode('(', $item['type'])[0] === 'datetime') {
                    $str .= "  <div class=\"layui-form-item\">
    <div class=\"layui-inline\">
      <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
      <div class=\"layui-input-block\">
        <input type=\"text\" name=\"" . $item['field'] . "\" id=\"" . $item['field'] . "\" autocomplete=\"off\" class=\"layui-input\" placeholder='yy-mm-dd HH:ii:ss'>
      </div>
    </div>
  </div>";
                } else {
                    $str .= "<div class=\"layui-form-item\">
                    <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
                    <div class=\"layui-input-block\">
                        <input type=\"text\" name=\"" . $item['field'] . "\" placeholder=\"请输入" . $item['comment'] . "\" autocomplete=\"off\" class=\"layui-input\"
                               lay-verify=\"required\">
                    </div>
                </div>";
                }
            } catch (Exception $e) {
                $str .= "<div class=\"layui-form-item\">
                    <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
                    <div class=\"layui-input-block\">
                        <input type=\"text\" name=\"" . $item['field'] . "\" placeholder=\"请输入" . $item['comment'] . "\" autocomplete=\"off\" class=\"layui-input\"
                               lay-verify=\"required\">
                    </div>
                </div>";
            }
        }
        return $str;
    }

    public static function getViewEditHtml($table)
    {
        //        SHOW FULL COLUMNS FROM tbl_name [FROM db_name]
        $list = Db::query('SHOW FULL FIELDS FROM ' . config('database.connections.mysql.prefix') . $table);
        $list = array_map('array_change_key_case', $list);
        $str = "";
        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            try {
                if ($item['field'] === Db::name("$table")->getPk()) {
                    $str .= "<input type=\"hidden\" name=\"" . $item['field'] . "\" placeholder=\"\" autocomplete=\"off\" class=\"layui-input\" value=\"" . '{$' . "" . $table . "." . $item['field'] . "}\">";
                } else if (explode('(', $item['type'])[0] === 'enum' && $item['field'] === 'state') {
                    $str .= "<div class=\"layui-form-item\">
    <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
    <div class=\"layui-input-block\">
        " . self::danxuanedit($table, $item['field'], $item) . "
    </div>
  </div>";
                } else if (explode('(', $item['type'])[0] === 'enum') {
                    $str .= "        <div class=\"layui-form-item\">
            <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
            <div class=\"layui-input-block\">
                <select name=\"" . $item['field'] . "\" lay-verify=\"required\">
                    <option value=\"\"></option>
                    " . self::xialaedit($table, $item['field'], $item) . "
                </select>
            </div>
        </div>";
                } else if ((explode('(', $item['type'])[0] === 'varchar' && (end($s) === 'img' || end($s) === 'image')) || (explode('(', $item['type'])[0] === 'text' && (end($s) === 'images' || end($s) === 'imgs'))) {

                    $fieldName = strpos($item['field'], 's') !== false ? ('{:implode(\'|\',$' . "" . $table . "." . $item['field'] . ')}') : ('{$' . "" . $table . "." . $item['field'] . '}');
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
                    $filedName = self::controlName(str_replace('_id', '', $item['field']), false) . 's';
                    $field  = $table . "." . $item['field'];
                    $str .= "  <div class=\"layui-form-item\">
            <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
            <div class=\"layui-input-block\">
                <select name=\"" . $item['field'] . "\" lay-verify=\"required\">
                    <option value=\"\"></option>
                    {foreach \${$filedName} as \$key=>\$vo } 
                        {if \${$field}==\$vo.id}}
                        <option value=\"{\$vo.id}\" selected>{\$vo.name}</option>
                        {else/}
                        <option value=\"{\$vo.id}\" >{\$vo.name}</option>
                        {/if}
                    {/foreach}
                </select>
            </div>
        </div>";
                } else if (endWith($item['field'], '_ids')) {
                    $filedName = self::controlName(str_replace('_ids', '', $item['field']), false) . 's';
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
    " . self::duoxuanedit($table, $item['field'], $item) . "
    </div>
</div>";
                } else if (explode('(', $item['type'])[0] === 'text' && endWith($item['field'],'content')) {
                    $str .= " <div class=\"layui-form-item layui-form-text\">
    <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
    <div class=\"layui-input-block\">
      <textarea id=\"" . $item['field'] . "\" name=\"" . $item['field'] . "\" lay-verify=\"" . $item['field'] . "\" style=\"display: none;\">" . '{$' . "" . $table . "." . $item['field'] . "}</textarea>
    </div>
  </div>";
                } else if ($item['field'] === 'switch') {
                    $str .= "    <div class=\"layui-form-item\">
    <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
    <div class=\"layui-input-block\">
      <input type=\"checkbox\" name=\"" . $item['field'] . "\" lay-skin=\"switch\" lay-text=\"开启|关闭\" {if $" . $table . "." . $item['field'] . " == 'on'}checked{/if}>
    </div>
  </div>";
                } else if (explode('(', $item['type'])[0] === 'int' && (end($s) === 'time' || end($s) === 'at' || endWith($item['field'], 'time'))) {
                    $str .= "  <div class=\"layui-form-item\">
    <div class=\"layui-inline\">
      <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
      <div class=\"layui-input-block\">
        <input type=\"text\" name=\"" . $item['field'] . "\" id=\"" . $item['field'] . "\" autocomplete=\"off\" class=\"layui-input\" placeholder='yy-mm-dd HH:ii:ss' value='" . '{$' . "" . $table . "." . $item['field'] . "|date=\"Y-m-d H:i:s\"}'>
      </div>
    </div>
  </div>";
                } else if (explode('(', $item['type'])[0] === 'datetime') {
                    $str .= "  <div class=\"layui-form-item\">
    <div class=\"layui-inline\">
      <label class=\"layui-form-label\">" . explode(':', $item['comment'])[0] . "</label>
      <div class=\"layui-input-block\">
        <input type=\"text\" name=\"" . $item['field'] . "\" id=\"" . $item['field'] . "\" autocomplete=\"off\" class=\"layui-input\" placeholder='yy-mm-dd HH:ii:ss' value='" . '{$' . "" . $table . "." . $item['field'] . "}'>
      </div>
    </div>
  </div>";
                } else {
                    $str .= "<div class=\"layui-form-item\">
                    <label class=\"layui-form-label\">" . $item['comment'] . "</label>
                    <div class=\"layui-input-block\">
                        <input type=\"text\" name=\"" . $item['field'] . "\" placeholder=\"请输入" . $item['comment'] . "\" autocomplete=\"off\" class=\"layui-input\"
                               lay-verify=\"required\" value=\"" . '{$' . "" . $table . "." . $item['field'] . "}\">
                    </div>
                </div>";
                }
            } catch (Exception $e) {
                $str .= "<div class=\"layui-form-item\">
                    <label class=\"layui-form-label\">" . $item['comment'] . "</label>
                    <div class=\"layui-input-block\">
                        <input type=\"text\" name=\"" . $item['field'] . "\" placeholder=\"请输入" . $item['comment'] . "\" autocomplete=\"off\" class=\"layui-input\"
                               lay-verify=\"required\" value=\"" . '{$' . "" . $table . "." . $item['field'] . "}\">
                    </div>
                </div>";
            }
        }
        return $str;
    }


    public static function wangEditor($field)
    {
        $str = "    
        layui.use('layedit', function(){
            var layedit = layui.layedit;
            layedit.build('" . $field . "'); //建立编辑器
        });";
        return $str;
    }

    public static function timejsadd($table)
    {
        $list = Db::query('SHOW FULL FIELDS FROM ' . config('database.connections.mysql.prefix') . $table);
        $list = array_map('array_change_key_case', $list);
        $str = "
        //全局定义一次, 加载xmSelects
        //加载组件
        layui.config({
            base: '../../static/dist/'
        })";
        foreach ($list as $elt => $item) {
            $demo = $item['field'];
            $s = explode('_', $item['field']);
            if (end($s) === 'ids') {
                $data = Db::name(str_replace('_ids', '', $demo))->field('id,name')->select();
                $arr = [];
                if ($data) {
                    foreach ($data as $k => $v) {
                        $arr[$k]['name'] = $v['name'];
                        $arr[$k]['value'] = $v['id'];
                    }
                }
                $filedName = self::controlName(str_replace('_ids', '', $item['field']), false) . 's';
                $str .= "
            .extend({
                xmSelect: 'xm-select'
            }).use(['xmSelect'], function(){
                var xmSelect = layui.xmSelect;
                 var data = {:json_encode(\${$filedName})};
                 var arr = [];
                 for(var key in data){
                     arr[key] = {
                         'name':data[key].name,
                         'value':data[key].id,
                     }
                 }
                //渲染多选
                var " . $demo . " = xmSelect.render({
                    el: '#" . $demo . "',
                    name: '" . $demo . "',
                    data:arr
                });
            });
            ";
            }
        }
        foreach ($list as $elts => $items) {
            if (explode('(', $items['type'])[0] === 'text' && endWith($items['field'],'content')) {
                $str .= "
                //创建{$items['field']}编辑器
                editorArr['{$items['field']}'] = layedit.build('{$items['field']}',{
                    uploadImage:{
                        url:\"{:url('admin/crud/upload')}\",
                        type:'post'
                    }
                });
                form.verify({
                    //content富文本域中的lay-verify值
                    '{$items['field']}': function(value) {
                        return layedit.sync(editorArr['{$items['field']}']);
                    }
                });";
            }
        }

        return $str;
    }

    public static function timejs($table)
    {
        $list = Db::query('SHOW FULL FIELDS FROM ' . config('database.connections.mysql.prefix') . $table);
        $list = array_map('array_change_key_case', $list);
        $str = "
        //全局定义一次, 加载xmSelects
        //加载组件
        layui.config({
            base: '../../static/dist/'
        })";
        foreach ($list as $elt => $item) {
            $demo = $item['field'];
            $s = explode('_', $item['field']);
            if (end($s) === 'ids') {
                $data = Db::name(str_replace('_ids', '', $demo))->field('id,name')->select();
                $arr = [];
                $str1 = "[";
                if ($data) {
                    foreach ($data as $k => $v) {
                        //                        $arr[$k]['name'] = $v['name'];
                        //                        $arr[$k]['value'] = $v['id'];
                        //                        $arr[$k]['selected'] = "{if in_array('" . $v['id'] . "',explode(',',$" . $table . "." . $demo . "))}true{else /}false{/if}";
                        $str1 .= "{\"name\":\"" . $v['name'] . "\",\"value\":\"" . $v['id'] . "\",\"selected\":{if in_array('" . $v['id'] . "',explode(',',$$table." . $item['field'] . "))}true{else /}false{/if}},";
                    }
                }
                $str1 .= "]";
                $filedName = self::controlName(str_replace('_ids', '', $item['field']), false) . 's';
                $filed = '{$'.$table.".".$demo.'}';
                $str .= "
            .extend({
                xmSelect: 'xm-select'
            }).use(['xmSelect'], function(){
                var xmSelect = layui.xmSelect;
                var data = {:json_encode(\${$filedName})};
                var ids = JSON.parse('[$filed]');
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
                //渲染多选
                var " . $demo . " = xmSelect.render({
                    el: '#" . $demo . "',
                    name: '" . $demo . "',
                    data:arr
                });
            });
            ";
            }
        }

        foreach ($list as $elts => $items) {
            if (explode('(', $items['type'])[0] === 'text' && endWith($items['field'],'content')) {
                $str .= "
                //创建{$items['field']}编辑器
                editorArr['{$items['field']}'] = layedit.build('{$items['field']}',{
                    uploadImage:{
                        url:\"{:url('admin/crud/upload')}\",
                        type:'post'
                    }
                });
                form.verify({
                    //content富文本域中的lay-verify值
                    '{$items['field']}': function(value) {
                        return layedit.sync(editorArr['{$items['field']}']);
                    }
                });";
            }
        }

        return $str;
    }

    public static function xm($table)
    {
        $list = Db::query('SHOW FULL FIELDS FROM ' . config('database.connections.mysql.prefix') . $table);
        $list = array_map('array_change_key_case', $list);
        $str = "";
        foreach ($list as $elt => $item) {
            $s = explode('_', $item['field']);
            if (explode('(', $item['type'])[0] === 'datetime') {
                $str .= "laydate.render({ 
                          elem: \"#" . $item['field'] . "\"
                          ,trigger:'click'
                          ,type: 'datetime'
                        });";
            }
            if (explode('(', $item['type'])[0] === 'int' && (end($s) === 'time' || end($s) === 'at' || endWith($item['field'], 'time'))) {
                $str .= "laydate.render({ 
                          elem: \"#" . $item['field'] . "\"
                          ,trigger:'click'
                          ,type: 'datetime'
                        });";
            }
        }
        return $str;
    }


    public static function danxuan($filed, $item)
    {
        $arr = explode(',', explode(':', $item['comment'])[1]);
        $str = "";
        foreach ($arr as $k => $v) {
            $array = explode('=', $v);
            $str .= "<input type=\"radio\" name=\"" . $filed . "\" value=\"$array[0]\" title=\"$array[1]\">";
        }
        return $str;
    }

    public static function danxuanedit($table, $filed, $item)
    {
        $arr = explode(',', explode(':', $item['comment'])[1]);
        $str = "";
        foreach ($arr as $k => $v) {
            $array = explode('=', $v);
            $str .= "<input type=\"radio\" name=\"" . $filed . "\" value=\"$array[0]\" title=\"$array[1]\" {if $" . $table . "." . $filed . " == $array[0]}checked{/if}>";
        }
        return $str;
    }

    public static function xiala($item)
    {
        $arr = explode(',', explode(':', $item['comment'])[1]);
        $str = "";
        foreach ($arr as $k => $v) {
            $array = explode('=', $v);
            $str .= "<option value=\"$array[0]\">$array[1]</option>";
        }
        return $str;
    }

    public static function xialaid($item)
    {
        $arr = Db::name(explode('_', $item['field'])[0])->field('id,name')->select();
        $str = "";
        foreach ($arr as $k => $v) {
            $str .= "<option value=\"" . $v["id"] . "\">" . $v["name"] . "</option>";
        }
        return $str;
    }

    public static function xialaids($item)
    {
        $arr = Db::name(explode('_', $item['field'])[0])->field('id,name')->select();
        $str = "";
        foreach ($arr as $k => $v) {
            $str .= "<option value=\"" . $v["id"] . "\" >" . $v["name"] . "</option>";
        }
        return $str;
    }

    public static function xialaedit($table, $filed, $item)
    {
        $arr = explode(',', explode(':', $item['comment'])[1]);
        $str = "";
        foreach ($arr as $k => $v) {
            $array = explode('=', $v);
            $str .= "<option {if $" . $table . "." . $filed . " == $array[0]}selected=\"\"{/if} value=\"$array[0]\">$array[1]</option>";
        }
        return $str;
    }

    public static function xialaidedit($table, $filed, $item)
    {
        $arr = Db::name(explode('_', $item['field'])[0])->field('id,name')->select();
        $str = "";
        foreach ($arr as $k => $v) {
            $str .= "<option {if $" . $table . "." . $filed . " == " . $v["id"] . "}selected=\"\"{/if} value=\"" . $v["id"] . "\">" . $v["name"] . "</option>";
        }
        return $str;
    }

    public static function duoxuan($filed, $item)
    {
        $arr = explode(',', explode(':', $item['comment'])[1]);
        $str = "";
        foreach ($arr as $k => $v) {
            $array = explode('=', $v);
            $str .= "<input type=\"checkbox\" name=\"" . $filed . "[$array[0]" . "]\" title=\"$array[1]\">";
        }
        return $str;
    }

    public static function duoxuanedit($table, $filed, $item)
    {
        $arr = explode(',', explode(':', $item['comment'])[1]);
        $str = "";
        foreach ($arr as $k => $v) {
            $array = explode('=', $v);
            $str .= "<input {if in_array('" . $array[0] . "',explode(',',$" . $table . "." . $filed . "))}checked=\"\"{/if} type=\"checkbox\" name=\"" . $filed . "[$array[0]" . "]\" title=\"$array[1]\">";
        }
        return $str;
    }

    public static function getsWitchMethod($table)
    {
        $list = Db::query('SHOW FULL FIELDS FROM ' . config('database.connections.mysql.prefix') . $table);
        $list = array_map('array_change_key_case', $list);
        $str = "";
        foreach ($list as $key => $item) {
            if ($item['field'] === 'switch') {
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
