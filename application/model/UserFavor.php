<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/3/23
 * Time: 17:10
 */

namespace app\model;


use think\Model;

class UserFavor extends Model
{
    protected $pk='id';
    protected $autoWriteTimestamp = true;

    public function book(){
        return $this->hasOne('Book','id');
    }
}