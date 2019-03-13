<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\facade\Route;

Route::rule('/tag/[:name]', 'index/tags/index');
Route::rule('/book/:id', 'index/books/index');
Route::rule('/booklist', 'index/books/booklist');
Route::rule('/chapter/:id', 'index/chapters/index');
Route::rule('/search/[:keyword]', 'index/search');
Route::rule('/bookshelf', 'index/bookshelf');
Route::rule('/rank', 'index/rank/index');
Route::rule('/author/:id', 'index/authors/index');
Route::rule('/bookshelf', 'index/index/bookshelf');
Route::rule('/rank', 'index/rank/index');