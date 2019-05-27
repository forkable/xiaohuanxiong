<?php


namespace app\ucenter\controller;


use app\model\UserFinance;
use app\model\UserOrder;
use think\Controller;
use think\facade\Cache;
use think\Request;

class Vkzfnotify extends Controller
{
    public function index(Request $request)
    {
        $para = $request->param();
        $sign = input('sign');
        $isSgin = verifyNotify($request, $sign);
        if ($isSgin) {
            $order_id = str_replace('xwx_order_', '', $para['out_trade_no']);
            $type = 0;
            switch ($para['type']) {
                case 'alipay':
                    $type = 1;
                    break;
                case 'qqpay':
                    $type = 2;
                    break;
                case 'wxpay':
                    $type = 3;
                    break;
            }
            $status = 0;
            if ($para['trade_status'] == 'TRADE_SUCCESS') {
                $status = 1;
            } else {
                return json(['code' => false, 'status' => 'error']);
            }
            $order = UserOrder::get($order_id); //通过返回的订单id查询数据库
            if ($order) {
                $order->money = $para['money'];
                $order->pay_type = $type; //支付类型
                $order->update_time = time(); //云端处理订单时间戳
                $order->status = $status;
                $order->isupdate(true)->save(); //更新订单

                if ($status == 1) { //如果已支付，则更新用户财务信息
                    $userFinance = new UserFinance();
                    $userFinance->user_id = $order->user_id;
                    $userFinance->money = $order->money;
                    $userFinance->usage = 1; //用户充值
                    $userFinance->summary = '快支付';
                    $userFinance->save(); //存储用户充值数据
                }
                Cache::clear('pay'); //清除支付缓存
            }
            return json(['code' => true, 'status' => 'success']);
        }
    }
}