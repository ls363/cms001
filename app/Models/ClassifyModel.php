<?php

namespace App\Models;

use App\Enums\PageEnums;
use App\Models\Base\Classify;

class ClassifyModel extends Classify
{

    /**
     * 获取分类详情
     *
     * @param  int  $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午3:53
     */
    public function getDetail(int $id)
    {
        return $this->where('id', $id)->first();
    }

    /**
     * 获取分类列表
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午3:53
     */
    public function getList()
    {
        return $this->orderBy('sort', 'asc')->get();
    }
}