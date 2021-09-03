<?php

namespace addons\aliyun_sms\controller;

use app\common\controller\AddonBase;
use think\exception\HttpResponseException;
use think\Response;

class Base extends AddonBase
{

    /**
     * 返回数据
     * @param $code
     * @param $data
     * @param string $errorMsg
     */
    protected function returnData($code = 0, $msg = 'success', $data = [])
    {
        $data = [
            'code'  => $code,
            'data'  => $data,
            'msg'   => $msg
        ];

        $response = Response::create($data, 'json');
        throw new HttpResponseException($response);
    }

    /**
     * 成功返回数据
     * @param $data
     * @param string $errorMsg
     */
    protected function returnSuccess($msg = 'success', $data = [])
    {

        $code = 0;
        $this->returnData($code, $msg, $data);
    }

    /**
     * 失败返回数据
     * @param $data
     * @param string $errorMsg
     */
    protected function returnError($msg = 'fail', $data = [])
    {

        $code = 1;
        $this->returnData($code, $msg, $data);
    }
}