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
class ClassifyCache{

    //文件缓存前辍
    protected static $prefix = 'classify_';

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

    /**
     * 删除缓存
     *
     * @param $id
     * @param $data
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/11 上午12:38
     */
    public static function deleteById($id){
        $path = RUNTIMES_PATH . '/cache/'. self::$prefix. $id . '.php';
        return unlink($path);
    }


    /**
     * 读取缓存
     *
     * @param $id
     * @return array|mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/11 上午12:39
     */
    public static function getAll(){
        $path = RUNTIMES_PATH . '/cache/'. self::$prefix .'all_id.php';
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
        $path = RUNTIMES_PATH . '/cache/'. self::$prefix . 'all_id.php';
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
    public static function getAllRoute(){
        $path = RUNTIMES_PATH . '/cache/'. self::$prefix .'all_route.php';
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
    public static function setAllRoute($data){
        $path = RUNTIMES_PATH . '/cache/'. self::$prefix . 'all_route.php';
        return array_to_file($data, $path);
    }

    /**
     * 获取子ID
     *
     * @param $parent_id
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/16 上午11:21
     */
    public static function getChildsByParentId($parent_id){
        $path = RUNTIMES_PATH . '/cache/'. self::$prefix . 'childs_'. $parent_id .'.php';
        return file_to_array($path);
    }

    /**
     * 删除子ID
     *
     * @param $parent_id
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/16 上午11:21
     */
    public static function deleteChildsByParentId($parent_id){
        $path = RUNTIMES_PATH . '/cache/'. self::$prefix . 'childs_'. $parent_id .'.php';
        if(is_file($path)){
            unlink($path);
            return true;
        }
        return false;
    }

    /**
     * 保存子目录
     *
     * @param $parent_id
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/16 上午11:21
     */
    public static function setChildsByParentId($parent_id, $ids){
        $path = RUNTIMES_PATH . '/cache/'. self::$prefix . 'childs_'. $parent_id .'.php';
        return array_to_file($ids, $path);
    }


}