<?php
namespace App\Logics;

use core\Singleton;
use App\Models\MenuModel;

class RoleLogic{

    use Singleton;
    
    /**
     * 获取文章详情
     *
     * @param  int  $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午2:18
     */
    public function getDetail(int $id){
        return MenuModel::getInstance()->getDetail($id);
    }

    public function getPageList(){
        return MenuModel::getInstance()->getPageList();
    }

    //获取文章测试
    public function getArticleList()
    {
        $articleList = [];
        $articleList[] = [
            'id'         => 1,
            'title'      => '测方式文章',
            'created_at' => '2022-04-24'
        ];
        $articleList[] = [
            'id'         => 2,
            'title'      => '测方式文章22',
            'created_at' => '2022-04-24'
        ];
        return $articleList;
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
            return MenuModel::where('id', $id)->update($data);
        }else{
            return MenuModel::insert($data);
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
        return MenuModel::getInstance()->where('id', $id)->delete();
    }

}