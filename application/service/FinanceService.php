<?php


namespace app\service;

use app\admin\controller\Chapters;
use app\model\Chapter;
use app\model\UserBuy;
use app\model\UserFinance;
use think\Controller;

class FinanceService extends Controller
{
    public function getUserChargeHistory()
    {
        $uid = session('xwx_user_id');
        if (!$uid) {
            return [];
        } else {
            $map = array();
            $map[] = ['user_id', '=', $uid];
            $map[] = ['usage', '=', 1];
            $type = 'util\Page';
            if ($this->request->isMobile()) {
                $type = 'util\MPage';
            }
            $charges = UserFinance::where($map)->paginate(1, false,
                [
                    'query' => request()->param(),
                    'type' => $type,
                    'var_page' => 'page',
                ]);

            return $charges;
        }
    }

    public function getUserSpendingHistory()
    {
        $uid = session('xwx_user_id');
        if (!$uid) {
            return [];
        } else {
            $map = array();
            $map[] = ['user_id', '=', $uid];
            $map[] = ['usage', ['=', 2], ['=', 3], 'or'];
            $type = 'util\Page';
            if ($this->request->isMobile()) {
                $type = 'util\MPage';
            }
            $spendings = UserFinance::where($map)->paginate(10, true,
                [
                    'query' => request()->param(),
                    'type' => $type,
                    'var_page' => 'page',
                ]);
            return $spendings;
        }
    }

    //获得当前用户充值总和
    public function getChargeSum()
    {
        $uid = session('xwx_user_id');
        if (!$uid) {
            return 0;
        }
        $map = array();
        $map[] = ['user_id', '=', $uid];
        $map[] = ['usage', '=', 1];
        $sum = UserFinance::where($map)->sum('money');
        return $sum;
    }

    //获得当前用户消费总和
    public function getSpendingSum()
    {
        $uid = session('xwx_user_id');
        if (!$uid) {
            return 0;
        }
        $map = array();
        $map[] = ['user_id', '=', $uid];
        $map[] = ['usage', ['=', 2], ['=', 3], 'or'];
        $sum = UserFinance::where($map)->sum('money');
        return $sum;
    }

    public function getBalance()
    {
        $uid = session('xwx_user_id');
        if (!$uid) {
            return 0;
        }
        $charge_sum = $this->getChargeSum();
        $spending_sum = $this->getSpendingSum();
        return $charge_sum - $spending_sum;
    }

    public function getUserBuyHistory()
    {
        $uid = session('xwx_user_id');
        if (!$uid) {
            return [];
        } else {
            $type = 'util\Page';
            if ($this->request->isMobile()) {
                $type = 'util\MPage';
            }
            $buys = UserBuy::where('user_id', '=', $uid)->order('id','desc')
                ->paginate(10, false,
                [
                    'query' => request()->param(),
                    'type' => $type,
                    'var_page' => 'page',
                ]);
            foreach ($buys as &$buy) {
                $chapter = Chapter::with('book')->find($buy['chapter_id']);
                $buy['chapter'] = $chapter;
            }
            return $buys;
        }
    }
}