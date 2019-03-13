<?php
/**
 * Created by PhpStorm.
 * User: zhangxiang
 * Date: 2018/10/17
 * Time: 下午5:01
 */

namespace app\index\controller;


use app\model\Tags as Tag;

class Tags extends Base
{
    public function index(){
        $tags = cache('tags');
        if (!$tags){
            $tags = Tag::all();
            cache('tags',$tags,null,'redis');
        }
        $this->assign([
            'tags' => $tags,
        ]);


        return view();
    }
}