<?php
namespace Core;

Trait Singleton
{
    private static $_instance = null;

    /**
     * 私有化默认构造方法，保证外界无法直接实例化
     */
    private function __construct()
    {

    }

    //覆盖__clone()方法，禁止克隆
    private function __clone() {

    }

    /**
     * 获取单例
     *
     * @return null
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/23 下午11:18
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }

}