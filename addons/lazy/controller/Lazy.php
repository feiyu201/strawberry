<?php
namespace addons\lazy\controller;

use app\common\controller\AddonBase;
use think\exception\PDOException;
use think\facade\Db;
use think\Exception;

/**
 * Api自动生成
 *
 * @icon fa fa-circle-o
 */
class Lazy extends AddonBase
{
    public function index()
    {
        return $this->fetch();
    }

    public function getList()
    {
        $page = $this->request->param('page', 1, 'intval');
        $limit = $this->request->param('limit', 10, 'intval');
        $count = Db::name('lazy')->count();
        $data = Db::name('lazy')->page($page, $limit)->select()->each(function ($item, $k) {
            $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            $item['admin_id'] = Db::name('admin')->where('id', $item['admin_id'])->field('username')->find()['username'];
            return $item;
        });

        return json([
            'code' => 0,
            'count' => $count,
            'data' => $data,
            'msg' => '查询成功'
        ]);
    }

    public function edit()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (Db::name('lazy')->where('id', $data['id'])->delete()) {
                self::add();
                $this->success("编辑成功");
            } else {
                $this->error("编辑失败");
            }
        }
        $id = $this->request->param('id');
        if (!$id) {
            $this->success("参数错误");
        }
        $wxappinfo = Db::name('lazy')->where('id', $id)->find();
        if (!$wxappinfo) {
            $this->error("参数错误");
        }
        $this::assign('wxappinfo', $wxappinfo);
        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $admin = session('admin');
            $params = $this->request->post();
            $params['create_time'] = time();
            $params['admin_id'] = $admin['id'];
            $table = $params['table_name'];
            $sql = "show tables like '" . config('database.connections.mysql.prefix') . "{$table}'";
            $istable = Db::query($sql);
            if (!$istable) {
                $this->error('Table name does not exist');
            }

            $sql = 'show table status';
            $tableList = Db::query($sql);
            $tableList = array_map('array_change_key_case', $tableList);
            $fix = "";
            foreach ($tableList as $key => $value) {
                if ($value['name'] === config('database.connections.mysql.prefix') . $table) {
                    $fix = $value['comment'];
                }
            }

            $list = Db::query('SHOW FULL FIELDS FROM ' . config('database.connections.mysql.prefix') . $table);
            $list = array_map('array_change_key_case', $list);
            // 查询主键
            $primary = "";
            foreach ($list as $elt => $value) {
                if ($value['key'] === 'PRI') {
                    $primary = $value['field'];
                }
            }
            array_shift($list);
            if (!$primary) {
                $this->error('Please add a primary key for this table');
            }

            $res = (new \addons\lazy\model\Lazy())->where('table_name', $table)->count();
            if ($res) {
                $this->error('The table Api interface has been generated');
            }

            if ($params) {
//                $params = $this->preExcludeFields($params);
//
//                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
//                    $params[$this->dataLimitField] = $this->auth->id;
//                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
//                    if ($this->modelValidate) {
//                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
//                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
//                        $this->model->validateFailException(true)->validate($validate);
//                    }
                    $result = (new \addons\lazy\model\Lazy())->save($params);
                    // 生成控制器
                    $controlFile = fopen("../app/api/controller/" . self::controlName($table) . ".php", "w");
                    $controlTxt = sprintf(
                        self::getFile('control'),
                        $fix,
                        self::controlName($table),
                        // 添加
                        self::controlName($table),
                        self::annotateNoPrimary($list),
                        self::modelName($table),
                        // 编辑
                        self::controlName($table),
                        self::annotate($list, $primary),
                        self::modelName($table),
                        // 查询单条
                        self::controlName($table),
                        $primary,
                        $primary,
                        self::primary($primary) . self::apiReturnParams($list, $primary),
                        $table,
                        $primary,
                        self::withInfo($list),
                        // 查询列表
                        self::controlName($table),
                        self::annotate($list, $primary) . self::apiReturnParams($list, $primary),
                        self::search($list),
                        $table,
                        self::withList($list),
                        // 删除
                        self::controlName($table),
                        $primary,
                        $primary,
                        self::primary($primary),
                        $table,
                        $primary
                    );
                    fwrite($controlFile, $controlTxt);
                    fclose($controlFile);

                    // 生成model
                    $modelFile = fopen("../app/common/model/" . self::modelName($table) . ".php", "w");
                    $modelText = sprintf(self::getFile('model'), self::modelName($table));
                    fwrite($modelFile, $modelText);
                    fclose($modelFile);

                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error('No rows were inserted');
                }
            }
            $this->error('Parameter %s can not be empty', '');
        }
        return $this->fetch();
    }


    // 生成注释(有主键)
    protected static function annotate(&$data, $primary)
    {
        $str = '* @param   int '
            . $primary . ' '
            . 'null 主键' . $primary . ' ' . 'Yes';
        foreach ($data as $elt => $v) {
            // 换行 且需要留5个空格
            $str = $str . "\r\n" . '     * @param   '
                . self::delString($v['type']) . ' '
                . $v['field'] . ' '
                . 'null '
                . $v['comment'] . ' '
                . $v['null'];
        }
        return $str;
    }

    // 生成注释(无主键)
    protected static function annotateNoPrimary(&$data)
    {
        $str = "";
        foreach ($data as $elt => $v) {
            if ($elt == 0) {
                $str = $str
                    . '* @param   ' . self::delString($v['type']) . ' '
                    . $v['field'] . ' '
                    . 'null '
                    . $v['comment'] . ' '
                    . $v['null'];
            } else {
                // 换行 且需要留5个空格
                $str = $str . "\r\n" . '     * @param   '
                    . self::delString($v['type']) . ' '
                    . $v['field'] . ' '
                    . 'null '
                    . $v['comment'] . ' '
                    . $v['null'];
            }
        }
        return $str;
    }

    public function delete()
    {
        $idsStr = $id = $this->request->param('idsStr');
        if (!$idsStr) {
            $this->success("参数错误");
        }
        if (Db::name('lazy')->where('id', 'in', $idsStr)->delete()) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

    // 控制器命名
    protected static function controlName($table)
    {
        return ucwords(str_replace('_', '', $table));
    }

    // 模型命名
    protected static function modelName($table)
    {
        $arr = explode('_', $table);
        $modelName = "";
        foreach ($arr as $item) {
            $modelName = $modelName . self::controlName($item);
        }
        return $modelName;
    }

    // 删除（）中的字符
    protected static function delString($str)
    {
        $str = explode(' ', $str)[0];
        $location = strpos($str, '(');

        //修复没有括号的字段类型不生成注释的问题
        if (!empty($location)) {
            $str = substr($str, '0', $location);
        }
       
        return $str;
    }

    // 获取文件内容
    protected static function getFile($name)
    {
        $file_path = ADS_PATH . "lazy/" . $name . ".txt";
        if (file_exists($file_path)) {
            $fp = fopen($file_path, "r");
            $str = fread($fp, filesize($file_path));//指定读取大小，这里把整个文件内容读取出来
        }
        return $str;
    }

    // 主键
    protected static function primary($primary)
    {
        $str = '* @param   int '
            . $primary . ' '
            . 'null 主键' . $primary . ' ' . 'Yes';
        return $str;
    }

    // 返回参数
    protected static function apiReturnParams($data, $primary, $type = true)
    {
        $str = ($type ? "\r\n     " : "") . '* @return   int ' . $primary . ' null 主键' . $primary;
        ;
        foreach ($data as $elt => $v) {
            // 换行 且需要留5个空格
            $str = $str . "\r\n" . '     * @return   '
                . self::delString($v['type']) . ' '
                . $v['field'] . ' '
                . 'null '
                . $v['comment'] . ' '
                . $v['null'];
        }
        return $str;
    }

    // 列表搜索
    protected static function search($data)
    {
        $str = '$where = [];' . "\r\n";
        foreach ($data as $elt => $v) {
            $str = $str . '        $' . $v["field"] . '=' . 'request()->param("' . $v["field"] . '");' . "\r\n";
        }
        foreach ($data as $k => $v) {
            if (strpos($v['field'], 'time')) {
                $str = $str . '        if (request()->param("start' . $v["field"] . '") && request()->param("end' . $v["field"] . '"))$where["' . $v["field"] . '"] = [[\'>=\', request()->param("start' . $v["field"] . '")], [\'<=\', request()->param("end' . $v["field"] . '")], \'and\'];' . "\r\n";
            } else {
                $str = $str . '        if (' . '$' . $v["field"] . ')$where[] = [\'' . $v["field"]. '\', \'like\', \'%\' .$' . $v["field"] . '. \'%\'];' . "\r\n";
            }
        }
        return $str;
    }

    //
    protected static function withInfo($list)
    {
        $tableModel = false;
        $tableModels = false;
        $str = "";
        foreach ($list as $elt => $item) {
            if (substr($item['field'], -3) === '_id') {
                $tableModel = substr($item['field'], 0, -3);
            } elseif (substr($item['field'], -4) === '_ids') {
                $tableModels = substr($item['field'], 0, -4);
            } elseif (endWith($item['field'], 'img')||endWith($item['field'], 'image')) {
                $str .= '$result["' . $item['field'] . '"] = url($result["' . $item['field'] . '"],[],null,true)->build();'.PHP_EOL;
            } elseif (endWith($item['field'], 'imgs')||endWith($item['field'], 'images')||endWith($item['field'], 'file')) {
                $str .= '$result["' . $item['field'] . '"] = explode("|",$result["' . $item['field'] . '"]);'.PHP_EOL;
                $field = $item['field'];
                $str .= <<<EOT
            \$result['{$field}'] = array_map(function(\$item){
                return url(\$item,[],null,true)->build();
            },\$result['{$field}']);
EOT;
            } else {
                continue;
            }
        }
       
        if ($tableModel) {
            $str .= '$result["' . $tableModel . '_name"] = Db::name("' . $tableModel . '")->where("id",$result["' . $tableModel . '_id"])->field(\'name\')->find()[\'name\'];';
        }
        if ($tableModels) {
            $str .= "\r\n" . '        $result["' . $tableModels . '_names"] = implode(",",Db::name("' . $tableModels . '")->where([["id", "in",explode(",",$result["' . $tableModels . '_ids"])]])->column(\'name\'));';
        }
        return $str;
    }

    //
    public static function withList($list)
    {
        $tableModel = false;
        $tableModels = false;

        $str = 'foreach($result as $elt => $item){' . "\r\n";
        foreach ($list as $elt => $item) {
            if (substr($item['field'], -3) === '_id') {
                $tableModel = substr($item['field'], 0, -3);
            } elseif (substr($item['field'], -4) === '_ids') {
                $tableModels = substr($item['field'], 0, -4);
            } elseif (endWith($item['field'], 'img')||endWith($item['field'], 'image')) {
                $str .= '$result[$elt]["' . $item['field'] . '"] = url($item["' . $item['field'] . '"],[],null,true)->build();'.PHP_EOL;
            } elseif (endWith($item['field'], 'imgs')||endWith($item['field'], 'images')||endWith($item['field'], 'file')) {
                $str .= '$result[$elt]["' . $item['field'] . '"] = explode("|",$item["' . $item['field'] . '"]);'.PHP_EOL;
                $field = $item['field'];
                $str .= <<<EOT
            \$result[\$elt]['{$field}'] = array_map(function(\$item){
                return url(\$item,[],null,true)->build();
            },\$result[\$elt]['{$field}']);
EOT;
            } else {
                continue;
            }
        }
        if ($tableModel) {
            $str .= "\r\n" . '            $result[$elt]["' . $tableModel . '_name"] = Db::name("' . $tableModel . '")->where("id",$item["' . $tableModel . '_id"])->field(\'name\')->find()[\'name\'];';
        }
        if ($tableModels) {
            $str .= "\r\n" . '            $result[$elt]["' . $tableModels . '_names"] = implode(",",Db::name("' . $tableModels . '")->where([["id", "in",explode(",",$item["' . $tableModels . '_ids"])]])->column(\'name\'));';
        }
        $str .= "\r\n" . '        }';
        return $str;
    }

    /**
     * 排除前台提交过来的字段
     * @param $params
     * @return array
     */
    protected function preExcludeFields($params)
    {
        if (is_array($this->excludeFields)) {
            foreach ($this->excludeFields as $field) {
                if (key_exists($field, $params)) {
                    unset($params[$field]);
                }
            }
        } else {
            if (key_exists($this->excludeFields, $params)) {
                unset($params[$this->excludeFields]);
            }
        }
        return $params;
    }
}
