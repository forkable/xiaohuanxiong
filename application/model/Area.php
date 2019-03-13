<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/3/7
 * Time: 12:26
 */

namespace app\model;


use think\Model;

class Area extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    public function setTagNameAttr($value){
        return trim($value);
    }
}