<?php
namespace App\Logics;

use App\Enums\ModelEnums;
use App\Facades\Db;
use App\Models\ContentModuleExtendModel;
use core\Singleton;

class ContentModuleExtendLogic{

    use Singleton;


    /**
     * 获取全部
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:59
     */
    public static function getAll()
    {
        return ContentModuleExtendModel::query()->orderBy('sort', 'asc')->pluck('title', 'id');
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
        return ContentModuleExtendModel::getById($id);
    }

    /**
     * 取数据列表
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/18 下午10:03
     */
    public static function getModelFields($model_id){
        $list = ContentModuleExtendModel::where('model_id',$model_id)->pluck('field_input');
        return $list;
    }

    /**
     * 取数据列表
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/18 下午10:03
     */
    public static function getList($model_id){
        $list = ContentModuleExtendModel::getListByModelId($model_id);
        foreach ($list as & $v){
            if(! empty($v['field_option'])){
                //中英文逗号作转换
                $v['field_option'] = str_replace("，",",", $v['field_option']);
                $v['field_option'] = explode(',', $v['field_option']);
            }
        }
        return $list;
    }

    /**
     * 获取扩展字段的列表
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/18 下午10:03
     */
    public static function getExtendFieldOption($model_id, $field){
        $result = ContentModuleExtendModel::where('model_id', $model_id)
            ->where('field_input', $field)->first();
        if(! empty($result)){
            if(! empty($result['field_option'])){
                $arr = explode(',', $result['field_option']);
                return array_map(function ($item){
                    return ['value' => $item];
                }, $arr);
            }
        }
        return [];
    }

    /**
     * 添加或者修改数据, 单条数据的修改
     * @param int $id
     * @param array $data
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午4:55
     */
    public static function save(int $id, array $data){
        $model_id = $data['model_id'];
        //获取内容模型信息
        $contentModule = ContentModuleLogic::getDetail($model_id);
        if($id > 0){
            $old = ContentModuleExtendModel::find($id);
            unset($data['id']);
            unset($data['_token']);
            //添加字段
            //字段类型变更会删除字段再重新添加
            if($old['field_type'] != $data['field_type']) {
                self::deleteItemColumn($contentModule['table'], $data['field_input']);
                self::addItemColumn($contentModule['table'], $data['field_input'], $data['field_type'], $data['field_title']);
            }
            //保存模型的信息编辑页面
            ModuleCopyLogic::copyInfoView($model_id);
            return ContentModuleExtendModel::where('id', $id)->update($data);
        }else{
            $id = ContentModuleExtendModel::insert($data);
            if(! empty($data['field_type'])) {
                self::addItemColumn($contentModule['table'], $data['field_input'], $data['field_type'], $data['field_name']);
            }
            //保存模型的信息编辑页面
            ModuleCopyLogic::copyInfoView($model_id);
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
        if($id <= 0){
            return false;
        }
        $data = ContentModuleExtendModel::getInstance()->find($id);
        log_error('delete', $id);
        $model_id = $data['model_id'];
        //获取内容模型信息
        $contentModule = ContentModuleLogic::getDetail($model_id);
        //删除数据库字段
        self::deleteItemColumn($contentModule['table'], $data['field_input']);
        //删除模型扩展字段记录
        ContentModuleExtendModel::getInstance()->where('id', $id)->delete();
        //保存模型的信息编辑页面
        ModuleCopyLogic::copyInfoView($model_id);
        return true;
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
        return ContentModuleExtendModel::where('id', $id)->update(['state' => $state]);
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
        return ContentModuleExtendModel::where('id', $id)->update(['sort' => $sort]);
    }

    /**
     * 保存表字段
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/31 下午11:37
     */
    public static function addItemColumn($tableName, $fieldName, $type, $comment=''){
        $column = [
            'name' => $fieldName
        ];
        switch ($type){
            case 'text':
                $column['type'] = 'varchar(255)';
                break;
            case 'textarea':
                $column['type'] = 'varchar(255)';
                break;
            case 'select':
                $column['type'] = 'varchar(128)';
                break;
            case 'radio':
                $column['type'] = 'varchar(128)';
                break;
            case 'checkbox':
                $column['type'] = 'varchar(255)';
                break;
        }
        if(! empty($title)) {
            $column['comment'] = $comment;
        }
        Db::addTableColumn($tableName, [$column]);
        //更新表结构缓存
        Db::getTableFieldType($tableName, true);
    }

    /**
     * 删除表字段
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/31 下午11:37
     */
    public static function deleteItemColumn($tableName, $field){
        Db::deleteTableColumn($tableName, [$field]);
        //更新表结构缓存
        Db::getTableFieldType($tableName, true);
    }

}