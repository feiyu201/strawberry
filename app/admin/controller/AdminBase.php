<?php
namespace app\admin\controller;

use app\admin\service\ConfigService;
use app\common\constants\AdminConstant;
use think\App;
use app\BaseController;
use think\facade\Session;
use think\facade\View;
use think\facade\Request;
use app\admin\service\SystemLogService;
class AdminBase extends BaseController
{
    /**
     * 无需登录的方法
     * @var array
     */
    protected $noNeedLogin = [];
    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];
    
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->checkLogin();
        $this->checkAuth();
		//var_dump($this->app->view->engine());exit;
        $this->app->view->engine()->layout('layout/defaultnew');
        $this->viewInit();
        // 左侧菜单
        $menus = \app\admin\model\Base::getMenus();

        View::assign(['menus'=>$menus]);
        //var_dump(Session::get('admin'));
    }

    public function menu() 
    {
        
        $menus = \app\admin\model\Base::getMenusJson();

      return json($menus)->header(['contentType'=>'application/json']);
        
    }

    protected function checkLogin()
    {
        if (!Session::has('admin')) {
            $this->error('请登陆', url('login/index'));
        }
        $expireTime = session('admin.expire_time');
        // 判断是否登录过期
        if ($expireTime !== true && time() > $expireTime) {
            session('admin', null);
            $this->error('登录已过期，请重新登录', url('login/index'));
        }
    }

    /**
     * 获取真实IP
     * @return mixed
     */
    protected function getRealIp()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }
        return $ip;
    }

    protected function checkAuth()
    {
        // 获取当前用户
        $admin_id = Session::get('admin.id');
        if (empty($admin_id)) {
            return redirect((string)url('login/index'));
        }
        // 定义方法白名单
        $allow = [
            'Index/index',      // 首页
            'Index/clear',      // 清除缓存
            'Upload/index',     // 上传文件
            'Upload/attachment',    //附件上传
            'Login/index',      // 登录页面
            'Login/signin', // 校验登录
            'Login/logout',     // 退出登录
            'Index/menu'
        ];
        $authRole = \app\common\model\AuthRule::select();
        // 查找当前控制器和方法，控制器首字母大写，方法名首字母小写 如：Index/index
        $addon = $this->request->param('addon');
        if ($addon) {
            //插件权限
            $route = "addons/".$addon."/".Request::controller() . '/' . lcfirst(Request::action());
        } else {
            $route = app('http')->getName()."/".Request::controller() . '/' . lcfirst(Request::action());
        }

        if(Request::controller()){
            $url = Request::url();
            $ip = $this->getRealIp();
            $params = Request::param();
            $data = [
                'admin_id'    => session('admin.id'),
                'url'         => $url,
                'method'      => Request::method(),
                'ip'          => $ip,
                'content'     => json_encode($params, JSON_UNESCAPED_UNICODE),
                'useragent'   => $_SERVER['HTTP_USER_AGENT'],
                'create_time' => time(),
            ];
            SystemLogService::instance()->save($data);
        }
        //var_dump($route);exit();
        $flag = false;
        foreach ($allow as $auth) {
            if (strtolower($auth)==strtolower($route)) {
                $flag = true;
            }
        }
        if ($admin_id != 1&&!$flag) {
            //开始认证
            $auth = new \Auth();
            $result = $auth->check($route, $admin_id);

            $route_two = app('http')->getName()."/".Request::controller() . '/' . lcfirst(Request::action());
            if ($route_two == 'admin/Index/menu') {
                $res_two = true;
            } else {
                $res_two = false;
            }

            if (!$result && $res_two == false) {
                 $this->error('您无此操作权限!', 'javascript:login/index');
            }
        }
    }

    /**
     * 初始化视图参数
     */
    private function viewInit(){
        $request = app()->request;
        $maps = array_keys(config('app.app_map'));

        list($thisModule, $thisController, $thisAction) = [app('http')->getName(), app()->request->controller(), $request->action()];
        list($thisControllerArr, $jsPath) = [explode('.', $thisController), null];
        foreach ($thisControllerArr as $vo) {
            empty($jsPath) ? $jsPath = parse_name($vo) : $jsPath .= '/' . parse_name($vo);
        }

        //echo root_path('public') . "static/{$thisModule}/js/{$jsPath}.js";

        $autoloadJs = file_exists(root_path('public') . "static/{$thisModule}/js/{$jsPath}.js") ? true : false;
        $thisControllerJsPath = "{$thisModule}/js/{$jsPath}.js";
        $data = [
            'thisController'       => parse_name($thisController),
            'thisAction'           => $thisAction,
            'thisRequest'          => parse_name("{$thisModule}/{$thisController}/{$thisAction}"),
            'thisControllerJsPath' => "{$thisControllerJsPath}",
            'autoloadJs'           => $autoloadJs,
            'adminModuleName'                => $maps[0],
            ''
        ];


        View::assign($data);
    }


}
