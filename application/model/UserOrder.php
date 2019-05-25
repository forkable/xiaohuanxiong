<?php


namespace app\model;


use think\Model;

class UserOrder extends Model
{
    protected $pk='id';
    protected $autoWriteTimestamp = true;

    public function setSummaryAttr($value){
        return trim($value);
    }
}