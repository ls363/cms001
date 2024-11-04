<?php
namespace App\Logics;

use Core\Singleton;
use App\Models\CompanyModel;
use App\Cache\CompanyCache;

class CompanyLogic{

    use Singleton;


    /**
     * 获取数据
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 下午2:41
     */
    public function get(){
        return CompanyModel::first();
    }

    /**
     * 保存数据
     *
     * @param  array  $data
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 下午2:42
     */
    public static function save(array $data){
        $info = CompanyModel::first();
        if($info){
            $result = CompanyModel::where('id', $info['id'])->update($data);
        }else{
            $result = CompanyModel::insert($data);
        }
        //刷新缓存
        self::getCompanyCache(true);
        return $result;

    }

    /**
     * 获取编辑页的数据
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 下午2:41
     */
    public static function getInfoForEdit(){
        $info = self::getCompanyInfo();
        return $info;
    }

    /**
     * 优化从缓存加载系统配置
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/11 上午9:01
     */
    public static function getCompanyCache($isRefresh = false){
        $data = CompanyCache::get();
        if(empty($data) || $isRefresh){
            $data = self::getCompanyInfo();
            CompanyCache::set($data);
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
    public static function getCompanyInfo(){
        return CompanyModel::first();
    }
}