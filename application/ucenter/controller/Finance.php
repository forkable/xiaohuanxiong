<?php


namespace app\ucenter\controller;


use app\model\Chapter;
use app\model\UserBuy;
use app\model\UserFinance;
use app\model\UserOrder;
use app\service\FinanceService;
use think\facade\Cache;
use think\Request;
use util\Zhapay;

class Finance extends BaseUcenter
{
    protected $financeService;
    protected $zhaPayUtil;
    protected $balance;

    protected function initialize()
    {
        $this->financeService = new FinanceService();
        $this->zhaPayUtil = new Zhapay();
        $this->balance = cache('balance:' . $this->uid); //当前用户余额
        if (!$this->balance) {
            $this->balance = $this->financeService->getBalance();
            cache('balance:' . $this->uid, $this->balance, '', 'pay');
        }
    }

    //充值记录
    public function chargehistory()
    {
        $charges = $this->financeService->getUserChargeHistory();

        $charge_sum = cache('chargesum:' . $this->uid);
        if (!$charge_sum) {
            $charge_sum = $this->financeService->getChargeSum();
            cache('chargesum:' . $this->uid, $charge_sum, '', 'pay');
        }

        $this->assign([
            'balance' => $this->balance,
            'charges' => $charges,
            'charge_sum' => $charge_sum,
            'header_title' => '充值记录'
        ]);
        return view($this->tpl);
    }

    public function spendinghistory()
    {
        $spendings = $this->financeService->getUserSpendingHistory();

        $spending_sum = cache('spendingsum:' . $this->uid);
        if (!$spending_sum) {
            $spending_sum = $this->financeService->getSpendingSum();
            cache('spendingsum:' . $this->uid, $spending_sum, '', 'pay');
        }

        $this->assign([
            'balance' => $this->balance,
            'spendings' => $spendings,
            'spending_sum' => $spending_sum,
            'header_title' => '消费记录'
        ]);
        return view($this->tpl);
    }

    //用户钱包
    public function wallet()
    {
        $charge_sum = cache('chargesum:' . $this->uid);
        if (!$charge_sum) {
            $charge_sum = $this->financeService->getChargeSum();
            cache('chargesum:' . $this->uid, $charge_sum, '', 'pay');
        }
        $spending_sum = cache('spending_sum:' . $this->uid);
        if (!$spending_sum) {
            $spending_sum = $this->financeService->getSpendingSum();
            cache('spending_sum:' . $this->uid, $charge_sum, '', 'pay');
        }
        $this->assign([
            'balance' => $this->balance,
            'charge_sum' => $charge_sum,
            'spending_sum' => $spending_sum,
            'header_title' => '我的钱包'
        ]);
        return view($this->tpl);
    }

    public function buyhistory()
    {
        $buys = cache('buyhistory:' . $this->uid);
        if (!$buys) {
            $buys = $this->financeService->getUserBuyHistory();
            cache('buyhistory:' . $this->uid, $buys, '', 'pay');
        }
        $this->assign([
            'buys' => $buys,
            'header_title' => '购买的作品'
        ]);
        return view($this->tpl);
    }

    //处理充值
    public function charge(Request $request)
    {
        if ($request->isPost()) {
            $money = $request->post('money'); //用户充值金额
            $pay_type = $request->post('pay_type'); //充值渠道
            $order = new UserOrder();
            $order->user_id = $this->uid;
            $order->money = $money;
            $order->status = 0; //未完成订单
            $order->pay_type = $pay_type;
            $order->summary = $request->port('summary');
            $order->expire_time = time() + 86400; //订单失效时间往后推一天
            $res = $order->save();
            if ($res) {
                $this->zhaPayUtil->submit('xwx_order_' . $order->id, $money, $pay_type); //调用功能类，进行充值处理
            }
        } else {

            $payment = config('site.payment');
            $payments = config('payment.' . $payment . '.channel');
            $this->assign([
                'balance' => $this->balance,
                'moneys' => config('payment.money'),
                'payments' => $payments,
                'header_title' => '用户充值'
            ]);
            return view($this->tpl);
        }
    }

    //用户支付回跳网址
    public function feedback()
    {

        $this->assign([
            'balance' => $this->balance,
            'header_title' => '支付成功'
        ]);
        return view($this->tpl);
    }

    public function buychapter()
    {
        $id = input('chapter_id');
        $chapter = Chapter::with(['photos' => function ($query) {
            $query->order('pic_order');
        }], 'book')->cache('chapter:' . $id, 600, 'redis')->find($id);
        $price = config('payment.price'); //获得单章价格
        if ($this->request->isPost()) {
            $redis = new_redis();
            $lock = $redis->sIsMember($this->redis_prefix.':user_buy_lock', $this->uid); //先判断有没有用户锁
            if (!$lock) { //如果没上锁，则该用户可以进行购买操作
                $redis->sAdd($this->redis_prefix.':user_buy_lock', $this->uid); //先加锁
                $this->balance = $this->financeService->getBalance(); //这里不查询缓存，直接查数据库更准确
                if ($price > $this->balance) { //如果价格高于用户余额，则不能购买
                    return ['err' => 1, 'msg' => '余额不足'];
                } else {
                    $userFinance = new UserFinance();
                    $userFinance->user_id = $this->uid;
                    $userFinance->money = $price;
                    $userFinance->usage = 3;
                    $userFinance->summary = '购买章节';
                    $userFinance->save();

                    $userBuy = new UserBuy();
                    $userBuy->user_id = $this->uid;
                    $userBuy->chapter_id = $id;
                    $userBuy->book_id = $chapter->book_id;
                    $userBuy->money = $price;
                    $userBuy->summary = '购买章节';
                    $userBuy->save();
                }
                $redis->sRem($this->redis_prefix.':user_buy_lock',$this->uid); //删除用户锁
                Cache::clear('pay'); //删除缓存
                return ['err' => 0, 'msg' => '购买成功，等待跳转'];
            }
        }
        $this->assign([
            'balance' => $this->balance,
            'chapter' => $chapter,
            'price' => $price
        ]);
        return view($this->tpl);
    }
}