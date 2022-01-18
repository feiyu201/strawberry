<?php
namespace app\common\controller;

use think\facade\Cache;
use think\exception\HttpResponseException;
use think\facade\Request;
use think\Response;

class User
{
    protected static $instance = null;
    protected $allowFields = ['id', 'token', 'avatar', 'nickname'];

    public $token;
    //当前登录会员详细信息
    protected $_user  = null;

    /**
     * 初始化
     */
    public function __construct()
    {

    }
    /**
     * 获取示例
     * @param array $options 实例配置
     * @return static
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($options);
        }
        return self::$instance;
    }

    /**
     * 获取User模型
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * 兼容调用user模型的属性
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_user ? $this->_user[$name] : null;
    }

    /**
     * 兼容调用user模型的属性
     */
    public function __isset($name)
    {
        return isset($this->_user) ? isset($this->_user[$name]): false;
    }

    /**
     * 根据Token初始化
     *
     * @param string $token Token
     * @return boolean
     */
    public function init($token)
    {
        $this->setToken($token);
        $data =$this->getUserInfo(true);
        if (!$data) {
            $this->error('用户未登录');
            return false;
        }
        $user_id = intval($data['id']);
        if ($user_id > 0) {
            $this->_user    =$data ;
            return true;
        } else {
            $this->error('用户未登录');
            return false;
        }
    }

    /**
     * 获取token
     */
    public function setToken($token)
    {
        return $this->token = $token;
    }

    /**
     * 设置用户返回数据字段
     */
    public function setAllowFields($field)
    {
        $this->allowFields = $field;
        return $this;       
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo($isError = false)
    {
        $this->userInfo = Cache::get($this->token);
        if (!$this->userInfo) {
            if($isError) {
                $this->error('用户未登录');
            } else {
                return false;
            }
        }
        $allowFields = $this->allowFields;
        $userInfo = array_intersect_key($this->userInfo, array_flip($allowFields));
        return $userInfo;
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
