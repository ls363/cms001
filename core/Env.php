<?php
namespace Core;

class Env
{
    private static $config = [];

    /**
     * 加载配置文件
     * @return void
     */
    public static function load()
    {
        if(! empty(self::$config)){
            return;
        }
        $filePath = ROOT_PATH . '/public/.env';
        if (!file_exists($filePath)) {
        //    throw new \Exception('配置文件' . $filePath . '不存在');
            return;
        }
        //返回二维数组
        self::$config = parse_ini_file($filePath, true);
    }

    /**
     * 获取环境变量值
     * @access public
     * @param  string  $name  环境变量名 为空返回全部
     * @param  string  $default  默认值
     * @return mixed
     */
    public static function get(string $name='', $default = null)
    {
        if(empty($name)){
            return self::$config;
        }
        return array_key_exists($name, self::$config) ? self::$config[$name] : $default;
    }
}