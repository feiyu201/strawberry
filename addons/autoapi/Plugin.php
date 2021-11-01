<?php
namespace addons\autoapi;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Plugin extends Addons
{

    // 该插件的基础信息
    public $info = [
        'name' => 'autoapi',	// 插件标识
        'title' => 'Api自动生成高级本',	// 插件名称
        'description' => 'Api接口自动生成插件高级版',	// 插件简介
        'status' => 1,	// 状态
        'author' => '官方',
        'version' => '1.0.0',
        'install'     => 0,                 // 是否已安装[1 已安装，0 未安装]
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'autoapi',
                'title'   => '自动生成(api)',
                'icon'    => 'fa-list',
                'remark'  => '',
                'ismenu'  => 1,
                'sublist' => [
                    ['name' => 'addons/autoapi/autoapi/index', 'title' => '列表','ismenu'  => 1,],
                    ['name' => 'addons/autoapi/autoapi/add', 'title' => '添加'],
                    ['name' => 'addons/autoapi/autoapi/edit', 'title' => '编辑 '],
                    ['name' => 'addons/autoapi/autoapi/del', 'title' => '删除']
                ]
            ]
        ];
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('autoapi');
        return true;
    }

    /**
     * 实现的testhook钩子方法
     * @return mixed
     */
    public function testhook($param)
    {
        // 调用钩子时候的参数信息
        print_r($param);
        // 当前插件的配置信息，配置信息存在当前目录的config.php文件中，见下方
        print_r($this->getConfig());
        // 可以返回模板，模板文件默认读取的为插件目录中的文件。模板名不能为空！
        return $this->fetch('info');
    }

}
