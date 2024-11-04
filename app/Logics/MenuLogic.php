<?php
namespace App\Logics;

use App\Utils\TreeUtils;
use App\Models\MenuModel;
use App\Cache\MenuCache;

class MenuLogic{

    /**
     * 获取内容管理的ID
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 下午8:52
     */
    public function getContentManageId(){
        return MenuModel::query()->where('parent_id', 0)->where('title', '内容管理')->value('id');
    }

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

    public function getParentList(){
        return MenuModel::getInstance()->getParentList();
    }

    /**
     * 获取树型列表
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/14 下午7:47
     */
    public function getTreeList(){
        $list = MenuCache::getAll();
        if(empty($list)) {
            $result = MenuModel::getInstance()->getList();
            foreach($result as &$v){
                $v['uri'] = str_replace('/admin/', '/'. config('admin_dir') .'/',$v['uri']);
            }
            $list = TreeUtils::getTreeArrayByListArray($result, 0);
            MenuCache::setAll($list);
        }
        return $list;
    }


    /**
     * 添加或者修改数据, 单条数据的修改
     * @param int $id
     * @param array $args
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午4:55
     */
    public function save(int $id, array $args){
        if($id > 0){
            unset($args['id']);
            $result = MenuModel::where('id', $id)->update($args);
        }else{
            $result = MenuModel::insert($args);
        }
        MenuCache::deleteAll();
        return $result;
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
        $result = MenuModel::where('id', $id)->delete();
        MenuCache::deleteAll();
        return $result;
    }

}