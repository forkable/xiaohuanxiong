<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/2/26
 * Time: 13:28
 */

namespace app\ucenter\controller;


use think\Controller;

class BaseUcenter extends Controller
{
    protected function checkAuth(){
        if (!Session::has('xwx_user')) {
            $this->redirect('ucenter/login/index');
        }
    }
}