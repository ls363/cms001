<?php

namespace App\Models;

use App\Models\Base\ContentModule;

class ContentModuleModel extends ContentModule
{

    /**
     * 获取详情
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
     * 获取列表
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/14 下午7:46
     */
    public function getList(){
        return $this->orderBy('sort', 'asc')->get();
    }

}