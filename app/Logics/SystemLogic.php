<?php
namespace App\Logics;

use App\Models\SystemConfigModel;
use App\Cache\SystemConfigCache;

class SystemLogic{

    /**
     * 获取数据
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 下午2:41
     */
    public static function getProtocol(){
        return SystemConfigModel::first(['protocol', 'privacy']);
    }

    /**
     * 优化从缓存加载系统配置
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/11 上午9:01
     */
    public static function getSystemCache($isRefresh = false){
        $data = SystemConfigCache::get();
        if(empty($data) || $isRefresh){
            $data = self::getSystemConfig();
            SystemConfigCache::set($data);
        }
        return $data;
    }

    /**
     * 获取系统配置
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/17 上午11:36
     */
    public static function getSystemConfig(){
        $info = SystemConfigModel::first();
        return $info;
    }

    /**
     * 获取编辑页的数据
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 下午2:41
     */
    public static function getInfoForEdit(){
        $info = self::getSystemConfig();
        return $info;
    }

    /**
     * 保存数据
     *
     * @param  int  $id
     * @param  array  $data
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 下午2:42
     */
    public static function save(array $data){
        $info = SystemConfigModel::first();
        unset($data['id']);
        if($info){
            $result = SystemConfigModel::where('id', $info['id'])->update($data);
        }else{
            $result = SystemConfigModel::insert($data);
        }
        //刷新缓存
        self::getSystemCache(true);
        return $result;
    }
}