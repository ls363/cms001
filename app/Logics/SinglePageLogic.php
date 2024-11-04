<?php

namespace App\Logics;

use App\Enums\PageEnums;
use App\Models\Base\Article;

class SinglePageLogic
{
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
        return Article::find($id);
    }

    /**
     * 单条数据查询
     *
     * @param $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:47
     */
    public static function getDetail($id)
    {
        return Article::where('class_id', $id)->first();
    }

    /**
     * 单条数据查询, 根据class_id查询
     *
     * @param $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:47
     */
    public static function getDetailByClassId($class_id)
    {
        return Article::where('class_id', $class_id)->first();
    }

    /**
     * 获取单页列表，一般情况单页比较少，不需要分页，单页是每个栏目只有一篇文章
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/24 下午6:08
     */
    public static function getList(){
        //获得单页模型ID
        $modelId = ContentModuleLogic::getSinglePageId();
        //print_r($modelId);exit;
        //获取所有单页列表
        $list = ClassifyLogic::getListByModelId($modelId);
        //print_r($list);exit;
        if(empty($list)){
            return [];
        }
        //获取所有栏目ID
        $classIds = array_column($list, 'id');
//        print_r($classIds);exit;
        //获取单页的标题
        $pageKv = Article::query()->whereIn('class_id', $classIds)->keyBy('class_id')->get(['title', 'class_id', 'hits']);
        foreach ($list as & $v){
            if(isset($pageKv[$v['id']])){
                $v['page_title'] = $pageKv[$v['id']]['title'];
                $v['hits'] = $pageKv[$v['id']]['hits'];
            }else{
                $v['page_title'] = '';
                $v['hits'] = 0;
            }
        }
        return $list;
    }

    /**
     * 获取全部
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:59
     */
    public static function getPageList($input=[])
    {
        //获得单页模型ID
        $modelId = ContentModuleLogic::getSinglePageId();
        //获取所有单页列表
        $classIds = ClassifyLogic::getIdsByModelId($modelId);
        $query = Article::query();
        $query->where('class_id', $classIds);
        $search_type = isset($input['search_type']) ? $input['search_type'] : '';
        $search_text = isset($input['search_text']) ? $input['search_text'] : '';
        //搜索字段处理
        if($search_text != '' && $search_type != '' && in_array($search_type, ['title', 'url', 'intro'])){
            $query->where($search_type, 'like', "%{$search_text}%");
        }
        $query->orderBy('id', 'desc');
        $result = $query->paginate(PageEnums::PAGE_SIZE, ['*'], isset($input['page']) ? $input['page'] : 1);
        $list = $result['list'];
        if(! empty($list)) {
            $categoryList = ClassifyLogic::getAll();
            foreach ($list as & $v) {
                if($v['class_id'] > 0){
                    $v['class_name'] = isset($categoryList[$v['class_id']]) ? $categoryList[$v['class_id']] : '';
                }else{
                    $v['class_name'] = '';
                }
            }
        }
        return [
            'list' => $list,
            'page' => $result['page'],
            'total' => $result['total'],
            'page_size' => $result['pageSize']
        ];
    }


    /**
     * 保存数据
     *
     * @param $id
     * @param $data
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:48
     */
    public static function save($data)
    {
        $class_id = $data['class_id'] ?? 0;
        if($class_id <= 0){
            return false;
        }
        try {
            unset($data['id']);
            $info = Article::query()->where('class_id', $class_id)->first();
            if (empty($info)) {
                return Article::insert($data);
            } else {
                unset($data['class_id']);
                return Article::query()->where('class_id', $class_id)->update($data);
            }
        } catch (\Exception $e) {
            return false;
        }
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
        return Article::where('id', $id)->update(['state' => $state]);
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
    public static function setField(int $id, string $field, int $state){
        if($id <= 0){
            return false;
        }
        if(in_array($field, ['state', 'is_top', 'is_recommend', 'is_slide']) === false){
            return false;
        }
        return Article::where('id', $id)->update([$field => $state]);
    }

    /**
     * 删除
     */
    public static function destroy($id)
    {
        try {
            $model = Article::query()->find($id);
            if (empty($model)) {
                throw new \Exception('数据已删除');
            }
            return $model->delete();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 批量删除
     *
     * @param  array  $ids
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/9 下午4:20
     */
    public static function batchDelete(array $ids)
    {
        try {
            return Article::query()->whereIn('id', $ids)->delete();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 批量移动记录到栏目
     *
     * @param  array  $ids
     * @param  int  $class_id
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/9 下午4:04
     */
    public static function move(array $ids, int $class_id)
    {
        try {
            return Article::query()->whereIn('id', $ids)->update(['class_id' => $class_id]);
        } catch (\Exception $e) {
            return false;
        }
    }

}
