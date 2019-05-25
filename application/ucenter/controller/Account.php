<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/2/25
 * Time: 15:55
 */

namespace app\ucenter\controller;


use app\model\User;
use think\App;
use think\Controller;
use think\Request;

class Account extends Controller
{
    protected $tpl;
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        if ($this->request->isMobile()){
            $this->tpl = $this->request->action();
        }else{
            $this->tpl = 'pc_'.$this->request->action();
        }
    }

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
        }else{
            $this->assign('site_name',config('site.site_name'));
            return view('./template/default/ucenter/account/register.html');
        }
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
                session('xwx_nick_name',$user->nick_name);
                session('xwx_user_mobile',$user->mobile);
                session('xwx_user_level',$user->level);
                return ['err' => 0, 'msg' => '登录成功'];
            }
        }else{
            $this->assign('site_name',config('site.site_name'));
            return view('./template/default/ucenter/account/login.html');
        }
    }

    public function logout(){
        session('xwx_user',null);
        session('xwx_user_id',null);
        $this->success('成功登出','/login');
    }

    public function recovery(){
        if ($this->request->isPost()){
            $code = trim(input('txt_phonecode'));
            $phone = trim(input('txt_phone'));
            if (verifycode($code,$phone) == 0){
                return ['err' => 1, 'msg' => '验证码不正确'];
            }
            $pwd = input('txt_password');
            $user = User::where('mobile','=',$phone)->find();
            if (is_null($user)){
                return ['err' => 1, 'msg' => '该手机号不存在'];
            }
            $user->password = $pwd;
            $user->isUpdate(true)->save();
            return ['err' => 0, 'msg' => '修改成功'];
        }
        $phone = input('phone');
        $this->assign([
            'phone' => $phone,
            'header_title' => '找回密码'
        ]);
        return view($this->tpl);
    }
}