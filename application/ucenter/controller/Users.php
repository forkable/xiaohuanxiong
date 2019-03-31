<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/2/26
 * Time: 13:27
 */

namespace app\ucenter\controller;

use app\service\UserService;
use think\response\Json;

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

    public function delfavors(){
        if ($this->request->isPost()){
            $ids = explode(',', input('ids')) ; //书籍id;
            $this->userService->delFavors($this->uid,$ids);
            return ['err' => 0, 'msg' => '删除收藏'] ;
        }else{
            return ['err' => 1, 'msg' => '非法请求'] ;
        }
    }

    public function delhistory(){
        if ($this->request->isPost()){
            $keys = explode(',',input('ids'));
            $this->userService->delHistory($this->uid,$keys);
            return ['err' => 0, 'msg' => '删除阅读历史'] ;
        } else {
            return ['err' => 1, 'msg' => '非法请求'] ;
        }
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