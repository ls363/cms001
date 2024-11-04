<?php
namespace App\Logics;

use App\Enums\ModelEnums;
use App\Models\Base\Menu;
use App\Models\ContentModuleModel;
use App\Cache\ModelCache;
use core\Singleton;

class ContentModuleLogic{

    use Singleton;

    /**
     * 获取单个模型信息
     *
     * @param $id
     * @return array|mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/11 上午8:38
     */
    public static function getByIdCache($id, $isRefresh=false){
        $data = ModelCache::getById($id);
        if(empty($data) || $isRefresh) {
            $data = ContentModuleModel::find($id);
            ModelCache::setById($id, $data);
        }
        return $data;
    }


    /**
     * 获取模块类型
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/10 上午7:42
     */
    public static function getModuleList($refresh=false){
        $list = ModelCache::getAll();
        if($refresh){//强制重新加载缓存
            $list = [];
        }
        if(empty($list)){
            $list = ContentModuleModel::query()->keyBy('id')->get(['id','type','table','title','list_template','content_template', 'table']);
            ModelCache::setAll($list);
        }
        return $list;
    }

    /**
     * 获取单页模型ID
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/24 下午5:55
     */
    public static function getSinglePageId(){
        return ContentModuleModel::query()->where('type', ModelEnums::SINGLE_PAGE)->value('id');
    }


    /**
     * 返回ID为key的数据
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:47
     */
    public static function getKvData(array $ids=[])
    {
        $query = ContentModuleModel::query();
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
        return ContentModuleModel::query()->orderBy('sort', 'asc')->pluck('title', 'id');
    }

    /**
     * 获取详情
     *
     * @param  int  $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午2:18
     */
    public static function getDetail(int $id){
        return ContentModuleModel::getInstance()->getDetail($id);
    }

    /**
     * 取数据列表
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/18 下午10:03
     */
    public static function getList(){
        return ContentModuleModel::getInstance()->getList();
    }

    public static function refresh( int $id){
        if($id <= 0){
            throw new \Exception('参数错误');
        }
        $data = ContentModuleModel::find($id);
        //如果单页，不进行表名变更和模型复制，单页是固化模型
        if($data['type'] == ModelEnums::SINGLE_PAGE){
            throw new \Exception('单页是系统模型，不能更新');
        }

        //新建模型
        self::initModule($data['table'], $data['title'], $id);
        return $data;
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
            $old = ContentModuleModel::find($id);
            unset($data['id']);
            unset($data['_token']);
            ContentModuleModel::where('id', $id)->update($data);
            //如果单页，不进行表名变更和模型复制，单页是固化模型
            if($data['type'] == ModelEnums::SINGLE_PAGE){
                return true;
            }
            //表名有变更要重建模型
            if(! empty($data['table']) && $old['table'] != $data['table']) {
                //删除模型
                self::deleteModule($data['table']);
            }
            //新建模型
            self::initModule($data['table'], $data['title'], $id);
            return true;
        }else{
            $id = ContentModuleModel::insert($data);
            $url = '/admin/'. $data['table'] . '/index';
            self::initMenu($data['title'], $url);
            //如果单页，不进行表名变更和模型复制，单页是固化模型
            if($data['type'] == ModelEnums::SINGLE_PAGE){
                return $id;
            }
            if(! empty($data['table'])) {
                self::initModule($data['table'], $data['title'], $id, true);
            }
            return $id;
        }
    }

    public static function deleteMenu(string $title){
        return Menu::query()->where('title', $title. '管理')->delete();
    }

    /**
     * 初始化模块的菜单
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 下午8:49
     */
    public static function initMenu(string $title, string $uri){
        $menuLogic = new MenuLogic();
        //获取内容管理这一项的ID
        $parent_id = $menuLogic->getContentManageId();
        $args = [
            'parent_id' => $parent_id,
            'title' => $title. '管理',
            'icon' => '&#xe652;',
            'uri' => $uri
        ];
        return $menuLogic->save(0, $args);
    }

    /**
     * 初始化模型
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 下午1:51
     */
    public static function initModule(string $table, string $title, string $moduleId, $initTable=false){
        //模块名称，是大驼峰
        $moduleName = convertUnderline($table, true);
        //复制表
        if($initTable) {
            ModuleCopyLogic::copyTable($table);
        }
        //复制视图
        ModuleCopyLogic::copyView($moduleName, $title, $moduleId);
        //复制控制器
        ModuleCopyLogic::copyController($moduleName, $moduleId);
        //复制逻辑层
        ModuleCopyLogic::copyLogic($moduleName);
        //复制模型
        ModuleCopyLogic::copyModel($moduleName);
    }

    /**
     * 删除模型， 修改表的时候会删除模型 表、视图、控制器、逻辑层、模型层
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 下午1:51
     */
    public static function deleteModule(string $table){
        //模块名称，是大驼峰
        $moduleName = convertUnderline($table, true);
        //删除表
        ModuleCopyLogic::deleteTable($table);
        //删除视图
        ModuleCopyLogic::deleteView($moduleName);
        //删除控制器
        ModuleCopyLogic::deleteController($moduleName);
        //删除逻辑层
        ModuleCopyLogic::deleteLogic($moduleName);
        //删除模型
        ModuleCopyLogic::deleteModel($moduleName);
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
        if($id <= 0){
            return false;
        }
        //查找模型数据
        $data = ContentModuleModel::find($id);
        //删除模型 （表、视图、控制器、逻辑层、模型层）
        self::deleteModule($data['table']);
        //删除对应的菜单
        self::deleteMenu($data['title']);
        return ContentModuleModel::getInstance()->where('id', $id)->delete();
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
        return ContentModuleModel::where('id', $id)->update(['state' => $state]);
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
        return ContentModuleModel::where('id', $id)->update(['sort' => $sort]);
    }


}