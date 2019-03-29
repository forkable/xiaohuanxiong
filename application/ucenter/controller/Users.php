<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/2/26
 * Time: 13:27
 */

namespace app\ucenter\controller;

use app\service\UserService;
use think\Request;

class Users extends BaseUcenter
{
    protected $userService;

    protected function initialize()
    {
        $this->userService = new UserService();
    }

    public function bookshelf(){
        $favors = $this->userService->getFavors($this->uid);
        $this->assign([
            'favors' => $favors,
            'header_title' => '我的收藏'
        ]);
        return view($this->tpl);
    }

    public function delbookshelf(){
//        if ($request->isPost()){
//            $ids = explode(',', $request->param('mid')) ; //书籍id;
//            $this->userService->delFavors($this->uid,$ids);
//            return ['err' => 0, 'msg' => '删除收藏'] ;
//        }else{
//            return ['err' => 1, 'msg' => '非法请求'] ;
//        }
        return ['err' => 1, 'msg' => 'ggggg'];
    }


    public function history(){
        $redis = new_redis();
        $vals = $redis->hVals('history:'.$this->uid);
        $books = array();
        foreach ($vals as $val){
            $books[] = json_decode($val,true);
        }
        $this->assign([
            'books' => $books,
            'header_title' => '阅读历史'
        ]);
        return view($this->tpl);
    }

    public function ucenter(){
        $this->assign([
            'header_title' => '个人中心'
        ]);
        return view($this->tpl);
    }

    public function userinfo(){
        return view($this->tpl);
    }
}