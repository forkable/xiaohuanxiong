<?php
/**
 * Created by PhpStorm.
 * User: hiliq
 * Date: 2019/1/31
 * Time: 14:42
 */

namespace app\admin\validate;


use think\Validate;

class Chapter extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'chapter_name' => 'require',
        'book_id' => 'require',
        'chapter_order' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'chapter_name' => '名称必须',
        'book_id' => 'book_id必须',
        'chapter_order' => '章节order必须'
    ];
}