<?php


namespace app\ucenter\controller;

use app\model\UserFinance;
use app\model\UserOrder;
use think\Request;

class Codepayrec extends BaseUcenter
{
    public function notify(Request $request)
    {
        $data = $request->param();
        ksort($data); //排序post参数
        reset($data); //内部指针指向数组中的第一个元素
        $codepay_key = config('payment.codepay.appkey'); //这是您的密钥
        $sign = '';//初始化
        foreach ($data AS $key => $val) { //遍历POST参数
            if ($val == '' || $key == 'sign') continue; //跳过这些不签名
            if ($sign) $sign .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
            $sign .= "$key=$val"; //拼接为url参数形式
        }
        if (!$data['pay_no'] || md5($sign . $codepay_key) != $data['sign']) { //不合法的数据
            return 'fail';  //返回失败 继续补单
        } else { //合法的数据
            $status = (int)input('status');
            if ($status == 0) { //成功
                $order = UserOrder::get(input('pay_id')); //通过返回的订单id查询数据库
                if ($order) {
                    $order->money = $data['money'];
                    $order->pay_type = $data['type']; //支付类型
                    $order->update_time = $data['serverTime']; //云端处理订单时间戳
                    $order->expire_time = $data['endTime']; //订单过期时间戳
                    $order->isupdate(true)->save(); //更新订单

                    $userFinance = new UserFinance();
                    $userFinance->user_id = $order->user_id;
                    $userFinance->money = $order->money;
                    $userFinance->usage = 1; //用户充值
                    $userFinance->summary = '用户充值';
                    $userFinance->save(); //存储用户充值数据
                }
            }
            return 'success';
        }
    }

    public function feedback(Request $request)
    {
        $param = $request->param();
        ksort($param); //排序post参数
        reset($param); //内部指针指向数组中的第一个元素
        $codepay_key = config('payment.codepay.appkey'); //这是您的密钥
        $sign = '';//初始化
        foreach ($param AS $key => $val) { //遍历POST参数
            if ($val == '' || $key == 'sign') continue; //跳过这些不签名
            if ($sign) $sign .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
            $sign .= "$key=$val"; //拼接为url参数形式
        }
        if (!$param['pay_no'] || md5($sign . $codepay_key) != $param['sign']) { //不合法的数据
            return 'fail';
        } else { //合法的数据
            //业务处理
            $money = (float)$param['money']; //实际付款金额
            $pay_no = $param['pay_no']; //流水号
            $this->assign([
                'money' => $money,
                'pay_no' => $pay_no
            ]);
            return view($this->tpl);
        }
    }
}