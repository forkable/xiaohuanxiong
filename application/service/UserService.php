<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/2/26
 * Time: 13:48
 */

namespace app\service;

use app\model\UserFavor;
use think\Controller;

class UserService extends Controller
{
    public function getFavors($uid){
        $type = 'util\Page';
        if ($this->request->isMobile()){
            $type = 'util\MPage';
        }
        $favors = UserFavor::where('user_id','=',$uid)->with(['book' => function($query){
            $query->order('last_time','desc');
        }])->select();
//            ->paginate(10,false,
//                [
//                    'query' => request()->param(),
//                    'type'     => $type,
//                    'var_page' => 'page',
//                ]);
        return $favors;
    }
}