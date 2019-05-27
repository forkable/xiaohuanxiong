<?php
return [
    'zhapay' => [ //幻兮支付，官网地址:https://www.zhapay.com/
        'appid' => '',
        'appkey' => '',
        'channel' => [
            ['type' => 2, 'img' => 'alipay', 'title' => '支付宝'],
            ['type' => 1, 'img' => 'weixin', 'title' => '微信支付']
        ]
    ],
    'vkzf' =>[ //快支付，官网地址：https://www.vkzf.cn/
        'appid' => '1714',
        'appkey' => 's3EMgvQRqsxGbPp3YVgqYQvxmqTzmEsf',
        'channel' => [
            ['type' => 'alipay', 'img' => 'alipay', 'title' => '支付宝'],
            ['type' => 'wxpay', 'img' => 'weixin', 'title' => '微信支付'],
            ['type' => 'qqpay', 'img' => 'qq', 'title' => 'QQ钱包']
        ]
    ],
    'vip' => [  //设置vip天数及相应的价格
        ['day' => 7, 'price' => 30],
        ['day' => 30, 'price' => 100]
    ],
    'money' => [1, 10, 30, 100], //设置支付金额
    'price' => 5 //设置单章价格
];