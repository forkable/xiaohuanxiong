<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/1/30
 * Time: 11:33
 */

namespace app\model;


use think\Model;

class FriendshipLink extends Model
{
    protected $pk='id';
    protected $autoWriteTimestamp = true;

    public function setNameAttr($value){
        return trim($value);
    }

    public function setUrlAttr($value){
        return trim($value);
    }
}