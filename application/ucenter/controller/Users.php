<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/2/26
 * Time: 13:27
 */

namespace app\ucenter\controller;

use app\model\Comments;
use app\model\User;
use app\service\FinanceService;
use app\service\UserService;
use think\facade\App;
use think\facade\Validate;
class Users extends BaseUcenter
{
    protected $userService;
    protected $financeService;

    protected function initialize()
    {
        $this->userService = new UserService();
        $this->financeService = new FinanceService();
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
        $balance = cache('balance:' . $this->uid); //当前用户余额
        if (!$balance) {
            $balance = $this->financeService->getBalance();
            cache('balance:' . $this->uid, $balance, '', 'pay');
        }
        $this->assign([
            'balance' => $balance,
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
            $phone = trim(input('txt_phone'));
            if (verifycode($code,$phone) == 0){
                return ['err' => 1, 'msg' => '验证码错误'];
            }
            if (User::where('mobile','=',$phone)->find()){
                return ['err' => 1, 'msg' => '该手机号码已经存在'];
            }
            $user->mobile = $phone;
            $user->isUpdate(true)->save();
            session('xwx_user_mobile',$phone);
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

    public function verifyphone(){
        $phone = input('txt_phone');
        $code = input('txt_phonecode');
        if (verifycode($code,$phone) == 0){
            return ['err' => 1, 'msg' => '验证码错误'];
        }
        return ['err' => 0];
    }

    public function sendcms(){
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
        $result = sendcode($this->uid,$phone,$code);
        if ($result['status'] == 0){ //如果发送成功
            session('xwx_sms_code',$code); //写入session
            session('xwx_cms_phone',$phone);
            $redis = new_redis();
            $redis->set($this->redis_prefix.':xwx_mobile_unlock:'.$this->uid,1,300); //设置解锁缓存，让用户可以更改手机
        }
        return ['msg' => $result['msg']];
    }

    public function userphone(){
        $user = User::get($this->uid);
        $this->assign([
            'user' => $user,
            'header_title' => '管理手机'
        ]);
        return view($this->tpl);
    }

    public function resetpwd(){
        if ($this->request->isPost()){
            $pwd = input('password');
            $validate = new \think\Validate;
            $validate->rule('password', 'require|min:6|max:21');

            $data = [
                'password'  => $pwd,
            ];
            if (!$validate->check($data)) {
              return[ 'msg' => '密码在6到21位之间','err' => 1];
            }
            $user = User::get($this->uid);
            $user->password = $pwd;
            $user->isUpdate(true)->save();
            return ['msg' => '修改成功', 'err' => 0];
        }
        $this->assign([
            'header_title' => '修改密码'
        ]);
        return view($this->tpl);
    }

    public function commentadd(){
        $content = strip_tags(input('comment')) ;
        $book_id = input('book_id');
        $redis = new_redis();
        if ($redis->exists('comment_lock:'.$this->uid)){
            return json(['msg' => '每10秒只能评论一次','err' => 1]) ;
        }else{
            $comment = new Comments();
            $comment->user_id = $this->uid;
            $comment->book_id = $book_id;
            $result = $comment->save();
            if ($result){
                $redis->set('comment_lock:'.$this->uid,1,10);
                $dir = App::getRootPath().'public/static/upload/comments/'.$book_id;
                if (!file_exists($dir)){
                    mkdir($dir, 0777, true);
                }
                file_put_contents($dir.'/'.$comment->id. '.txt',$content);
                cache('comments:'.$book_id,null);
                return json(['msg' => '评论成功','err' => 0]) ;
            }else{
                return json(['msg' => '评论失败','err' => 1]) ;
            }
        }
    }
}