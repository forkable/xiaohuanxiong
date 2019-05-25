<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/3/23
 * Time: 17:10
 */

namespace app\model;

use think\model\Pivot;
class UserBook extends Pivot
{
    protected $autoWriteTimestamp = true;
}