<?php


namespace app\admin\controller;


use think\facade\App;

class Sms extends BaseAdmin
{
    public function smsbao(){
        if ($this->request->isPost()){
            $username = input('username');
            $password = input('password');
            $content = <<<INFO
        <?php
        return [
            'username' => '{$username}',
            'password' => '{$password}',        
        ];
INFO;
        file_put_contents(App::getRootPath() . 'config/sms.php', $content);
        $this->success('修改成功');
        }
        $username = config('sms.username');
        $password = config('sms.password');
        $this->assign([
            'username' => $username,
            'password' => $password
        ]);
        return view();
    }
}