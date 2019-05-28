<?php


namespace app\service;


use app\model\UserOrder;

class OrderService
{
    public function getPagedOrders()
    {
        $data = UserOrder::order('id', 'desc');
        $orders = $data->paginate(5, false,
            [
                'query' => request()->param(),
                'type' => 'util\AdminPage',
                'var_page' => 'page',
            ]);
        return [
            'orders' => $orders,
            'count' => $data->count()
        ];
    }
}