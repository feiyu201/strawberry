    <?php return [
        // 应用地址
        'app_host'         => env('app.host', ''),
        // 应用的命名空间
        'app_namespace'    => '',
        // 是否启用路由
        'with_route'       => true,
        // 默认应用
        'default_app'      => 'admin',
        // 默认时区
        'default_timezone' => 'Asia/Shanghai',
    
        // 应用映射（自动多应用模式有效）
        'app_map'               => ['wkv4wp6o' => 'admin'],
        // 域名绑定（自动多应用模式有效）
        'domain_bind'      => [],
        // 禁止URL访问的应用列表（自动多应用模式有效）
        'deny_app_list'    => ['common','admin'],
    
        // 异常页面的模板文件
        'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',
    
        // 错误显示信息,非调试模式有效
        'error_message'    => '页面错误！请稍后再试～',
        // 显示错误信息
        'show_error_msg'   => true,
        // 默认跳转页面对应的模板文件【新增】
        'dispatch_success_tmpl' => app()->getRootPath() . '/public/tpl/dispatch_jump.tpl',
        'dispatch_error_tmpl'  => app()->getRootPath() . '/public/tpl/dispatch_jump.tpl',
    ];