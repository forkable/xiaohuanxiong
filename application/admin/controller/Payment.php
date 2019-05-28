<?php


namespace app\admin\controller;


use app\service\OrderService;
use think\facade\App;

class Payment extends BaseAdmin
{
    protected $orderService;

    protected function initialize()
    {
        $this->orderService = new OrderService();
    }

    //支付配置文件
    public function index()
    {
        if ($this->request->isPost()) {
            $content = input('json');
            file_put_contents(App::getRootPath() . 'config/payment.php', $content);
            $this->success('保存成功');
        }
        $content = file_get_contents(App::getRootPath() . 'config/payment.php');
        $this->assign('json', $content);
        return view();
    }

    //订单查询
    public function orders()
    {
        $data = $this->orderService->getPagedOrders();
        $this->assign([
            'orders' => $data['orders'],
            'count' => $data['count']
        ]);
        return view();
    }

}