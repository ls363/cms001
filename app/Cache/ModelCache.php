<?php
namespace App\Cache;

/**
 * Class ClassifyCache
 * 栏目缓存
 *
 * @package App\Cache
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/11 上午12:36
 */
class ModelCache{

    //文件缓存前辍
    protected static $prefix = 'model_';

    /**
     * 读取缓存
     *
     * @param $id
     * @return array|mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/11 上午12:39
     */
    public static function getAll(){
        $path = RUNTIMES_PATH . '/cache/'. self::$prefix .'all.php';
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
    public static function setAll($data){
        $path = RUNTIMES_PATH . '/cache/'. self::$prefix . 'all.php';
        return array_to_file($data, $path);
    }

    /**
     * 读取缓存
     *
     * @param $id
     * @return array|mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/11 上午12:39
     */
    public static function getById($id){
        $path = RUNTIMES_PATH . '/cache/'. self::$prefix. $id .'.php';
        return file_to_array($path);
    }

    /**
     * 写缓存
     *
     * @param $id
     * @param $data
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/11 上午12:38
     */
    public static function setById($id, $data){
        $path = RUNTIMES_PATH . '/cache/'. self::$prefix. $id . '.php';
        return array_to_file($data, $path);
    }
}