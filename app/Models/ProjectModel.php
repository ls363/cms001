<?php

namespace App\Models;

use App\Enums\PageEnums;
use App\Models\Base\Project;
use App\Models\Base\Uploads;

class ProjectModel extends Project
{

    public function file(){
        return $this->hasMany(Uploads::class, 'file_id', 'id');
    }


    /**
     * 获取文章详情
     *
     * @param  int  $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午3:53
     */
    public function getDetail(int $id)
    {
        return self::where('id', $id)->first();
    }

    /**
     * 获取文章列表
     *
     * @param  int  $page
     * @param  int  $pageSize
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午3:53
     */
    public function getPageList(int $page = PageEnums::DEFAULT_PAGE, int $pageSize = PageEnums::PAGE_SIZE)
    {
        return $this->orderBy('id', 'desc')->paginate( $pageSize, ['*'],  $page);
    }
}