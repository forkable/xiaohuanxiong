<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/1/31
 * Time: 14:53
 */

namespace app\admin\validate;


use think\Validate;

class Photo extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'chapter_id' => 'require',
        'book_id' => 'require',
        'pic_order' => 'require|float'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'chapter_id' => '章节id必须',
        'book_id' => '漫画id必须',
        'pic_order' => '图片order必须'
    ];

    protected $scene = [
        'edit'  =>  ['chapter_id','book_id','pic_order'],
        'upload' => ['chapter_id','book_id']
    ];
}