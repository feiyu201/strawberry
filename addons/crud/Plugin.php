<?php
namespace addons\crud;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Plugin extends Addons
{

    // 该插件的基础信息
    public $info = [
        'name' => 'crud',	// 插件标识
        'title' => '自动生成CRUD',	// 插件名称
        'description' => '自动生成控制器 模型 视图工具',	// 插件简介
        'status' => 1,	// 状态
        'author' => '一笑奈何',
        'version' => '0.1',
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
                'name'    => 'admin/crud/index',
                'title'   => '自动生成CRUD',
                'icon'    => 'fa-list',
                'remark'  => '',
                'ismenu'  => 1,
                'sublist' => [
                    ['name' => 'admin/crud/add', 'title' => '添加'],
                    ['name' => 'admin/crud/edit', 'title' => '编辑 '],
                    ['name' => 'admin/crud/del', 'title' => '删除']
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
        Menu::delete('crud');
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
