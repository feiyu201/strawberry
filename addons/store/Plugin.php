<?php
namespace addons\store;	// 注意命名空间规范

use app\common\library\Menu;
use think\Addons;

/**
 * 插件测试
 * @
 */
class Plugin extends Addons	// 需继承think\Addons类
{
	// 该插件的基础信息
	public $info = [
		'name' => 'store',	// 插件标识
		'title' => '商品模块',	// 插件名称
		'description' => '商品模块',	// 插件简介
		'status' => 1,	// 状态
		'author' => 'bytest',
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
                'name'    => 'store',
                'title'   => '商城管理',
                'icon'    => 'fa-shopping-cart',
                'ismenu'  => 1,
                'sublist' => [
                    ['name' => 'addons/store/category/category', 'title' => '商品分类','ismenu'=> 1],
                    ['name' => 'addons/store/brand/list', 'title' => '品牌列表','ismenu'=> 1],
                    ['name' => 'addons/store/goods/list', 'title' => '商品列表','ismenu'=> 1],
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
		
		Menu::delete('store');
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