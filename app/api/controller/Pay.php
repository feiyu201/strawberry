<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\facade\Db;

/**
 * @title 小程序管理接口
 */

class Pay extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ["*"];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ["*"];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * @title    微信小程序支付
     * @author 空城
     * @desc  (描述信息)
     * @method   (POST/GET)
     * @ApiRoute    (/api/pay/payment)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   varchar appid &nbsp; 小程序APPID NO
     * @param   varchar token &nbsp;  NO
     * @param   varchar encodingaeskey &nbsp;  NO
     * @param   tinyint level &nbsp;  NO
     * @param   varchar account &nbsp;  NO
     * @param   varchar original &nbsp;  NO
     * @param   varchar key &nbsp;  NO
     * @param   varchar secret &nbsp;  NO
     * @param   varchar name &nbsp; 小程序名称 NO
     * @param   varchar status &nbsp;  NO
     * @param   int id &nbsp;  NO
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function payment()
    {
        $param = request()->param();

        $order = [
              'out_trade_no' => $param['out_trade_no'],
            'body' => $param['body'],
            'total_fee' => $param['total_fee'],
            'openid' => $param['openid'],
        ];
        
        //小程序支付 wx_miniapp 公众号支付：wx_gzh 具体请参考支付插件说明
        $payres = hook('payhook', ['type'=>'wx_miniapp','order'=>$order]);
        $result = json_decode($payres,true); 

        if ($result){
            $this->success('成功',$result);
        }else{
            $this->error('失败');
        }
    }

    

}