<?php
namespace App\Logics;

use App\Cache\ClassifyCache;
use App\Models\ClassifyModel;
use App\Utils\ClassUtils;
use App\Models\ContentModuleExtendModel;

class ClassifyLogic{

    /**
     * 返回ID为key的数据
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:47
     */
    public static function getKvData(array $ids=[])
    {
        $query = ClassifyModel::query();
        if(! empty($ids)){
            $query->whereIn('id', $ids);
        }
        return $query->pluck('title','id')->toArray();
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
        return ClassifyModel::query()->orderBy('sort', 'asc')->pluck('title', 'id');
    }


    /**
     * 获取下拉框数据
     *
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/8 上午12:01
     */
    public static function getSelectOption($model_id=0){
        $query = ClassifyModel::query();
        if($model_id > 0){
            $query->where('model_id', $model_id);
        }
        $query->select(['id','parent_id','title','sort']);
        $query->orderBy('parent_id', 'asc');
        $query->orderBy('sort', 'asc');
        $result = $query->get();
        $list = [];
        foreach ($result as $v){
            $list[$v['parent_id']][] = $v;
        }
        $instance = new ClassUtils();
        $instance->setData($list);
        $result = $instance->showTopClass(0, 'title');
        //一级分类的时候要选择顶级分类
        if($model_id == 0) {
            $result = '<option value="0">顶级栏目</option>' . $result;
        }else{
            $module =ContentModuleLogic::getDetail($model_id);
            //$result = '<option value="0">'. ($module['title'] ?? '') .'分类</option>'. $result;
            $result = '<option value="0">请选择栏目</option>'. $result;
        }
        return $result;
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
        if($id == 0){
            return [];
        }
        return ClassifyModel::getInstance()->getDetail($id);
    }

    public static function getList(){
        $result = ClassifyModel::getInstance()->getList();
        $moduleList = ContentModuleLogic::getAll();
        $list = [];
        foreach ($result as $v){
            if($v['model_id'] == 0){
                $v['model_name'] = '未配置';
            }else{
                $v['model_name'] = isset($moduleList[$v['model_id']]) ? $moduleList[$v['model_id']] : '';
            }
            $list[$v['parent_id']][] = $v;
        }
        $instance = new ClassUtils();
        $instance->setData($list);
        $data = $instance->getTopClassHtml(0, 'title');
        return $data;
    }

    /**
     * 刷新缓存
     *
     * @param $id
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/12 下午2:56
     */
    public static function refreshCache($id){
        self::getByIdCache($id, true);
        self::getAllFromCache(true);
        self::getRouteList(true);
        //删除子节点缓存
        ClassifyCache::deleteChildsByParentId($id);
    }

    /**
     * 删除栏目缓存
     *
     * @param $id
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/12 下午2:56
     */
    public static function deleteCache($id){
        //删除单个栏目的缓存
        ClassifyCache::deleteById($id);
        self::getAllFromCache(true);
        //删除子节点缓存
        ClassifyCache::deleteChildsByParentId($id);
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
            unset($data['id']);
            $num = ClassifyModel::query()->where('parent_id', $id)->count();
            $data['child_num'] = $num;
            $res = ClassifyModel::where('id', $id)->update($data);

            self::refreshCache($id);
            return $res;
        }else{
            $id = ClassifyModel::insert($data);
            $parent_id = $data['parent_id'] ?? 0;
            if($parent_id > 0){
                $num = ClassifyModel::query()->where('parent_id', $parent_id)->count();
                ClassifyModel::where('id', $parent_id)->update(['child_num' => $num]);
            }
            self::refreshCache($id);
            return $id;
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
        log_error('delete', $id);
        self::deleteCache($id);
        $childs = self::getAllChildClassId($id);
        if(! empty($childs)){
            $childs[] = $id;
        }else{
            $childs = [$id];
        }
        $result = ClassifyModel::getInstance()->whereIn('id', $childs)->delete();
        return $result;
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
        return ClassifyModel::where('id', $id)->update(['state' => $state]);
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
        return ClassifyModel::where('id', $id)->update(['sort' => $sort]);
    }

    /**
     * 根据内容模型，获取栏目ID
     *
     * @param  int  $modelId
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/24 下午6:00
     */
    public static function getIdsByModelId(int $modelId){
        return ClassifyModel::where('model_id', $modelId)->pluck('id');
    }

