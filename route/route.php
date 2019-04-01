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
Route::rule('/rank', 'index/rank/index');
Route::rule('/author/:id', 'index/authors/index');

Route::rule('/ucenter', 'ucenter/users/ucenter');
Route::rule('/bookshelf', 'ucenter/users/bookshelf');
Route::rule('/history', 'ucenter/users/history');
Route::rule('/userinfo', 'ucenter/users/userinfo');
Route::rule('/delfavors', 'ucenter/users/delfavors');
Route::rule('/delhistory', 'ucenter/users/delhistory');
Route::rule('/updateUserinfo', 'ucenter/users/update');
Route::rule('/bindphone', 'ucenter/users/bindphone');
Route::rule('/userphone', 'ucenter/users/userphone');
Route::rule('/sendcode', 'ucenter/users/sendcode');
Route::rule('/verifycode', 'ucenter/users/verifycode');
Route::rule('/login', 'ucenter/account/login');
Route::rule('/register', 'ucenter/account/register');
Route::rule('/logout', 'ucenter/account/logout');
Route::rule('/addfavor', 'index/books/addfavor');
