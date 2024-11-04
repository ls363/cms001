<?php

namespace App\Models;

use App\Enums\PageEnums;
use App\Models\Base\Link;
use App\Models\LinkCategoryModel;

class LinkModel extends Link
{

    /**
     * 关联分类
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/24 下午11:00
     */
    public function category(){
        //链接属于分类 （分类表，链接表中分类的字段，分类表的ID）
        return $this->belongsTo(LinkCategoryModel::class, 'category_id', 'id');
    }

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
     * 获取分页列表
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