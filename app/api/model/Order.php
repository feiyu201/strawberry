<?php

namespace app\api\model;

use think\Model;

class Order extends Model
{
    //订单状态:1待付款 2待发货(已付款OR拼团成功) 3配送中 4待评价(已收货) 5已完成 6已取消
    const UNPAID    = 1;//未支付
    const PAID      = 2;//已支付
    //付款方式 1微信 2支付宝 3余额
    const WECHAT_WAY = 1;
    // 表名
    protected $name = 'order';

}