<?php
namespace App\Enums;


class ModelEnums{

    const SINGLE_PAGE = 1;
    const LIST_CONTENT = 2;

    public static $typeList = [
        self::SINGLE_PAGE => '单页',
        self::LIST_CONTENT => '列表+内容'
    ];

    //表单字段类型
    public static $inputType = [
        'text'             => '单行文本',
        'textarea'      => '多行文本',
        'select' => '下拉列表框',
        'radio' => '单选框',
        'checkbox' => '多选框'
    ];

    //调查选项类型
    public static $surveyInputType = [
        'text'             => '单行文本',
        'textarea'      => '多行文本',
        'radio' => '单选框',
        'checkbox' => '多选框'
    ];
}