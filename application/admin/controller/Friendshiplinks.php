<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/1/30
 * Time: 11:35
 */

namespace app\admin\controller;


use app\model\FriendshipLink;
use think\Request;

class Friendshiplinks extends BaseAdmin
{
    public function index(){
        $data = FriendshipLink::order('id','desc');
        $links =  $data->paginate(5,false,
            [
                'type'     => 'util\AdminPage',
                'var_page' => 'page',
            ]);
        $this->assign([
            'links' => $links,
            'count' => $data->count()
        ]);
        return view();
    }

    public function create(){
        return view();
    }

    public function save(Request $request){
        $data = $request->param();
        $link = new FriendshipLink();
        $link->save($data);
        $this->success('新增友链成功');
    }

    public function edit(){
        $link = FriendshipLink::get(input('id'));
        $this->assign([
            'link' => $link,
        ]);
        return view();
    }

    public function update(Request $request){
        $data = $request->param();
        $link = new FriendshipLink();
        $link->isUpdate(true)->save($data);
        $this->success('编辑成功');
    }

    public function delete($id){
        FriendshipLink::destroy($id);
        return ['err' => '0','msg' => '删除成功'];
    }
}