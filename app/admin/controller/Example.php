<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Db;
use think\facade\Session;

class Example extends BaseController
{
    public function select()
    {
        if ($this->request->isAjax()) {
            $list = [
                ['user_id' => 1, 'name' => '武则天'],
                ['user_id' => 2, 'name' => '小乔'],
                ['user_id' => 3, 'name' => '司马懿'],
                ['user_id' => 4, 'name' => '妲己'],
                ['user_id' => 5, 'name' => '张良'],
            ];
            $data = [
                'code'  =>  1,
                'msg'   =>  null,
                'data'  =>  $list,
            ];
            return json($data);
        }
        return View::fetch();
    }
    public function editor()
    {
        return View::fetch();
    }
    public function switchto()
    {
        return View::fetch();
    }
    public function citypicker()
    {
        return View::fetch();
    }
    public function uploadimg()
    {
        return View::fetch();
    }
}
