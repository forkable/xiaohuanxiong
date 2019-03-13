<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/2/25
 * Time: 15:55
 */

namespace app\ucenter\controller;


use app\model\User;
use think\Controller;
use think\Request;

class Account extends Controller
{
    public function register(Request $request){
        if ($request->isPost()){
            $user = User::where('username','=',trim($request->param('username')))->find();
            if (!is_null($user)){
                return ['err' => 1, 'msg' => '用户名已经存在'];
            }
            $user = new User();
            $result = $user->save([
                'username' => trim($request->param('username')),
                'password' => $request->param('password')
            ]);
            if ($result){
                return ['err' => 0, 'msg' => '注册成功，请登录'];
            }else{
                return ['err' => 1, 'msg' => '注册失败，请尝试重新注册'];
            }
        }
        $site_name = config('site.site_name');
        $this->assign('site_name',$site_name);
        return view();
    }

    public function login(Request $request){
        if ($request->isPost()){
            $map = array();
            $map[] = ['username','=',trim($request->param('username'))];
            $map[] = ['password','=',md5(strtolower(trim($request->param('password'))).config('site.salt'))];
            $user = User::where($map)->find();
            if (is_null($user)){
                return ['err' => 1, 'msg' => '用户名或密码错误'];
            }else {
                session('xwx_user',$user->username);
                session('xwx_user_id',$user->id);
                return ['err' => 0, 'msg' => '登录成功'];
            }
        }
        $site_name = config('site.site_name');
        $this->assign('site_name',$site_name);
        return view();
    }
}