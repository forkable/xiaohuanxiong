<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2018/10/9
 * Time: 16:06
 */

namespace app\model;


use think\Model;

class Admin extends Model
{
    protected $pk='id';
    protected $autoWriteTimestamp = true;

    public function setUsernameAttr($value){
        return trim($value);
    }

    public function setPasswordAttr($value){
        return trim($value);
    }
}