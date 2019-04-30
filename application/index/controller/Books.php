<?php

namespace app\index\controller;

use app\model\Book;
use app\model\Comments;
use app\model\User;
use app\model\UserBook;
use think\Db;
use think\facade\App;
use think\Request;

class Books extends Base
{
    protected $bookService;

    public function initialize()
    {
        cookie('nav_switch', 'booklist'); //设置导航菜单active
        $this->bookService = new \app\service\BookService();
    }

    public function index($id)
    {
        $book = cache('book:' . $id);
        $tags = cache('tags:book:' . $id );
        if ($book ==false) {
            $book = Book::with(['chapters' => function($query){
                $query->order('order');
            }],'author')->find($id);
            $tags = explode('|', $book->tags);
            cache('book:' . $id, $book,null,'redis');
            cache('tags:book:' . $id , $tags,null,'redis');
        }
        $hot_books = $this->savehot($book);

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
        $start = cache('book_start:' . $id);
        if ($start == false) {
            $db = Db::query('SELECT id FROM '.$this->prefix.'chapter WHERE book_id = ' . $id . ' ORDER BY id LIMIT 1');
            $start = $db ? $db[0]['id'] : -1;
            cache('book_start:' . $id, $start,null,'redis');
        }

        $comments = $this->getComments($book->id);

        $isfavor = 0;
        if (!is_null($this->uid)){
            $where[] = ['user_id','=',$this->uid];
            $where[] = ['book_id','=',$id];
            $userfavor = UserBook::where($where)->find();
            if (!is_null($userfavor)){ //未收藏本漫画
                $isfavor = 1;
            }
        }

        $this->assign([
            'book' => $book,
            'tags' => $tags,
            'start' => $start,
            'updates' => $updates,
            'hot' => $hot_books,
            'recommand' => $recommand,
            'header_title' => $book->book_name,
            'isfavor' => $isfavor,
            'comments' => $comments
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

    public function addfavor(){
        if ($this->request->isPost()){
            if (is_null($this->uid)){
                return ['err' => 1, 'msg' => '用户未登录'];
            }
            $redis = new_redis();
            if ($redis->exists('favor_lock:'.$this->uid)){ //如果存在锁
                return ['err' => 1,'msg' => '操作太频繁'];
            }else{
                $redis->set('favor_lock:'.$this->uid,1,3); //写入锁

                $val = input('val');
                $book_id = input('book_id');

                if ($val == 0){ //未收藏
                    $user = User::get($this->uid);
                    $book = Book::get($book_id);
                    $user->books()->save($book);
                    return ['err' => 0, 'isfavor' => 1]; //isfavor表示已收藏
                }else{
                    $user = User::get($this->uid);
                    $user->books()->detach(['book_id' => $book_id]);
                    return ['err' => 0, 'isfavor' => 0]; //isfavor为0表示未收藏
                }
            }
        }
        return ['err' => 1,'msg' => '不是post请求'];
    }

    private function savehot($book)
    {
        $redis = new_redis();
        $redis->zIncrBy($this->redis_prefix . 'hot_books', 1, json_encode([
            'id' => $book->id,
            'book_name' => $book->book_name,
            'cover_url' => $book->cover_url,
            'last_time' => $book->last_time,
            'chapter_count' => count($book->chapters),
            'summary' => $book->summary,
            'area' => $book->area,
            'author' => $book->author,
            'taglist' => explode('|', $book->tags),
        ]));
        $hots = $redis->zRevRange($this->redis_prefix.'hot_books',0,10,true);
        $hot_books = array();
        foreach ($hots as $k => $v){
            $hot_books[] = json_decode($k,true);
        }
        return $hot_books;
    }

    private function getComments($book_id){
        $comments = cache('comments:'.$book_id);
        if (!$comments){
            $comments = Comments::where('book_id','=',$book_id)->order('create_time','desc')
                ->limit(0,5)->select();
            cache('comments:'.$book_id,$comments);
        }
        $dir = App::getRootPath().'public/static/upload/comments/'.$book_id;
        foreach ($comments as &$comment){
            $comment['content'] = file_get_contents($dir.'/'.$comment->id.'.txt');
        }
        return $comments;
    }
}
