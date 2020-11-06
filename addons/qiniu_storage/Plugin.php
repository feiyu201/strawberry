<?php

namespace addons\qiniu_storage;
// 注意命名空间规范

use app\common\library\Menu;
use think\Addons;

/**
 * 插件测试
 * @
 */
class Plugin extends Addons    // 需继承think\Addons类
{
    const NAME = 'qiniu_storage';
    const TITLE = '七牛云存储';
    const DESCRIPTION = '七牛云存储(参数：存储空间名称 Bucket ACCESS_KEY SECRET_KEY 空间域名)';
    const ICON = 'fa-list';
    const SUBLIST_NAME = 'addons/qiniu_storage/index/';

    // 该插件的基础信息
    public $info = [
        'name'        => SELF::NAME,    // 插件标识
        'title'       => SELF::TITLE,    // 插件名称
        'description' => SELF::DESCRIPTION,    // 插件简介
        'status'      => 1,    // 状态
        'author'      => 'Eden',
        'version'     => '1.0',
        'install'     => 1,                 // 是否已安装[1 已安装，0 未安装]
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => SELF::NAME,
                'title'   => SELF::TITLE,
                'icon'    => SELF::ICON,
                'ismenu'  => 1,//是否是菜单
                'sublist' => [
                    ['name' => SELF::SUBLIST_NAME . 'index', 'title' => '查看', 'ismenu' => 1],
                    ['name' => SELF::SUBLIST_NAME . 'add', 'title' => '添加', 'ismenu' => 1],
                    ['name' => SELF::SUBLIST_NAME . 'detail', 'title' => '详情', 'ismenu' => 1],
                ]
            ]
        ];
//        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {

        Menu::delete(SELF::NAME);
        return true;
    }

    /**
     * 实现的qiniu_cloud_storage钩子方法
     * @return mixed
     */
    public function qiniu_cloud_storage_hook($param)
    {
        // 调用钩子时候的参数信息
        print_r($param);
        // 当前插件的配置信息，配置信息存在当前目录的config.php文件中，见下方
        print_r($this->getConfig());
        // 可以返回模板，模板文件默认读取的为插件目录中的文件。模板名不能为空！
        return $this->fetch('info1');

        $config = $this->getConfig();

    }

    /**
     * 应用类插件的入口方法，获取系统用户，小程序信息，然后做子应用处理
     */
    public function welcome()
    {
        echo SELF::TITLE;
//        global $_W;
//        var_dump($_W);
//        //todo 插件内跳转等操作
//        $uri = $_W['siteroot'] . 'addons/' . $_W['current_module']['name'] . '/index.php' . '?r=mall/we7-entry/login';
//        header('Location: ' . $uri);
    }

}