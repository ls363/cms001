<?php
namespace App\Enums;

class BannerEnums
{
    //类型
    public static $typeList = [
        1 => '轮播图',
        2 => '普通BANNER'
    ];

    //上下架
    public static $shelfRange = [
        1 => '上架',
        2 => '下架'
    ];

    //显示/隐藏
    public static $showRange = [
        1 => '显示',
        2 => '隐藏'
    ];

    //开启/关闭
    public static $stateRange = [
        1 => '开启',
        0 => '关闭'
    ];

    //开启/关闭
    public static $contentMakeRange = [
        1 => '文章保存时生成',
        2 => '点击生成按钮'
    ];

}