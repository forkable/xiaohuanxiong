<?php


namespace Util;


class Vkzf
{
    public function submit($order_id, $money, $pay_type = 'alipay')
    {
        $parameter = array(
            "pid" => trim(config('payment.vkzf.appid')),
            "type" => $pay_type,
            "notify_url" => config('site.url') . '/vkzfnotify',//通知地址,
            "return_url" => config('site.url') . '/feedback',//跳转地址,
            "out_trade_no" => $order_id, //唯一标识 可以是用户ID,用户名,session_id(),订单ID,ip 付款后返回
            "name" => config('site.site_name') . '充值',
            "money" => $money
        );

        $para_filter = paraFilter($parameter);
        $para_sort = argSort($para_filter); //对待签名参数数组排序
        $mysign = buildRequestMysign($para_sort); //生成签名结果
        $urls = 'https://pay.qfme.top/submit.php?'; //初始化URL参数为空

        //签名结果与签名方式加入请求提交参数组中
        $para_sort['sign'] = $mysign;
        $para_sort['sign_type'] = strtoupper('MD5');

        $para_str = createLinkstringUrlencode($para_sort);

        $urls = $urls . $para_str;
        header("Location:{$urls}"); //跳转到支付页面
    }
}