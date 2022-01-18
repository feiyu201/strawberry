<?php
namespace app\common\controller;

use think\facade\Cache;
use app\common\controller\Api;
use think\exception\HttpResponseException;
use think\facade\Request;
use think\Response;
use app\common\controller\User;

class Auth
{

    protected $allowFields = ['id', 'token', 'avatar', 'nickname'];
    // 不校验token的方法白名单
    protected $pass = ['User/login','User/register'];
    public $token;
    /**
     * 权限Auth
     * @var Auth
     */
    protected $auth = null;

    /**
     * 初始化
     */
    public function __construct()
    {
        $class = app('request')->controller();
        $action = app('request')->action();
        // dump($class.'/'.$action);die;
        $this->token = request()->header('token');
        // 登录完善后需验证token真实性
        if (!$this->token && !in_array($class.'/'.$action, $this->pass)) {
            $this->error('token不能为空');
        }
        $this->auth = User::instance();
        //初始化
        $this->auth->init($this->token);
    }

        /**
     * 操作成功返回的数据
     * @param string $msg 提示信息
     * @param mixed $data 要返回的数据
     * @param int $total 数据总条数
     * @param int $code 错误码，默认为1
     * @param string $type 输出类型
     * @param array $header 发送的 Header 信息
     */
    protected function success($msg = '', $data = [], $total = 0, $jump = 200, $code = 200, $type = 'json', array $header = [])
    {
        $this->callback($msg, $data, $total, $jump, $code, $type, $header);
    }

    /**
     * 操作失败返回的数据
     * @param string $msg 提示信息
     * @param mixed $data 要返回的数据
     * @param int $total 数据总条数
     * @param int $code 错误码，默认为0
     * @param string $type 输出类型
     * @param array $header 发送的 Header 信息
     */
    protected function error($msg = '', $data = [], $total = 0, $jump = 43960, $code = 43960, $type = 'json', array $header = [])
    {
        $this->callback($msg, $data, $total, $jump, $code, $type, $header);
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param mixed $msg 提示信息
     * @param mixed $data 要返回的数据
     * @param int $code 错误码，默认为0
     * @param string $type 输出类型，支持json/xml/jsonp
     * @param array $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    protected function result($msg, $data = [], $code = 0, $type = null, array $header = [])
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'time' => Request::instance()->server('REQUEST_TIME'),
            'data' => $data,
        ];
        // 如果未设置类型则自动判断
        $type = $type ? $type : ($this->request->param(config('var_jsonp_handler')) ? 'jsonp' : $this->responseType);

        if (isset($header['statuscode'])) {
            $code = $header['statuscode'];
            unset($header['statuscode']);
        } else {
            //未设置状态码,根据code值判断
            $code = $code >= 1000 || $code < 200 ? 200 : $code;
        }
        $response = Response::create($result, $type, $code)->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param mixed $message 提示信息
     * @param mixed $data 要返回的数据
     * @param int $code 错误码，默认为0
     * @param string $type 输出类型，支持json/xml/jsonp
     * @param array $header 发送的 Header 信息
     * @param int $total 总数
     * @return void
     * @throws HttpResponseException
     */
    protected function callback($message, $data = [], $total = 0, $jump = 200, $code = 43960, $type = 'json', array $header = [])
    {
        $result = [
            'code' => $code,
            'mark' => $jump,
            'message' => $message,
            'time' => time(),
            'motto' => "#",
            'data' => $data ? $data : [],
            'total' => $data ? $total : 0
        ];
        // 如果未设置类型则自动判断
        // $type = $type ? $type : ($this->request->param(config('var_jsonp_handler')) ? 'jsonp' : $this->responseType);

        if (isset($header['statuscode'])) {
            $code = $header['statuscode'];
            unset($header['statuscode']);
        } else {
            //未设置状态码,根据code值判断
            $code = $code >= 1000 || $code < 200 ? 200 : $code;
        }
        // dump($type);die;
        // 设置头
        $response = Response::create($result, $type, $code)->header($header);
        throw new HttpResponseException($response);
    }
}
