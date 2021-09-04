<?php
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Config as ConfigModel;

class Config extends Api{

    /**
     * 通过key 获取config的某条信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index() {
        $key = $this->request->param("key", "", "trim");

        $groupList = ConfigModel::getGroupList();
        foreach ($groupList as $k => $v) {
            $siteList[$k]['name'] = $k;
            $siteList[$k]['title'] = $v;
        }
        $result = [];
        foreach ((new \app\common\model\Config())->select() as $k => $v) {
            $value = $v->toArray();
            if (!isset($siteList[$value['group']])) {
                continue;
            }
            $value['title'] = __($value['title']);
            if (in_array($value['type'], ['select', 'selects', 'checkbox', 'radio'])) {
                $value['value'] = explode(',', $value['value']);
            }
            $value['content'] = json_decode($value['content'], true);
            $value['tip'] = htmlspecialchars($value['tip']);
            $row = $value;
            $row["groupDes"] = $siteList[$v['group']];
            $result["site.".$value["name"]] = $row;
        }

        if ($key && isset($result[$key])) {
            $result = $result[$key];
        }else{
            $result = array_values($result);
        }

        $this->success('', $result);
    }

}