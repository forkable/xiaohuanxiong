<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/3/1
 * Time: 21:21
 */

namespace app\api\controller;

use app\model\Book;
use app\model\Chapter;
use app\model\Photo;
use think\Controller;

class Read extends Controller
{
    public function book(){
        $api_key = input('api_key');
        if (empty($api_key) || is_null($api_key)){
            return json(['err' => 1,'msg' => 'api密钥错误']);
        }
        if ($api_key != config('site.api_key')){
            return json(['err' => 1,'msg' => 'api密钥错误']);
        }
        $size = input('size');
        if (empty($size) || is_null($size)){
            $size = 5;
        }
        $page = input('page');
        if (empty($page) || is_null($page)){
            $page = 1;
        }
        $offset = $size * ($page - 1);
        $books = Book::with('author')->limit($offset,$size)->select();
        return json(['err' => 0, 'books' => $books]);
    }

    public function chapter(){
        $api_key = input('api_key');
        if (empty($api_key) || is_null($api_key)){
            return json(['err' => 1,'msg' => 'api密钥错误']);
        }
        if ($api_key != config('site.api_key')){
            return json(['err' => 1,'msg' => 'api密钥错误']);
        }
        $bid = input('bid');
        if (empty($bid) || is_null($bid)){
            return json(['err' => 1, 'msg' => 'bid为必须参数']);
        }
        $chapters = Chapter::where('book_id','=',$bid)->select();
        return json(['err' => 0, 'chapters' => $chapters]);
    }

    public function pic(){
        $api_key = input('api_key');
        if (empty($api_key) || is_null($api_key)){
            return json(['err' => 1,'msg' => 'api密钥错误']);
        }
        if ($api_key != config('site.api_key')){
            return json(['err' => 1,'msg' => 'api密钥错误']);
        }
        $cid = input('cid');
        if (empty($cid) || is_null($cid)){
            return json(['err' => 1, 'msg' => 'cid为必须参数']);
        }
        $pics = Photo::where('chapter_id','=',$cid)->select();
        return json(['err' => 0, 'pics' => $pics]);
    }
}