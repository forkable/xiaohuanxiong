<?php

namespace app\index\controller;

use app\model\Book;
use think\Db;
use think\facade\Cache;
use think\Request;

class Books extends Base
{
    protected $bookService;

    public function initialize()
    {
        cookie('nav_switch', 'booklist'); //设置导航菜单active
        $this->bookService = new \app\service\BookService();
    }

    public function index(Request $request)
    {
        $id = $request->param('id');
        $book = cache('book' . $id);
        $tags = cache('book' . $id . 'tags');
        if ($book ==false) {
            $book = Book::with('chapters,author')->find($id);
            $tags = explode('|', $book->tags);
            cache('book' . $id, $book,null,'redis');
            cache('book' . $id . 'tags', $tags,null,'redis');
        }
        $redis = new_redis();
        $redis->zIncrBy($this->redis_prefix.'hot_books',1,json_encode([
            'id' => $book->id,
            'book_name' => $book->book_name,
            'cover_url' => $book->cover_url,
            'last_time' => $book->last_time,
            'chapter_count' => count($book->chapters),
            'summary' => $book->summary,
            'area' => $book->area,
            'author' => $book->author,
            'taglist' => explode('|',$book->tags),
        ]));

        $hots = $redis->zRevRange($this->redis_prefix.'hot_books',0,10,true);
        $hot_books = array();
        foreach ($hots as $k => $v){
            $hot_books[] = json_decode($k,true);
        }
        $recommand = cache('rand_books');
        if (!$recommand){
            $recommand = $this->bookService->getRandBooks();
            cache('rand_books',$recommand,null,'redis');
        }
        $updates = cache('update_books');
        if (!$updates){
            $updates = $this->bookService->getBooks('update_time',[],10);
            cache('update_books',$updates,null,'redis');
        }
        $start = cache('book_start' . $id);
        if ($start == false) {
            $db = Db::query('SELECT id FROM '.$this->prefix.'chapter WHERE book_id = ' . $request->param('id') . ' ORDER BY id LIMIT 1');
            $start = $db ? $db[0]['id'] : -1;
            cache('book_start' . $id, $start,null,'redis');
        }

        $this->assign([
            'book' => $book,
            'tags' => $tags,
            'start' => $start,
            'updates' => $updates,
            'hot' => $hot_books,
            'recommand' => $recommand,
            'header_title' => $book->book_name
        ]);
        return view($this->tpl);

    }

    public function booklist(Request $request)
    {
        $cate_selector = '全部';
        $area_selector = '全部';
        $end_selector = '全部';
        $tags = \app\model\Tags::all();
        $areas = \app\model\Area::all();
        $map = array();
        $area = $request->param('area');
        if (is_null($area) || $area == '-1'){

        } else {
            $area_selector = $area;
            $map[] = ['area_id','=',$area];
        }
        $tag = $request->param('tag');
        if (is_null($tag) || $tag == '全部'){

        } else {
            $cate_selector = $tag;
            $map[] = ['tags', 'like', '%' . $tag . '%'];
        }
        $end = $request->param('end');
        if (is_null($end) || $end == -1){

        } else {
            $end_selector = $end;
            $map[] = ['end','=',$end];
        }
        $books = $this->bookService->getPagedBooks('create_time', $map, 35);
        $this->assign([
            'books' => $books,
            'tags' => $tags,
            'areas' => $areas,
            'cate_selector' => $cate_selector,
            'area_selector' => $area_selector,
            'end_selector' => $end_selector,
            'header_title' => $cate_selector
        ]);
        return view($this->tpl);
    }
}
