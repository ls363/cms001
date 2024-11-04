<?php
namespace App\Logics;

use App\Models\SinglePageModel;
use core\Singleton;

class SinglePageBakLogic{

    use Singleton;

    /**
     * 获取分类详情
     *
     * @param  int  $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午2:18
     */
    public function getDetail(int $id){
        return SinglePageModel::getInstance()->getDetail($id);
    }

    public function getPageList(){
        return SinglePageModel::getInstance()->getPageList();
    }

    /**
     * 添加或者修改数据, 单条数据的修改
     * @param int $id
     * @param array $data
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午4:55
     */
    public function save(int $id, array $data){
        if($id > 0){
            return SinglePageModel::where('id', $id)->update($data);
        }else{
            return SinglePageModel::insert($data);
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
    public function delete(int $id){
        log_error('delete', $id);
        return SinglePageModel::getInstance()->where('id', $id)->delete();
    }

}