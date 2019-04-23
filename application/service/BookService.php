<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2018/10/3
 * Time: 23:39
 */

namespace app\service;
use app\index\controller\Base;
use app\model\Book;
use app\model\Chapter;
use think\Db;

class BookService extends Base
{
    public function getPagedBooks($order = 'id',$where = '1=1',$num = 5)
    {
        $type = 'util\Page';
        if ($this->request->isMobile()){
            $type = 'util\MPage';
        }
        $books = Book::where($where)->with('chapters')->order($order,'desc')
            ->paginate($num,false,
                [
                    'query' => request()->param(),
                    'type'     => $type,
                    'var_page' => 'page',
                ]);
        foreach ($books as &$book){
            $book['chapter_count'] = count($book->chapters);
        }
        return $books;
    }

    public function getPagedBooksAdmin($where = '1=1'){
        $data = Book::where($where);
        $books = $data->with('author,chapters')->order('id','desc')
            ->paginate(5,false,
                [
                    'query' => request()->param(),
                    'type'     => 'util\AdminPage',
                    'var_page' => 'page',
                ]);
        return [
            'books' => $books,
            'count' => $data->count()
        ];
    }

    public function getBooks($order = 'update_time',$where = '1=1',$num = 6){
        $books = Book::where($where)->with('author,chapters')
            ->limit($num)->order($order,'desc')->select();
        foreach ($books as &$book){
            $book['chapter_count'] = count($book->chapters);
            $book['taglist'] = explode('|',$book->tags);
        }
        return $books;
    }

    public function getBooksById($ids){
        if (empty($ids) || strlen($ids)<=0){
            return [];
        }
        $exp = new \think\db\Expression('field(id,'.$ids.')');
        $books = Book::where('id','in',$ids)->with('author,chapters')->order($exp)->select();
        foreach ($books as &$book){
            $book['chapter_count'] = count($book->chapters);
        }
        return $books;
    }

    public function getRandBooks(){
        $books = Db::query('SELECT ad1.id,book_name,summary,cover_url FROM '.$this->prefix.'book AS ad1 JOIN 
(SELECT ROUND(RAND() * ((SELECT MAX(id) FROM '.$this->prefix.'book)-(SELECT MIN(id) FROM '.$this->prefix.'book))+(SELECT MIN(id) FROM '.$this->prefix.'book)) AS id)
 AS t2 WHERE ad1.id >= t2.id ORDER BY ad1.id LIMIT 10');
        foreach ($books as &$book){
            $book['chapter_count'] = Chapter::where('book_id','=',$book['id'])->count();
        }
        return $books;
    }


    public function getByName($name)
    {
        return Book::where('book_name', '=', $name)->find();
    }

    public function getBook($id)
    {
        return Book::get($id);
    }

    public function getTags($id)
    {
        $tags = Book::field('tags')->with('author')->where('id', '=', $id)->find()['tags'];
        $array = explode('|', $tags);
        return $array;
    }

    public function getRand($num)
    {
        $books = Db::query('SELECT a.id,a.book_name,a.summary,a.end,b.author_name FROM 
(SELECT ad1.id,book_name,summary,end,author_id,cover_url
FROM '.$this->prefix.'book AS ad1 JOIN (SELECT ROUND(RAND() * ((SELECT MAX(id) FROM '.$this->prefix.'book)-(SELECT MIN(id) FROM '.$this->prefix.'book))+(SELECT MIN(id) FROM '.$this->prefix.'book)) AS id)
 AS t2 WHERE ad1.id >= t2.id ORDER BY ad1.id LIMIT ' . $num . ') as a
 INNER JOIN author as b on a.author_id = b.id');
        return $books;
    }

    public function getNewest()
    {
        return Book::with('author')->limit(3)->order('update_time', 'desc')->select();
    }

    public function search($keyword){
        return Db::query(
            "select * from ".$this->prefix."book where match(book_name,summary) 
            against ('".$keyword."' IN NATURAL LANGUAGE MODE)"
        );
    }
}