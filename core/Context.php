<?php
namespace Core;

/**
 * Class Context
 * 用于保存上下文全局变量
 *
 * @package Core
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/22 下午2:37
 */
class Context{

    public static $context = [];

    public static function all(){
        return self::$context;
    }

    /**
     * 读取
     *
     * @param  string  $name
     * @param  string  $default
     * @return mixed|string
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 上午10:04
     */
    public static function get(string $name, $default=''){
        if(strpos($name, '.')){
            $keyArray = explode('.', $name);
            $config = [];
            foreach ($keyArray as $i=>$key){
                if($i == 0) {
                    //存在到下一级
                    if (isset(self::$context[$key])) {
                        $config = self::$context[$key];
                        continue;
                    }
                    //不存在退出
                    return $default;
                }else{
                   if(isset($config[$key])){
                        $config = $config[$key];
                        continue;
                   }
                    //不存在退出
                    return $default;
                }
            }
            return $config;
        }else {
            return self::$context[$name] ?? $default;
        }
    }

    /**
     * 设置
     *
     * @param  string  $name
     * @param $value
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 上午10:04
     */
    public static function set(string $configName, $value){

        if(strpos($configName, '.')){
            $config = & self::$context;
            $keyArray = explode('.', $configName);
            $len = count($keyArray);
            foreach ($keyArray as $i=>$key){
                if($i == $len-1){
                    $config[$key] = $value;
                }else {
                    //转成树型结构
                    if (!isset($config[$key])) {
                        $config[$key] = [];
                    }
                    $config = &$config[$key];
                }
            }
        }else {
            self::$context[$configName] = $value;
        }
    }

}