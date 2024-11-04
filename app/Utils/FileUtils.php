<?php
namespace App\Utils;

class FileUtils
{

    /**
     * 删除目录
     *
     * @param $dir
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/16 上午12:34
     */
    public static function deldir($dir)
    {
        if(is_dir($dir) === false){
            return;
        }
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    self::deldir($fullpath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

}