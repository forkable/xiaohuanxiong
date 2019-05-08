<?php

namespace app\admin\controller;

use app\model\Book;
use think\Controller;
use think\Request;
use app\model\Chapter;

class Chapters extends BaseAdmin
{
    protected $chapterService;
    protected $validate;

    public function initialize()
    {
        $this->chapterService = new \app\service\ChapterService();
        $this->validate = new \app\admin\validate\Chapter;

    }

    public function index($book_id)
    {
        $book = Book::get(input('book_id'));
        $data = $this->chapterService->getChapters([
            ['book_id','=',$book_id]
        ]);
        $this->assign([
            'chapters' => $data['chapters'],
            'count' => $data['count'],
            'book' => $book
        ]);
        return view();
    }

    public function create(){
        $book_id = input('book_id');
        $lastChapterOrder = 0;
        $lastChapter = $this->chapterService->getLastChapter($book_id);
        if ($lastChapter){
            $lastChapterOrder = $lastChapter->order;
        }
        $this->assign([
            'book_id' => $book_id,
            'order' => $lastChapterOrder + 1,
        ]);
        return view();
    }

    public function save(Request $request)
    {
        $data = $request->param();
        if ($this->validate->check($data)){
            $result = Chapter::create($data);
            if ($result){
                $param = [
                    "id" => $data["book_id"],
                    "last_time" => time()
                ];
                Book::update($param);
                $this->success('添加成功');
            }else{
                $this->error('新增失败');
            }
        }else{
            $this->error($this->validate->getError());
        }

    }

    public function edit($id)
    {
        $id = input('id');
        $chapter = Chapter::get($id);
        if (!$chapter){
            $this->error('不存在的章节');
        }
        $this->assign([
            'chapter' => $chapter,
        ]);
        return view();
    }

    public function update(Request $request)
    {
        $data = $request->param();
        if ($this->validate->check($data)) {
            $chapter = Chapter::get($data['id']);
            if ($chapter) {
                $chapter->isUpdate(true)->save($data);
                $this->success('编辑成功');
            } else {
                $this->error('章节不存在');
            }
        }else{
            $this->error($this->validate->getError());
        }
    }

    public function delete($id)
    {
        $chapter = Chapter::get($id);
        $photos = $chapter->photos;
        if (count($photos) > 0){
            return ['err'=>1,'msg'=>'章节下还存在图片，请先删除'];
        }
        $chapter->delete();
        return ['err'=>0,'msg'=>'删除成功'];
    }
}
