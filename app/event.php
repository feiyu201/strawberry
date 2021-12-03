<?php
// 事件定义文件
return [
    'bind'      => [
    ],

    'listen'    => [
        'AppInit'  => [

        ],
        'HttpRun'  => [
        ],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
        'platform_action' => ['addons\platform_manage\event\PlatformManage'],
    ],

    'subscribe' => [
    ],
];
