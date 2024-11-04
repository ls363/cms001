<?php
namespace App\Cache;

/**
 * Class SystemConfigCache
 * 系统配置缓存
 *
 * @package App\Cache
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/11 上午12:36
 */
class SystemConfigCache{

    //文件缓存前辍
    protected static $prefix = 'system_config';

    /**
     * 读取缓存
     *
     * @return array|mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/11 上午12:39
     */
    public static function get(){
        $path = RUNTIMES_PATH . '/cache/'. self::$prefix .'.php';
        return file_to_array($path);
    }

    /**
     * 写缓存
     *
     * @param $data
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/11 上午12:38
     */
    public static function set($data){
        $path = RUNTIMES_PATH . '/cache/'. self::$prefix . '.php';
        return array_to_file($data, $path);
    }

}