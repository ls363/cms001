<?php
namespace App\Logics;

use App\Models\NavigationModel;

class NavigationLogic{

    /**
     * 返回ID为key的数据
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:47
     */
    public static function getKvData(array $ids=[])
    {
        $query = NavigationModel::query();
        if(! empty($ids)){
            $query->whereIn('id', $ids);
        }
        return $query->pluck('name','id')->toArray();
    }

    /**
     * 获取全部
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:59
     */
    public static function getAll($refresh=false)
    {
        return NavigationModel::query()->orderBy('sort', 'asc')->pluck('title', 'id');
    }

    /**
     * 单条数据查询
     *
     * @param $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:47
     */
    public static function getById($id)
    {
        return NavigationModel::find($id);
    }

    /**
     * 获取分类详情
     *
     * @param  int  $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午2:18
     */
    public static function getDetail(int $id){
        return NavigationModel::getInstance()->getDetail($id);
    }

    public static function getPageList(){
        return NavigationModel::getInstance()->getPageList();
    }

    /**
     * 添加或者修改数据, 单条数据的修改
     * @param int $id
     * @param array $data
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午4:55
     */
    public static function save(int $id, array $data){
        if($id > 0){
            return NavigationModel::where('id', $id)->update($data);
        }else{
            return NavigationModel::insert($data);
        }
    }

    /**
     * 单条记录删除
     *
     * @param  int  $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午5:00
     */
    public static function delete(int $id){
        return NavigationModel::getInstance()->where('id', $id)->delete();
    }

    /**
     * 设置状态
     *
     * @param  int  $id
     * @param  int  $state
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/6/1 下午11:46
     */
    public static function setState(int $id, int $state){
        if($id <= 0){
            return false;
        }
        return NavigationModel::where('id', $id)->update(['state' => $state]);
    }

    /**
     * 设置排序
     *
     * @param  int  $id
     * @param  int  $state
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/6/1 下午11:46
     */
    public static function setSort(int $id, int $sort){
        if($id <= 0){
            return false;
        }
        return NavigationModel::where('id', $id)->update(['sort' => $sort]);
    }


}