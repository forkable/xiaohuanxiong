<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/2/26
 * Time: 13:27
 */

namespace app\ucenter\controller;

use app\model\User;
use app\service\UserService;
use think\facade\Validate;
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
        $vals = $redis->hVals($this->redis_prefix.':history:'.$this->uid);
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
        $this->assign([
            'header_title' => '我的资料'
        ]);
        return view($this->tpl);
    }

    public function update(){
        if ($this->request->isPost()){
            $nick_name = input('nickname');
            $user = new User();
            $user->nick_name = $nick_name;
            $result = $user->isUpdate(true)->save(['id' => $this->uid]);
            if ($result){
                session('xwx_nick_name',$nick_name);
                return ['msg' => '修改成功'];
            }else{
                return ['msg' => '修改失败'];
            }
        }
        return ['msg' => '非法请求'];
    }

    public function bindphone(){
        $user = User::get($this->uid);
        $redis = new_redis();
        if ($this->request->isPost()){
            $code = trim(input('txt_phonecode'));
            $phone = trim(input('phone'));
            if (verifycode($code,$phone) == 0){
                return ['err' => 1, 'msg' => '验证码错误'];
            }
            if (User::where('mobile','=',$phone)->find()){
                return ['err' => 1, 'msg' => '该手机号码已经存在'];
            }
            $user->mobile = $phone;
            $user->isUpdate(true)->save();
            return ['err' => 0, 'msg' => '绑定成功'];
        }

        //如果用户手机已经存在，并且没有进行修改手机验证，也就是没有解锁缓存
        if (!$redis->exists($this->redis_prefix.':xwx_mobile_unlock:'.$this->uid) && !empty($user->mobile)) {
            $this->redirect('/userphone'); //则重定向至手机信息页
        }

        $this->assign([
            'header_title' => '绑定手机'
        ]);
        return view($this->tpl);
    }

    public function sendcode(){
        $code = generateRandomString();
        $phone = trim(input('phone'));
        $validate = Validate::make([
            'phone'  => 'mobile'
        ]);
        $data = [
            'phone'  => $phone
        ];
        if (!$validate->check($data)) {
            return ['msg' => '手机格式不正确'];
        }
//        $result = sendcode($this->uid,$code,$phone);
//        if ($result['status'] == 0){ //如果发送成功
//            session('xwx_sms_code',$code); //写入session
//            session('xwx_cms_phone',$phone);
//            $redis = new_redis();
//            $redis->set($this->redis_prefix.':xwx_mobile_unlock:'.$this->uid,1,300); //设置解锁缓存，让用户可以更改手机
//        }
//        return ['msg' => $result['msg']];
        session('xwx_sms_code',$code); //写入session
        session('xwx_cms_phone',$phone);
        return ['msg' => $code];;
    }

    public function userphone(){
        $user = User::get($this->uid);
        $this->assign([
            'user' => $user,
            'header_title' => '管理手机'
        ]);
        return view($this->tpl);
    }


}