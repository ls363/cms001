<?php

namespace App\Logics;

use App\Cache\InnerLinkCache;
use App\Enums\PageEnums;
use App\Models\Base\InnerLink;
use core\Singleton;

class InnerLinkLogic
{
    use Singleton;

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
        return InnerLink::find($id);
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
        $query = InnerLink::query();
        if(isset($input['model_id']) && $input['model_id'] > 0){
            $query->where('model_id', $input['model_id']);
        }
        $search_type = isset($input['search_type']) ? $input['search_type'] : '';
        $search_text = isset($input['search_text']) ? $input['search_text'] : '';
        //搜索字段处理
        if($search_text != '' && $search_type != '' && in_array($search_type, ['title', 'url', 'intro'])){
            $query->where($search_type, 'like', "%{$search_text}%");
        }
        $query->orderBy('id', 'desc');
        return $query->paginate(PageEnums::PAGE_SIZE, ['*'], isset($input['page']) ? $input['page'] : 1);
    }

    public function replaceInnerLink($modelId, &$content){
        $list = InnerLinkCache::getAll();
        if(empty($list)){
            $list = InnerLink::where('model_id', $modelId)->get();
            InnerLinkCache::setAll($list);
        }
        if(empty($list)){
            return;
        }
        foreach ($list as $v){
            $content = str_replace($v['title'], '<a href="'. $v['url'] .'" target="_blank">'. $v['title'] .'</a>', $content);
        }
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
                return InnerLink::where('id', $id)->update($data);
            } else {
                return InnerLink::insert($data);
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
            $model = InnerLink::query()->find($id);
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
            return InnerLink::query()->whereIn('id', $ids)->delete();
        } catch (\Exception $e) {
            return false;
        }
    }

}
