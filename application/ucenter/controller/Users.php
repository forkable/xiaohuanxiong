<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/2/26
 * Time: 13:27
 */

namespace app\ucenter\controller;


use app\service\UserService;

class Users extends BaseUcenter
{
    protected $userService;

    protected function initialize()
    {
        $this->userService = new UserService();
    }

    public function bookshelf(){
        $uid = session('xwx_user_id');
        $favors = $this->userService->getFavors($uid);
        $this->assign([
            'favors' => $favors
        ]);
       // halt($favors);
        return view($this->tpl);
    }

    public function userinfo(){
        return view($this->tpl);
    }
}