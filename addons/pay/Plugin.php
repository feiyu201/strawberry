<?php
namespace addons\pay;	// 注意命名空间规范

use app\common\library\Menu;
use think\Addons;

use fast\ZfbPay;
use fast\WxPay;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

/**
 * 插件测试
 * @
 */
class Plugin extends Addons	// 需继承think\Addons类
{
	// 该插件的基础信息
	public $info = [
		'name' => 'pay',	// 插件标识
		'title' => '支付插件',	// 插件名称
		'description' => '支付插件',	// 插件简介
		'status' => 1,	// 状态
		'author' => 'bykogncheng',
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
                'name'    => 'pay',
                'title'   => '支付插件',
                'icon'    => '&#xe66f;',
                'ismenu'  => 0,
                'sublist' => [
                    ['name' => 'addons/pay/index/index', 'title' => '查看'],
                    ['name' => 'addons/pay/index/add', 'title' => '添加'],
                    ['name' => 'addons/pay/index/detail', 'title' => '详情'],
                ]
            ]
        ];
        //Menu::create($menu);
		return true;
	}

	/**
	 * 插件卸载方法
	 * @return bool
	 */
	public function uninstall()
	{
		
		Menu::delete('pay');
		return true;
	}

	/**
	 * 实现的testhook钩子方法
	 * @return mixed
	 */
	public function payhook($param)
	{
		
		// 调用钩子时候的参数信息
		//print_r($param);
		// 当前插件的配置信息，配置信息存在当前目录的config.php文件中，见下方
		//print_r($this->getConfig());
		// 可以返回模板，模板文件默认读取的为插件目录中的文件。模板名不能为空！
		//return $this->fetch('info');

		$config = $this->getConfig();

		//微信扫码支付
		if($param['type'] == 'wx_scan')
		{

			$info = ['appid'=>$config['appappid'],'app_id'=>$config['gzhappid'],'miniapp_id'=>$config['xcxaapid'],'mch_id'=>$config['mch_id'],'key'=>$config['wxkey'],'notify_url'=>$config['wx_notify_url']];
			$configeasy = [
				'appid' => $info['appid'], // APP APPID
				'app_id' => $info['app_id'], // 公众号 APPID
				'miniapp_id' => $info['miniapp_id'], // 小程序 APPID
				'mch_id' => $info['mch_id'],
				'key' => $info['key'],
				'notify_url' => $info['notify_url'],
				'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
				'cert_key' => './cert/apiclient_key.pem',// optional，退款等情况时用到
				'log' => [ // optional
					'file' => './logs/wechat.log',
					'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
					'type' => 'single', // optional, 可选 daily.
					'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
				],
				'http' => [ // optional
					'timeout' => 5.0,
					'connect_timeout' => 5.0,
					// 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
				],
				'mode' => 'normal', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
			];
	
			$pay = Pay::wechat($configeasy)->scan($param['order']);
			print_r($pay);
		}

		//微信小程序支付
		if($param['type'] == 'wx_miniapp')
		{

			$info = ['appid'=>$config['appappid'],'app_id'=>$config['gzhappid'],'miniapp_id'=>$config['xcxaapid'],'mch_id'=>$config['mch_id'],'key'=>$config['wxkey'],'notify_url'=>$config['wx_notify_url']];
			$configeasy = [
				'appid' => $info['appid'], // APP APPID
				'app_id' => $info['app_id'], // 公众号 APPID
				'miniapp_id' => $info['miniapp_id'], // 小程序 APPID
				'mch_id' => $info['mch_id'],
				'key' => $info['key'],
				'notify_url' => $info['notify_url'],
				'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
				'cert_key' => './cert/apiclient_key.pem',// optional，退款等情况时用到
				'log' => [ // optional
					'file' => './logs/wechat.log',
					'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
					'type' => 'single', // optional, 可选 daily.
					'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
				],
				'http' => [ // optional
					'timeout' => 5.0,
					'connect_timeout' => 5.0,
					// 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
				],
				'mode' => 'normal', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
			];
	
			$pay = Pay::wechat($configeasy)->miniapp($param['order']);
			print_r($pay);
		}

		//微信app支付
		if($param['type'] == 'wx_app')
		{

			$info = ['appid'=>$config['appappid'],'app_id'=>$config['gzhappid'],'miniapp_id'=>$config['xcxaapid'],'mch_id'=>$config['mch_id'],'key'=>$config['wxkey'],'notify_url'=>$config['wx_notify_url']];
			$configeasy = [
				'appid' => $info['appid'], // APP APPID
				'app_id' => $info['app_id'], // 公众号 APPID
				'miniapp_id' => $info['miniapp_id'], // 小程序 APPID
				'mch_id' => $info['mch_id'],
				'key' => $info['key'],
				'notify_url' => $info['notify_url'],
				'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
				'cert_key' => './cert/apiclient_key.pem',// optional，退款等情况时用到
				'log' => [ // optional
					'file' => './logs/wechat.log',
					'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
					'type' => 'single', // optional, 可选 daily.
					'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
				],
				'http' => [ // optional
					'timeout' => 5.0,
					'connect_timeout' => 5.0,
					// 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
				],
				'mode' => 'normal', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
			];
	
			$pay = Pay::wechat($configeasy)->app($param['order']);
			print_r($pay);
		}

		//微信公众号支付
		if($param['type'] == 'wx_gzh')
		{

			$info = ['appid'=>$config['appappid'],'app_id'=>$config['gzhappid'],'miniapp_id'=>$config['xcxaapid'],'mch_id'=>$config['mch_id'],'key'=>$config['wxkey'],'notify_url'=>$config['wx_notify_url']];
			$configeasy = [
				'appid' => $info['appid'], // APP APPID
				'app_id' => $info['app_id'], // 公众号 APPID
				'miniapp_id' => $info['miniapp_id'], // 小程序 APPID
				'mch_id' => $info['mch_id'],
				'key' => $info['key'],
				'notify_url' => $info['notify_url'],
				'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
				'cert_key' => './cert/apiclient_key.pem',// optional，退款等情况时用到
				'log' => [ // optional
					'file' => './logs/wechat.log',
					'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
					'type' => 'single', // optional, 可选 daily.
					'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
				],
				'http' => [ // optional
					'timeout' => 5.0,
					'connect_timeout' => 5.0,
					// 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
				],
				'mode' => 'normal', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
			];
	
			$pay = Pay::wechat($configeasy)->mp($param['order']);
			return $pay;
		}


		//支付宝扫码支付==========================================================================
		if($param['type'] == 'zfb_scan')
		{
			$configeasy = [
				'app_id' => $config['zfb_app_id'],
				'notify_url' => $config['zfb_notify_url'],
				'return_url' => $config['zfb_return_url'],
				'ali_public_key' => $config['zfb_ali_public_key'],
				// 加密方式： **RSA2**  
				'private_key' => $config['zfb_private_key'],
				// 使用公钥证书模式，请配置下面两个参数，同时修改ali_public_key为以.crt结尾的支付宝公钥证书路径，如（./cert/alipayCertPublicKey_RSA2.crt）
				// 'app_cert_public_key' => './cert/appCertPublicKey.crt', //应用公钥证书路径
				// 'alipay_root_cert' => './cert/alipayRootCert.crt', //支付宝根证书路径
				'log' => [ // optional
					'file' => './logs/alipay.log',
					'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
					'type' => 'single', // optional, 可选 daily.
					'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
				],
				'http' => [ // optional
					'timeout' => 5.0,
					'connect_timeout' => 5.0,
					// 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
				],
				'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
			];

			$pay = Pay::alipay($configeasy)->scan($param['order']);
			print_r($pay);
		}

		//支付宝小程序支付==========================================================================
		if($param['type'] == 'zfb_mini')
		{
			$configeasy = [
				'app_id' => $config['zfb_app_id'],
				'notify_url' => $config['zfb_notify_url'],
				'return_url' => $config['zfb_return_url'],
				'ali_public_key' => $config['zfb_ali_public_key'],
				// 加密方式： **RSA2**  
				'private_key' => $config['zfb_private_key'],
				// 使用公钥证书模式，请配置下面两个参数，同时修改ali_public_key为以.crt结尾的支付宝公钥证书路径，如（./cert/alipayCertPublicKey_RSA2.crt）
				// 'app_cert_public_key' => './cert/appCertPublicKey.crt', //应用公钥证书路径
				// 'alipay_root_cert' => './cert/alipayRootCert.crt', //支付宝根证书路径
				'log' => [ // optional
					'file' => './logs/alipay.log',
					'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
					'type' => 'single', // optional, 可选 daily.
					'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
				],
				'http' => [ // optional
					'timeout' => 5.0,
					'connect_timeout' => 5.0,
					// 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
				],
				'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
			];

			$pay = Pay::alipay($configeasy)->mini($param['order']);
			print_r($pay);
		}

		//支付宝app支付==========================================================================
		if($param['type'] == 'zfb_app')
		{
			$configeasy = [
				'app_id' => $config['zfb_app_id'],
				'notify_url' => $config['zfb_notify_url'],
				'return_url' => $config['zfb_return_url'],
				'ali_public_key' => $config['zfb_ali_public_key'],
				// 加密方式： **RSA2**  
				'private_key' => $config['zfb_private_key'],
				// 使用公钥证书模式，请配置下面两个参数，同时修改ali_public_key为以.crt结尾的支付宝公钥证书路径，如（./cert/alipayCertPublicKey_RSA2.crt）
				// 'app_cert_public_key' => './cert/appCertPublicKey.crt', //应用公钥证书路径
				// 'alipay_root_cert' => './cert/alipayRootCert.crt', //支付宝根证书路径
				'log' => [ // optional
					'file' => './logs/alipay.log',
					'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
					'type' => 'single', // optional, 可选 daily.
					'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
				],
				'http' => [ // optional
					'timeout' => 5.0,
					'connect_timeout' => 5.0,
					// 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
				],
				'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
			];

			$pay = Pay::alipay($configeasy)->app($param['order']);
			print_r($pay);
		}

		exit('插件钩子传参错误，请检查');

	}

}