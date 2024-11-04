<?php
namespace App\Utils;
class CacheUtils{

    public static function set($key, $value, $expired){
        $path = RUNTIMES_PATH . '/data_cache/'. $key;
        //检查目录
        check_dir_by_path($path);
        $data = [
            'value' => $value,
            'expired' => time() + $expired,
        ];
        $str_cache = json_encode($data);
        file_put_contents($path, $str_cache);
    }

    public function get($key){
        $path = RUNTIMES_PATH . '/data_cache/'. $key;
        if(! file_exists($path)){
            return '';
        }
        $str_cache = file_get_contents($path);
        $data = json_decode($str_cache, true);
        if($data['expired'] > time()){
            return $data['value'];
        }else{
            unlink($path);
        }
    }

    public function del($key){
        $path = RUNTIMES_PATH . '/data_cache/'. $key;
        if(file_exists($path)){
            unlink($path);
        }
    }
}