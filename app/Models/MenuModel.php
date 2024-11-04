<?php

namespace App\Models;

use App\Enums\PageEnums;
use App\Models\Base\Menu;

class MenuModel extends Menu
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
     * @param  int  $page
     * @param  int  $pageSize
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午3:53
     */
    public function getPageList(int $page = PageEnums::DEFAULT_PAGE, int $pageSize = PageEnums::PAGE_SIZE)
    {
        return $this->get();
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

    /**
     * 获取列表
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/14 下午7:46
     */
    public function getParentList(){
        return $this->where('parent_id', 0)->orderBy('sort', 'asc')->get(['id', 'title']);
    }
}