<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/2/26
 * Time: 13:28
 */

namespace app\ucenter\controller;


use think\App;
use think\Controller;
use think\facade\Session;
use think\facade\View;

class BaseUcenter extends Controller
{
    protected $tpl;
    protected $uid;
    protected $user;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->uid = session('xwx_user_id');
        if (is_null($this->uid)){
            $this->redirect(url('/login'));
        }
        $tpl_root = './template/'.config('site.tpl').'/ucenter/';
        $controller = strtolower($this->request->controller());
        $action = strtolower($this->request->action());
        if ($this->request->isMobile()){
            $this->tpl = $tpl_root.$controller.'/'.$action.'.html';
        }else{
            $this->tpl = $tpl_root.$controller.'/'.'pc_'.$action.'.html';
        }

        $this->user = \app\model\User::get($this->uid);

        View::share([
            'url' => config('site.url'),
            'site_name' => config('site.site_name'),
            'img_site' => config('site.img_site'),
            'user' => $this->user
        ]);
    }
}