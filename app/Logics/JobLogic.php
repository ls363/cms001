<?php

namespace App\Logics;

use App\Enums\PageEnums;
use App\Models\Base\Job;
use core\Singleton;

class JobLogic
{
    use Singleton;


    /**
     * 返回ID为key的数据
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:47
     */
    public static function getKvData(array $ids=[])
    {
        $query = Job::query();
        if(! empty($ids)){
            $query->whereIn('id', $ids);
        }
        return $query->pluck('name','id');
    }


    /**
     * 获取全部
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:59
     */
    public static function getAll()
    {
        return Job::query()->orderBy('sort', 'asc')->get();
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
        return Job::find($id);
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
        $query = Job::query();
        if(isset($input['class_id']) && $input['class_id'] > 0){
            $query->whereIn('class_id', ClassifyLogic::getAllChildClassId($input['class_id']));
        }
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
    public static function save($id, $data)
    {
        try {
            if ($id > 0) {
                unset($data['id']);
                Job::query()->where('id', $id)->update($data);
            } else {
                $seo_title = $data['seo_title'] ?? '';
                if(empty($seo_title)){
                    $data['seo_title'] = $data['title'];
                }
                $id = Job::insert($data);
            }
            return $id;
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
        return Job::where('id', $id)->update(['state' => $state]);
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
        return Job::where('id', $id)->update([$field => $state]);
    }

    /**
     * 删除
     */
    public static function delete($id)
    {
        try {
            $model = Job::query()->find($id);
            if (empty($model)) {
                throw new \Exception('数据已删除');
            }
            return Job::where('id', $id)->delete();
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
            return Job::query()->whereIn('id', $ids)->delete();
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
            return Job::query()->whereIn('id', $ids)->update(['class_id' => $class_id]);
        } catch (\Exception $e) {
            return false;
        }
    }

}
