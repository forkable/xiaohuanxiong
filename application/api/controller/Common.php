<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/3/25
 * Time: 17:57
 */

namespace app\api\controller;

use think\facade\App;
use think\facade\Cache;
use think\Controller;

class Common extends Controller
{
    public function clearcache(){
        $key = input('api_key');
        if (empty($key) || is_null($key)){
            return 'api密钥错误！';
        }
        Cache::clear('redis');
        $rootPath = App::getRootPath();
        delete_dir_file($rootPath . '/runtime/cache/') && delete_dir_file($rootPath . '/runtime/temp/');
        return '清理成功';
    }
}