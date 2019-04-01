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
            $result = $user->isUpdate(true)->save(['id' => session('xwx_user_id')]);
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
        if ($this->request->isPost()){
            $code = input('txt_phonecode');
            if (is_null(session('xwx_sms_code')) || $code != session('xwx_sms_code')){
                return ['msg' => '验证码错误'];
            }
            $phone = input('phone');
            if (is_null(session('xwx_cms_phone')) || $phone != session('xwx_cms_phone')){
                return ['msg' => '手机号码错误'];
            }
            $user->mobile = $phone;
            $user->isUpdate(true)->save();
            return ['msg' => '绑定成功'];
        }
        if (!empty($user->mobile)) { //如果用户已经有了手机，重定向到手机信息页
            $this->redirect('/userphone');
        }
        $this->assign([
            'header_title' => '绑定手机'
        ]);
        return view($this->tpl);
    }

    public function verifycode(){
        $code = generateRandomString();
        $phone = input('phone');
        $validate = Validate::make([
            'phone'  => 'mobile'
        ]);
        $data = [
            'phone'  => $phone
        ];
        if (!$validate->check($data)) {
            return ['msg' => '手机格式不正确'];
        }
        $result = sendcode($this->uid,$code,$phone);
        if ($result['status'] == 0){ //如果发送成功
            session('xwx_sms_code',$code); //写入session
            session('xwx_cms_phone',$phone);
        }
        return ['msg' => $result['msg']];
    }
}