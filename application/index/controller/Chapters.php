<?php
/**
 * Created by PhpStorm.
 * User: zhangxiang
 * Date: 2018/10/18
 * Time: 下午5:42
 */

namespace app\index\controller;

use app\model\Chapter;
use think\Db;

class Chapters extends Base
{
    public function index($id)
    {
        $chapter = Chapter::with(['photos' => function ($query) {
            $query->order('id');
        }], 'book')->cache('chapter' . $id,600,'redis')->find($id);
        $book_id = $chapter->book_id;
        $chapters = cache('mulu'.$book_id);
        if (!$chapters){
            $chapters = Chapter::where('book_id','=',$book_id)->select();
            cache('mulu'.$book_id,$chapters,null,'redis');
        }
        $prev = cache('chapter_prev'.$id);
        if (!$prev){
            $prev = Db::query(
                'select * from '.$this->prefix.'chapter where book_id='.$book_id.' and `order`<' . $chapter->order . ' order by id desc limit 1');
            cache('chapter_prev'.$id,$prev,null,'redis');
        }
        $next = cache('chapter_next'.$id);
        if (!$next){
            $next = Db::query(
                'select * from '.$this->prefix.'chapter where book_id='.$book_id.' and `order`>' . $chapter->order . ' order by id limit 1');
            cache('chapter_next'.$id,$next,null,'redis');
        }
        if (count($prev) > 0) {
            $this->assign('prev', $prev[0]);
        } else {
            $this->assign('prev', 'null');
        }
        if (count($next) > 0) {
            $this->assign('next', $next[0]);
        } else {
            $this->assign('next', 'null');
        }
        $this->assign([
            'chapter' => $chapter,
            'chapters' => $chapters,
            'photos' => $chapter->photos,
            'chapter_count' => count($chapters),
            'site_name' => config('site.site_name')
        ]);
        return view($this->tpl);
    }
}