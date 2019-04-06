<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2018/10/6
 * Time: 20:44
 */

namespace app\admin\controller;

use app\model\Book;
use app\model\Chapter;
use app\model\Photo;
use app\service\PhotoService;
use think\facade\App;
use think\Request;

class Photos extends BaseAdmin
{
    protected $photoService;
    protected $validate;
    protected function initialize()
    {
        $this->photoService = new PhotoService();
        $this->validate = new \app\admin\validate\Photo;
    }

    public function index(){
        $chapter_id = input('chapter_id');
        $chapter = Chapter::get($chapter_id);
        $book_id = input('book_id');
        $book = Book::get($book_id);
        $data = Photo::where('chapter_id','=',$chapter_id);
        $photos = $data->order('order','desc')
            ->paginate(5,false,
                [
                    'query' => request()->param(),
                    'type'     => 'util\AdminPage',
                    'var_page' => 'page',
                ]);
        $this->assign([
            'photos'=>$photos,
            'chapter_id'=>$chapter_id,
            'book_id'=>$book_id,
            'book_name'=>$book->book_name,
            'chapter_name'=>$chapter->chapter_name,
            'count' => $data->count()
        ]);
        return view();
    }

    public function clear(){
        $chapter_id =  input('chapter_id');
        Photo::destroy(function ($query) use($chapter_id){
            $query->where('chapter_id','=',$chapter_id);
        });
        $this->success('删除章节图片');
    }

    public function delete(){
        $id = input('id');
        Photo::destroy($id);
        return ['err'=>0,'msg'=>'删除成功'];
    }

    public function upload(Request $request){
        $data = $request->param();
        if (!$this->validate->check($data)){
            $this->error($this->validate->getError());
        }
        $book_id = $data('book_id');
        $chapter_id = $data('chapter_id');
        $lastPhoto = $this->photoService->getLastPhoto($chapter_id);
        if (!$lastPhoto){
            $order = 1;
        }else{
            $order = $this->photoService->getLastPhoto($chapter_id)->order + 1; //拿到最新图片的order，加1
        }
        $files = $request->file('image');
        foreach($files as $file){
            $photo = new Photo();
            $photo->chapter_id = $chapter_id;
            $photo->order = $order;
            $result = $photo->save();
            if ($result){
                $dir = App::getRootPath() . 'public/static/upload/book/'.$book_id.'/'.$chapter_id;
                if (!file_exists($dir)){
                    mkdir($dir,0777,true);
                }
                $file->validate(['size'=>2048000,'ext'=>'jpg,png,gif'])->move($dir,$photo->id.'.jpg');
            }
            $order++;
        }
        $this->success('上传成功');
    }

    public function edit(Request $request){
        if ($request->isPost()){
            $data = $request->param();
            if (!$this->validate->check($data)){
                $this->error($this->validate->getError());
            }
            $photo = new Photo();
            $result = $photo->isUpdate(true)->save($data);
            if ($result){
                $file = $request->file('image');
                $dir = App::getRootPath() . 'public/static/upload/book/'.$data['book_id'].'/'.$data['chapter_id'];
                if (!file_exists($dir)){
                    mkdir($dir,0777,true);
                }
                if ($file){
                    $file->validate(['size'=>2048000,'ext'=>'jpg,png,gif'])->move($dir,$photo->id.'.jpg');
                }
                $this->success('编辑成功',$data['returnUrl'],'',1);
            }else{
                $this->error('编辑失败');
            }
        }
        $book_id = input('book_id');
        $chapter_id = input('chapter_id');
        $id = input('id');
        $returnUrl = input('returnUrl');
        $photo = Photo::get($id);
        if (!$photo){
            $this->error('图片不存在');
        }
        $this->assign([
            'id' => $id,
            'book_id' => $book_id,
            'chapter_id' => $chapter_id,
            'order' => $photo->order,
            'returnUrl' => $returnUrl
        ]);
        return view();
    }
}