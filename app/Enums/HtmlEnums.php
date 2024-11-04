<?php
namespace App\Enums;


class HtmlEnums
{
    const HTML_PATH = '/'; // 生成的页面路径, 暂未使用
    const TEMPLATE_PATH = '/templates/'; // 模板路径
    const TEMPLATE_CACHE_PATH = '/runtimes/template_cache/'; // 模板路径
    const HTML_BR_CODE = 'zxkvbCrLf'; //换行符的处理
    const LOOP_TAG = 'loop';
    const NEXT_LOOP_TAG = 'nextloop';
    //配置忽略的文件夹
    public static $ignoreFolder = ['..','.', 'images', 'js','style','.DS_Store','common','bootstrap','css','datetimepicker','fileinput', 'font-awesome-4.7.0','swiper-4.3.5'];
}