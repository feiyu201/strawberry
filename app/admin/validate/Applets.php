<?php
namespace app\admin\validate;

use think\Validate;

class Applets extends Validate
{
	protected $rule = [
		'appid'  =>  'require|max:50',
		'original'  =>  'require|max:50',
		'secret'  =>  'require|max:50',
		'name'  =>  'require|max:30',
		//'addons'  =>  'require',
	];
	protected $message  =   [
		'appid.require' => 'AppId必须填写',
        'appid.max'     => 'AppId最多不能超过50个字符',
		'original.require' => '原始ID必须填写',
        'original.max'     => '原始ID最多不能超过50个字符',
		'secret.require' => 'AppSecret必须填写',
        'secret.max'     => 'AppSecret最多不能超过50个字符',
		'name.require' => '小程序名称必须填写',
        'name.max'     => '小程序名称最多不能超过30个字符',
		//'addons.require' => '应用必须选择',
	];
	protected $scene = [
		'edit'  =>  ['appid','original','secret','name'],
	];
}