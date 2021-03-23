<?php

// [ 应用入口文件 ]

namespace think;

require __DIR__.'/../vendor/autoload.php';
// 判断是否安装
if (! is_file('./install.lock')) {
    header("location:/install.php");
    exit;
}

// 定义项目路径
define('ADS_PATH', '../addons/');
// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->name('admin')->run();

$response->send();

$http->end($response);
