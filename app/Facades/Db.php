<?php
namespace App\Facades;

use Core\Facade;

class Db extends Facade{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'db';
    }

    /**
     * 数据库字段转换
     *
     * @param $sql
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/15 下午12:27
     */
//    public static function raw($sql){
//        return 'raw:'.$sql;
//    }

}