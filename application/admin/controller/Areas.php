<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/3/13
 * Time: 13:38
 */

namespace app\admin\controller;


use app\model\Area;

class Areas extends BaseAdmin
{
    public function index(){
        $areas = Area::all();
        $this->assign([
            'areas' => $areas,
            'count' => count($areas)
        ]);
        return view();
    }

    public function create(){
        return view();
    }

    public function save(){
        $aname = trim(input('area_name')) ;
        $area = Area::where('area_name','=',$aname)->find();
        if ($area){
            $this->error('已经存在相同地区！');
        }
        $area = new Area();
        $area->area_name = $aname;
        $area->save();
        $this->success('添加成功','index','',1);
    }

    public function edit(){
        $returnUrl = input('returnUrl');
        $id = input('id');
        $area = Area::get($id);
        $this->assign([
            'area' => $area,
            'returnUrl' => $returnUrl
        ]);
        return view();
    }

    public function update(){
        $returnUrl = input('returnUrl');
        $aname = trim(input('area_name')) ;
        $area = Area::where('area_name','=',$aname)->find();
        if ($area){
            $this->error('已经存在相同地区！');
        }
        $area = new Area();
        $area->id = input('id');
        $area->area_name = $aname;
        $area->isUpdate(true)->save();

        $this->success('编辑成功',$returnUrl,'',1);
    }

    public function delete($id)
    {
        Area::destroy($id);
        return ['err' => 0,'msg' => '删除成功'];
    }
}