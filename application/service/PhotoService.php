<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/1/29
 * Time: 12:33
 */

namespace app\service;

use app\model\Photo;
class PhotoService
{
    public function getLastPhoto($chapter_id){
        return Photo::where('chapter_id','=',$chapter_id)->order('id','desc')->limit(1)->find();
    }
}