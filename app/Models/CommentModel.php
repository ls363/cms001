<?php

namespace App\Models;

use App\Enums\PageEnums;
use App\Models\Base\Comment;

class CommentModel extends Comment
{

    /**
     * 根据用户名查询
     *
     * @param  string  $username
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午3:53
     */
    public static function getByUserName(string $username)
    {
        return self::where('username', $username)->first();
    }

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
        return self::where('id', $id)->first();
    }


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
        return self::where('id', $id)->first();
    }

    /**
     * 获取分类列表
     *
     * @param  int  $page
     * @param  int  $pageSize
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午3:53
     */
    public static function getPageList(int $page = PageEnums::DEFAULT_PAGE, int $pageSize = PageEnums::PAGE_SIZE)
    {
        $query = self::query();
        $query->orderBy('id', 'desc');
        $result = $query->paginate($pageSize, ['*'], $page);
        $list = $result['list'];
        if(! empty($list)) {
            foreach ($list as & $v) {
                $v['sex_text'] = ($v['sex'] ==1) ? '男' : '女';
            }
        }
        return [
            'list' => $list,
            'page' => $result['page'],
            'total' => $result['total'],
            'page_size' => $result['pageSize']
        ];
    }
}