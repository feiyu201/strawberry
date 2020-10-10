<?php
namespace ric\apidoc;

use think\facade\Request;
use think\facade\View;


class Controller
{

    protected $assets_path = "";
    protected $view_path = '';
    protected $root = '';

    protected $request; # Request 实例
    protected $view;    # 视图类实例

    # 资源类型
    protected $mimeType = [
        'xml'  => 'application/xml,text/xml,application/x-xml',
        'json' => 'application/json,text/x-json,application/jsonrequest,text/json',
        'js'   => 'text/javascript,application/javascript,application/x-javascript',
        'css'  => 'text/css',
        'rss'  => 'application/rss+xml',
        'yaml' => 'application/x-yaml,text/yaml',
        'atom' => 'application/atom+xml',
        'pdf'  => 'application/pdf',
        'text' => 'text/plain',
        'png'  => 'image/png',
        'jpg'  => 'image/jpg,image/jpeg,image/pjpeg',
        'gif'  => 'image/gif',
        'csv'  => 'text/csv',
        'html' => 'text/html,application/xhtml+xml,*/*',
    ];

    public function __construct()
    {
        //有些程序配置了默认json问题
        $this->assets_path = __DIR__ .'/assets/';
        $this->doc = new Doc(config('apidoc'));

        $config     = [
            'view_path'      => __DIR__ . '/view/',
            'default_filter' => ''
        ];

        View::config( [ 'view_path' => __DIR__ . '/view/','view_suffix'  => 'html' ] );

        View::assign('web', $this->doc->__get());


        $this->assets_path = $this->doc->__get("static_path") ?: '/static/doc';
        View::assign('assets', $this->assets_path);
        $this->root = request()->root() ?: request()->domain();

        if (request()->session('doc.is_login') !== $this->doc->__get('password')
            && $this->doc->__get('password')
            && request()->url() !== '/doc/login'
            && stristr(request()->url(), '/assets') == false
        ) {
            session('doc.request_url', Request::url(true));
            header('location:/doc/login');
            exit();
        }

        // 序言文档
        View::assign('document', $this->doc->__get('document'));


        // 分类
        View::assign('versions', $this->doc->__get('api_type'));


        // 左侧菜单
        View::assign('menu', $this->doc->get_api_list(input('version', 0, 'intval')));

    }

    # 解析资源
    public function assets()
    {
        $assets_path = __DIR__ . '/assets/';
        $path        = str_replace("doc/assets", "", request()->pathinfo());
        $ext         = request()->ext();
        if ($ext) {
            $type    = "text/html";
            $content = file_get_contents($assets_path . $path);
            if (array_key_exists($ext, $this->mimeType)) {
                $type = $this->mimeType[$ext];
            }
            return response($content, 200, ['Content-Length' => strlen($content)])->contentType($type);
        }
    }


    /** 显示模板
     */
    protected function template($name, $vars = [], $config = [])
    {
        $vars = array_merge(['root' => $this->root], $vars);
        // print_r(View($name));exit;
        return View($name);

    }


    public function index()
    {
        return $this->template('index');
    }

    public function module($name = '')
    {
        if (class_exists($name)) {
            $reflection = new \ReflectionClass($name);
            $doc_str    = $reflection->getDocComment();
            $doc        = new Parser();
            # 解析类
            $class_doc = $doc->parse_class($doc_str);
            View::assign('data', $class_doc);

        }
        return $this->template('module');
    }

    public function action($name = '')
    {
        if (request()->isAjax()) {
            list($class, $action) = explode("::", $name);
            $data = $this->doc->get_api_detail($class, $action);
            # 全局header
            $data['_header'] = $this->doc->__get('header');
            # 全局参数
            $data['_params'] = $this->doc->__get('params');
            return totrue($data);
        } else {
            return $this->template('action');
        }
    }

    public function document($name = 'explain')
    {
        View::assign('data', $this->doc->__get('document')[$name]);
        return $this->template('doc_' . $name);
    }

    // debug 格式化参数
    public function format_params()
    {
        $header           = $this->format(request()->param('header'));
        $header["Cookie"] = request()->param('cookie');

        $url = request()->param('url');
        $method = request()->param('method');

        $data = $this->format(request()->param('params'));

        if($method == 'API'){
            $arr = explode('::',$url);
            return totrue(api($arr[0],$arr[1],$data));
        }

        return totrue(['params' => $data, 'header' => $header]);
    }

    private function format($data = [])
    {
        if (!$data || count($data) < 1) {
            return [];
        }
        $result = [];
        foreach ($data['name'] as $k => $v) {
            $result[$v] = $data['value'][$k];
        }
        return $result;
    }

    public function login()
    {
        if (request()->isPost()) {
            if (input('post.password') != $this->doc->__get('password')) {
                return redirect('',['密码错误']);
            } else {
                session('doc.is_login', input('post.password'));
                return redirect(session('doc.request_url') ?: '/doc');
            }
        } else {
            if (session('doc.is_login') == $this->doc->__get('password')) {
                header('location:/doc');
            } else {
                return $this->template('login');
            }
        }

    }


}