<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\facade\Db;
use app\api\model\Order;

//总订单表

/**
 * 支付回调方法
 */
class PayNotify extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ["*"];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ["*"];

    //回调参数
    protected $notify_data;
    //订单分隔符
    protected $order_type_symbol = '_';
    //订单类型
    protected $order_type;
    //订单金额
    protected $total_fee;
    //订单号 生成订单时随机生成带订单类型前缀
    protected $order_no;
    //总表订单信息
    protected $all_order_info;
    //子订单信息表
    protected $order_info;
    public function _initialize()
    {
        parent::_initialize();

    }

    //处理微信支付回调
    /***************************微信回调返回*****************/
    /** 接收到回调的原生数据
    $pay_xml = '<xml>
                <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
                <attach><![CDATA[支付测试]]></attach>
                <bank_type><![CDATA[CFT]]></bank_type>
                <fee_type><![CDATA[CNY]]></fee_type>
                <is_subscribe><![CDATA[Y]]></is_subscribe>
                <mch_id><![CDATA[10000100]]></mch_id>
                <nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
                <openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
                <out_trade_no><![CDATA[ES_1409811653]]></out_trade_no>
                <result_code><![CDATA[SUCCESS]]></result_code>
                <return_code><![CDATA[SUCCESS]]></return_code>
                <sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
                <time_end><![CDATA[20140903131540]]></time_end>
                <total_fee>1</total_fee>
                <coupon_fee><![CDATA[10]]></coupon_fee>
                <coupon_count><![CDATA[1]]></coupon_count>
                <coupon_type><![CDATA[CASH]]></coupon_type>
                <coupon_id><![CDATA[10000]]></coupon_id>
                <trade_type><![CDATA[JSAPI]]></trade_type>
                <transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
                </xml>'

    /** 解析出来的数组
    $result =  [
                 "appid" => "wx2421b1c4370ec43b"
                 "attach" => "支付测试"
                 "bank_type" => "CFT"
                 "fee_type" => "CNY"
                 "is_subscribe" => "Y"
                 "mch_id" => "10000100"
                 "nonce_str" => "5d2b6c2a8db53831f7eda20af46e531c"
                 "openid" => "oUpF8uMEb4qRXf22hE3X68TekukE"
                 "out_trade_no" => "ES_1409811653"
                 "result_code" => "SUCCESS"
                 "return_code" => "SUCCESS"
                 "sign" => "B552ED6B279343CB493C5DD0D78AB241"
                 "time_end" => "20140903131540"
                 "total_fee" => "1"
                 "coupon_fee" => "10"
                 "coupon_count" => "1"
                 "coupon_type" => "CASH"
                 "coupon_id" => "10000"
                 "trade_type" => "JSAPI"
                 "transaction_id" => "1004400740201409030005092168"
                ]
     *
     ***************************微信回调返回*****************/
    public function wechat_pay_notify()
    {
        $pay_xml = file_get_contents("php://input");//获取文件流
        $this->pay_log('微信回调原始数据', $pay_xml);
        $pay_xml = json_encode(simplexml_load_string($pay_xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        $result  = json_decode($pay_xml, true);//转成数组
        $this->pay_log('微信回调数组转换', $result);
        //如果成功返回了
        if ($result) {
            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                //执行业务逻辑改变订单状态等操作
                $this->notify_data = $result;//array 结果
                $this->order_no    = $this->notify_data['out_trade_no'];//对外订单号
                $this->total_fee   = ($this->notify_data['total_fee'] / 100);//总金额 微信单位是：分
//                //验证总订单
//                if ($msg = $this->order_check()) {
//                    return $this->wechat_notify_pay_result($msg,'fail');
//                }
                dd($this->assign_commission());
                //分别处理不同业务逻辑订单
                $out_trade_no_explode = explode($this->order_type_symbol, $this->order_no);//分割订单号 符号分割
                switch ($out_trade_no_explode[0]) {
                    case 'ES' : //房产预约支付订单
                        $this->order_type = 'ES';
                        $res = $this->pay_notify_estate_order();
                        break;
                    case 'AG' : //代理商支付订单
                        $this->order_type = 'AG';
                        $res = $this->pay_notify_agent_order();
                        break;
                    default : //普通订单操作
//                        $res = $this->pay_notify_all_order();
                        $res = '订单/类型不存在';
                        break;
                }
                if($res){
                    //通知微信 回调失败
                    echo $this->wechat_notify_pay_result($res, 'fail');
                    exit;
                }
                //通知微信 回调成功
                echo $this->wechat_notify_pay_result('已经成功收到回调信息了', 'success');
                exit;
            } else {
                //失败记录
                write_log('微信支付回调参数状态码错误', []);
            }
        } else {
            //失败记录
            write_log('微信支付回调失败', []);
        }
    }
    //订单验证处理
    private function order_check(){
        //查询订单是否存在
        $order_info = $this->all_order_info = (new Order())->where([
            'order_no' => $this->order_no,//订单号
            'status'   => (new Order())::UNPAID,//未付款
        ])->find();
        if(!$order_info){
            //查询不到订单
            //通知微信 记录日志
            return '查询不到总订单/订单已经支付过了';
        }
        if($order_info['total_price'] != $this->notify_data['total_fee']){
            //订单金额对应不上
            //通知微信 记录日志
            return '与实际订单支付金额不符';
        }
        return false;
    }

    //房产订单 estate_report
    private function pay_notify_estate_order(){

        //开启事务处理订单
        Db::startTrans();
        try{
//            //总订单处理
//            if(!$this->all_order_update()){
//                Db::rollback();
//            }
            //查询订单是否存在
            $where_order = [
                'order_no' => $this->order_no,//订单号
                'status'   => 0,//未付款
            ];
            $order_info = $this->order_info = Db::name('estate_report')->where($where_order)->find();
            if(!$order_info){
                //查询不到订单
                //通知微信 记录日志
                return '查询不到ESTATE订单/订单已经支付过了';
            }
            $update_data = [
                'status' => 1,
            ];
            Db::name('estate_report')->where($where_order)->update($update_data);//修改订单为已支付

            //分配佣金 FIVE LEVEL
            $res = $this->assign_commission();

            if(!$res){
                Db::rollback();
            }
            //提交事务
            Db::commit();
            return false;
        }catch (\Exception $e) {
            Db::rollback();
            return 'ESTATE订单错误' . $e;
        }

    }

    //代理商订单 agent_apply_record
    private function pay_notify_agent_order(){

        //开启事务处理订单
        Db::startTrans();
        try{
//            //总订单处理
//            if(!$this->all_order_update()){
//                Db::rollback();
//            }
            //查询订单是否存在
            $where_order = [
                'order_no' => $this->order_no,//订单号
                'status'   => 0,//未付款
            ];
            $order_info = $this->order_info = Db::name('agent_apply_record')->where($where_order)->find();
            if(!$order_info){
                //查询不到订单
                //通知微信 记录日志
                return '查询不到AGENT订单/订单已经支付过了';
            }
            $update_data = [
                'status' => 1,
            ];
            Db::name('agent_apply_record')->where($where_order)->update($update_data);//修改订单为已支付

            //分配佣金 FIVE LEVEL
            $res = $this->assign_commission();
            if(!$res){
                Db::rollback();
            }
            //提交事务
            Db::commit();
            return false;
        }catch (\Exception $e) {
            Db::rollback();
            return 'AGENT订单错误' . $e;
        }
    }

    //总订单
    private function pay_notify_all_order(){

        //开启事务处理订单
        Db::startTrans();
        try{
            //总订单处理
            if(!$this->all_order_update()){
                Db::rollback();
            }
            //分配佣金 FIVE LEVEL
            $res = $this->assign_commission();
            if(!$res){
                Db::rollback();
            }
            //提交事务
            Db::commit();
            return false;
        }catch (\Exception $e) {
            Db::rollback();
            return '总订单错误' . $e;
        }
    }

    //总订单更新
    private function all_order_update(){

        $where_order = [
            'order_no' => $this->order_no,//订单号
            'status'   => (new Order())::UNPAID,//未付款
        ];
        $update_data = [
            'status' => (new Order())::PAID,//已付款
            'pay_way' => (new Order())::WECHAT_WAY,//支付方式
            'is_settle' => 1,//是否结算
            'trade_no' => $this->notify_data['transaction_id'],//支付流水号
            'pay_time' => time(),//支付时间
            'shop_money_time' => time(),//结算时间
            'update_time' => time(),
        ];
        $res = (new Order())->where($where_order)->update($update_data);//修改订单为已支付
        if(!$res){
            $this->pay_log('总订单更新失败',['$where_order'=>$where_order,'$update_data'=>$where_order]);
            return '总订单更新失败';
        }

        return true;
    }

    //佣金分配
    private function assign_commission()
    {
        $user_id  = $this->order_info['user_id'];//子订单表用户ID
//        $user_id  = $this->all_order_info['userid'];//总订单表用户ID
        $parent_user_id = Db::name('user')->where(['status' => 1,'id'=>$user_id])->value('inviter_mem_info_id');
        $commission_log = [];
        //有上级
        if($parent_user_id){
            //所有用户
            $all_user = Db::name('user')->field('id,inviter_mem_info_id as pid,nickname,money')->where([
                'status' => 1,
            ])->select();
            //等级对应的佣金表
            $share_percent = Db::name('sharepercent')->select();
            $max_level    = 5;//最多5级
            //递归查询用户的上级
            $users = get_downline($all_user, $parent_user_id, $max_level);
            $this->pay_log('用户的所有上级',$users);
            //查询上级等级和对应的佣金比例
            foreach ($users as $k => $v) {
                $level_name = $v['level'] . 'level';
                $poin       = empty($share_percent[0][$level_name]) ? 0 : $share_percent[0][$level_name];
                //对应等级
                if ($poin) {
                    $v['money'] = $v['money']?? 0;
                    $earnings_price = ($this->total_fee * $poin);
                    $total          = ($v['money'] + $earnings_price);
                    //更新账户余额
                    Db::name('user')->where('id', $v['id'])->update(['money' => $total]);
                    $this->pay_log('账户佣金:', [
                        $v['nickname'] => [//昵称
                            '账户之前余额' => $v['money'],
                            '增加余额'    => $earnings_price,
                            '更新之后余额' => $total,
                        ],
                    ]);
                    $commission_log[] = [
                        'user_id'        => $v['id'],
                        'parent_id'      => Db::name('user')->where(['status' => 1,'id'=>$v['pid']])->value('inviter_mem_info_id'),//父级
                        'orderno'        => $this->order_no,//单号
                        'pay_price'      => $this->total_fee,//金额
                        'poin'           => $poin,//百分比
                        'earnings_price' => $earnings_price,//佣金
                        'create_time'    => time(),
                    ];
                }
            }

            Db::name('earning')->insertAll($commission_log);
        }

        //记录给予上级佣金
        $this->pay_log('记录给予上级佣金',$commission_log);
        $this->pay_log('分配佣金完毕,回调订单完成。', []);
        return true;
    }

    //记录支付日志
    private function pay_log($remark, $data)
    {

        $path      = app()->getRuntimePath() . 'pay_log/' . Date('Ym') . '/';
        $file_name = date('d') . '.txt';
        write_log($remark, $data, $path, $file_name);
    }

    //通知微信回调结果
    /**
     *
     * @param $msg
     * @param $status success fail
     *
     */
    private function wechat_notify_pay_result($msg,$status){

        if($status == 'success'){
            $status = 'SUCCESS';
            $msg = 'OK';
        }
        if($status == 'fail'){
            $status = 'FAIL';
        }
        $res = "<xml><return_code><![CDATA[$status]]></return_code><return_msg><![CDATA[$msg]]></return_msg></xml>";
        $this->pay_log('通知微信回调结果',$res);
        return $res;
    }
}

