<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/1/28
 * Time: 17:28
 */

namespace app\admin\controller;

use app\model\Tags;
use app\service\TagsService;
use think\Request;
use think\facade\App;

class Tag extends BaseAdmin
{
    protected $tagsService;
    protected function initialize()
    {
        $this->tagsService = new TagsService();
    }

    public function index(){
        $data = $this->tagsService->getPagedAdmin();
        $this->assign([
            'tags' => $data['tags'],
            'count' => $data['count']
        ]);
        return view();
    }

    public function create(){
        return view();
    }

    public function save(Request $request){
        $tag = new Tags();
        $dir = App::getRootPath().'/public/static/upload/tags';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $tag->tag_name = $request->param('tag_name');
        $tag->save();
        $cover = $request->file('cover');
        if ($cover) {
            $cover->validate(['size' => 1024000, 'ext' => 'jpg,png,gif'])
                ->move($dir,$tag->id . '.jpg');
        }

        $this->success('添加成功','index','',1);
    }

    public function edit(){
        $returnUrl = input('returnUrl');
        $id = input('id');
        $tag = Tags::get($id);
        $this->assign([
            'tag' => $tag,
            'returnUrl' => $returnUrl
        ]);
        return view();
    }

    public function update(Request $request){
        $data = $request->param();
        $returnUrl = $data['returnUrl'];
        $tag = new Tags();
        $tag->isUpdate()->save($data);
        $dir = App::getRootPath().'/public/static/upload/tags';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $cover = $request->file('cover');
        if ($cover) {
            $cover->validate(['size' => 1024000, 'ext' => 'jpg,png,gif'])
                ->move($dir,$tag->id . '.jpg');
        }
        $this->success('编辑成功',$returnUrl,'',1);
    }

    public function delete($id)
    {
        Tags::destroy($id);
        return ['err' => 0,'msg' => '删除成功'];
    }
}