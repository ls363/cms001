<?php

namespace App\Models;

use App\Models\Base\ContentModuleExtend;

class ContentModuleExtendModel extends ContentModuleExtend
{

    /**
     * 根据ID查询
     *
     * @param  int  $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午3:53
     */
    public static function getById(int $id)
    {
        return self::find($id);
    }

    /**
     * 取列表
     *
     * @param $model_id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 下午4:50
     */
    public static function getListByModelId($model_id)
    {
        return self::where('model_id', $model_id)->orderBy('sort', 'asc')->get();
    }

    /**
     * 获取模型字段
     *
     * @param  int  $model_id
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午3:53
     */
    public static function getFieldsByModelId(int $model_id)
    {
        return self::where('model_id', $model_id)->pluck('field_input');
    }
}