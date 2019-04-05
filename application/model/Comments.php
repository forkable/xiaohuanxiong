<?php


namespace app\model;


use think\Model;

class Comments extends Model
{
    protected $pk='id';
    protected $autoWriteTimestamp = true;
}