<?php

namespace App\Logics;

use App\Enums\PageEnums;
use App\Models\Base\Link;
use core\Singleton;

class LinkLogic
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
        $query = Link::query();
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
        return Link::query()->orderBy('sort', 'asc')->get();
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
        return Link::find($id);
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
        $query = Link::query();
        if(isset($input['category_id']) && $input['category_id'] > 0){
            $query->where('category_id', $input['category_id']);
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
            $categoryList = LinkCategoryLogic::getAll();
            foreach ($list as & $v) {
                if($v['category_id'] > 0){
                    $v['category_name'] = isset($categoryList[$v['category_id']]) ? $categoryList[$v['category_id']] : '';
                }else{
                    $v['category_name'] = '';
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
                return Link::where('id', $id)->update($data);
            } else {
                return Link::insert($data);
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除
     */
    public static function destroy($id)
    {
        try {
            $model = Link::query()->find($id);
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
            return Link::query()->whereIn('id', $ids)->delete();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 批量移动记录到栏目
     *
     * @param  array  $ids
     * @param  int  $category_id
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/9 下午4:04
     */
    public static function move(array $ids, int $category_id)
    {
        try {
            return Link::query()->whereIn('id', $ids)->update(['category_id' => $category_id]);
        } catch (\Exception $e) {
            return false;
        }
    }

}
