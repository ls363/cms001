<?php

namespace App\Models;

use App\Enums\PageEnums;
use App\Models\Base\Uploads;

class UploadsModel extends Uploads
{
    /**
     * 根据ID查询
     *
     * @param  int  $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午3:53
     */
    public function getById(int $id)
    {
        return $this->where('id', $id)->first();
    }

    /**
     * 获取分类列表
     *
     * @param  int  $page
     * @param  int  $pageSize
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午3:53
     */
    public function getPageList(int $page = PageEnums::DEFAULT_PAGE, int $pageSize = PageEnums::PAGE_SIZE)
    {
        return $this->get();
    }
}