    /**
     * 根据内容模型，获取栏目列表
     *
     * @param  int  $modelId
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/24 下午6:00
     */
    public static function getListByModelId(int $modelId){
        return ClassifyModel::where('model_id', $modelId)->get();
    }

    /**
     * 从缓存取全部分类的数据
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/12 下午2:51
     */
    public static function getAllFromCache($isRefresh=false){
        $dataList = ClassifyCache::getAll();
        if(empty($dataList) || $isRefresh) {
            $query = ClassifyModel::query();
            $dataList = $query->where('state', 1)
                ->select(['id', 'parent_id', 'title', 'url'])
                ->keyBy('id')->get();
            ClassifyCache::setAll($dataList);
        }
        return $dataList;
    }

    /**
     * 获取路径
     *
     * @param $class_id
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/25 下午11:53
     */
    public static function getPosition($class_id){
        $dataList = self::getAllFromCache();
        //初始化ID数组
        $array = [];
        do {
            $result = $dataList[$class_id];
            $class_id = $result['parent_id'];
            array_unshift($array, $result);
        } while ($result['parent_id'] > 0);
        return $array;
    }

    /**
     * 获取路径
     *
     * @param $class_id
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/25 下午11:53
     */
    public static function getTemplate($id){
        return self::getByIdCache($id);
    }

    /**
     * 根据parentId获取所有子class_id
     *
     * @param $parentId
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/28 下午10:54
     */
    public static function getAllChildClassId($parentId){
        if(empty($parentId)){
            return [];
        }
        $ids = ClassifyCache::getChildsByParentId($parentId);
        if(empty($ids)) {
            $list = ClassifyModel::getInstance()->keylist('parent_id', ['id', 'model_id', 'parent_id', 'title']);
            $util = new ClassUtils();
            $util->setData($list);
            $ids = $util->getChildClassId($parentId);
            if (empty($ids)) {
                return [$parentId];
            }
            $ids[] = $parentId;
            ClassifyCache::setChildsByParentId($parentId, $ids);
        }
        return $ids;
    }

    /**
     * 根据栏目ID获取扩展表和所有的扩展字段
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/29 下午7:03
     */
    public static function getExtendFields($id){
        $info = ClassifyModel::find($id);
        //print_r($info);exit;
        $model_id = $info['model_id'];
        //print_r($model_id);exit;
        $fields = ContentModuleExtendModel::getFieldsByModelId($model_id);
        return [
            'table' => 'content_extend_'. $model_id,
            'fields' => $fields
        ];
    }

    /**
     * 获取可用的路由, url为空， list_{id}为路由
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/7 下午5:25
     */
    public static function getRouteList($isRefresh = false){
        $list = ClassifyCache::getAllRoute();
        if(empty($list) || $isRefresh) {
            $list = ClassifyModel::query()->where('state', 1)->get(['id', 'model_id', 'url']);
            ClassifyCache::setAllRoute($list);
        }
        return $list;
    }

    /**
     * 获取栏目信息
     *
     * @param $id
     * @return array|mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/11 上午8:38
     */
    public static function getByIdCache($id, $isRefresh=false){
        if($id <= 0){
            return [];
        }
        $data = ClassifyCache::getById($id);
        if(empty($data) || $isRefresh) {
            $data = ClassifyModel::find($id);
            ClassifyCache::setById($id, $data);
        }
        return $data;
    }

    //批量获取子菜单
    public static function batchGetChildList(array & $list){
        if(empty($list)){
            return [];
        }
        //取出所有ID
        $ids = array_column($list, 'id');
        //按照parent_id分组返回
        $result = ClassifyModel::query()->whereIn('parent_id', $ids)->get('id,parent_id,title,alias,url,cover,banner','parent_id');
        $childGroupList = [];
        foreach ($result as $v){
            $childGroupList[$v['parent_id']][] = $v;
        }
        foreach ($list as & $v){
            if(isset($childGroupList[$v['id']])){
                $v['child_list'] = $childGroupList[$v['id']];
            }else{
                $v['child_list'] = [];
            }
        }
        $childGroupList = null;
        $result = null;
        //dd($list);
    }

}