<?php
namespace addons\platform_manage;

use app\common\library\Menu;
use think\Addons;

/**
 * 个人建简历插件
 * @
 */
class Plugin extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'platform_manage',	// 插件标识
        'title' => '第三方平台管理',	// 插件名称
        'description' => '抖音微信小程序等第三方平台的管理',	// 插件简介
        'status' => 1,	// 状态
        'author' => 'delong',
        'version' => '0.1',
        'install'     => 1,                 // 是否已安装[1 已安装，0 未安装]
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        if (is_file($this->app->getBasePath() . 'event.php')) {
            $event = include $this->app->getBasePath() . 'event.php';
            $event['listen']['platform_action'] = ['addons\platform_manage\event\PlatformManage'];
            $eventStr = var_export($event, true);
            $config = <<<EOT
<?php
return {$eventStr}
?>
EOT;
            file_put_contents($this->app->getBasePath() . 'event.php', $config,LOCK_EX);
        }

        $menu = [
            [
                'name'    => '/addons/platform_manage/index/index',
                'title'   => '第三方平台管理',
                'icon'    => '&#xe66f;',
                'ismenu'  => 1,//是否是菜单
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
        if (is_file($this->app->getBasePath() . 'event.php')) {
            $event = include $this->app->getBasePath() . 'event.php';
            $event['listen']['platform_action'] = [];
            $eventStr = var_export($event, true);
            $config = <<<EOT
<?php
return {$eventStr}
?>
EOT;
            file_put_contents($this->app->getBasePath() . 'event.php', $config,LOCK_EX);
        }


        Menu::delete('/addons/platform_manage/index/index');
        return true;
    }

}