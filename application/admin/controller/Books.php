<?php

namespace app\admin\controller;

use app\model\Area;
use app\model\Book;
use think\facade\App;
use think\Request;

class Books extends BaseAdmin
{
    protected $authorService;
    protected $bookService;

    public function initialize()
    {
        $this->authorService = new \app\service\AuthorService();
        $this->bookService = new \app\service\BookService();
    }

    public function index()
    {
        $data = $this->bookService->getPagedBooksAdmin();
        $books = $data['books'];
        foreach ($books as &$book) {
            $book['chapter_count'] = count($book->chapters);
        }
        $count = $data['count'];
        $this->assign([
            'books' => $books,
            'count' => $count
            ]);
        return view();
    }

    public function search(){
        $name = input('book_name');
        $where = [
            ['book_name', 'like', '%'.$name.'%']
        ];
        $data = $this->bookService->getPagedBooksAdmin($where);
        $books = $data['books'];
        foreach ($books as &$book) {
            $book['chapter_count'] = count($book->chapters);
        }
        $count = $data['count'];
        $this->assign([
            'books' => $books,
            'count' => $count
        ]);
        return view('index');
    }

    public function create()
    {
        $areas = Area::all();
        $this->assign('areas',$areas);
        return view();
    }

    public function save(Request $request)
    {
        $book = new Book();
        $data = $request->param();
        $validate = new \app\admin\validate\Book();
        if ($validate->check($data)){
            if ($this->bookService->getByName($data['book_name'])){
                $this->error('漫画名已经存在');
            }

            //作者处理
            $author = $this->authorService->getByName($data['author']);
            if (is_null($author)){//如果作者不存在
                $author = new \app\model\Author();
                $author->author_name = $data['author'];
                $author->save();
            }
            $book->author_id = $author->id;
            $result = $book->save($data);
            if ($result){
                $dir = App::getRootPath().'/public/static/upload/book/' . $book->id;
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                if (count($request->file()) > 0) {
                    $cover = $request->file('cover');
                    $cover->validate(['size' => 2048000, 'ext' => 'jpg,png,gif'])
                        ->move($dir,'cover.jpg');
                }

                $this->success('添加成功','index','',1);
            }else{
                $this->error('添加失败');
            }
        }else{
            $this->error($validate->getError());
        }
    }

    public function edit()
    {
        $returnUrl = input('returnUrl');
        $areas = Area::all();
        $id = input('id');
        $book = Book::with('author')->find($id);
        $this->assign([
            'book' => $book,
            'returnUrl' => $returnUrl,
            'areas' => $areas
        ]);
        return view();
    }

    public function update(Request $request){
        $data = $request->param();
        $returnUrl = $data['returnUrl'];
        $validate = new \app\admin\validate\Book();
        if ($validate->check($data)){
            //作者处理
            $author = $this->authorService->getByName($data['author']);
            if (is_null($author)){//如果是新作者
                $author = new \app\model\Author();
                $author->author_name = $data['author'];
                $author->save();
            }else{ //如果作者已经存在
                $data['author_id'] = $author->id;
            }
            $result = Book::update($data);
            if ($result){
                $dir = App::getRootPath().'/public/static/upload/book/' . $data['id'];
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                if (count($request->file()) > 0) {
                    $cover = $request->file('cover');
                    $cover->validate(['size' => 2048000, 'ext' => 'jpg,png,gif'])
                        ->move($dir,'cover.jpg');
                    //清理浏览器缓存
                    header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
                    header("Cache-Control: no-cache, must-revalidate" );

                }
                $this->success('编辑成功',$returnUrl,'',1);
            }else{
                $this->error('编辑失败');
            }
        }else{
            $this->error($validate->getError());
        }
    }

    public function delete($id)
    {
        $book = Book::get($id);
        $chapters = $book->chapters;
        if (count($chapters) > 0){
            return ['err' => 1,'msg' => '该漫画下含有章节，请先删除所有章节'];
        }
        $book->delete();
        return ['err' => 0,'msg' => '删除成功'];
    }


}
