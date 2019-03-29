<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/2/26
 * Time: 13:48
 */

namespace app\service;

use app\model\User;
use think\Controller;

class UserService extends Controller
{
    public function getFavors($uid){
        $type = 'util\Page';
        if ($this->request->isMobile()){
            $type = 'util\MPage';
        }
        $user = User::get($uid);
        $books = $user->books()
            ->paginate(10,false,
                [
                    'query' => request()->param(),
                    'type'     => $type,
                    'var_page' => 'page',
                ]);
        return $books;
    }
}