<?php

namespace App\Logics;

use App\Enums\ErrorEnums;
use App\Enums\PageEnums;
use App\Models\Base\Comment;
use Core\WebSession;

class CommentLogic
{
    /**
     * 获取所有评论数
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:47
     */
    public static function getAllNum()
    {
        return Comment::count();
    }


    /**
     * 单条数据查询
     *
     * @param $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:47
     */
    public static function getById($id)
    {
        return Comment::find($id);
    }

    /**
     * 获取全部
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:59
     */
    public static function getPageList($input = [])
    {
        $query = Comment::query();
        if (array_key_exists('state', $input) && $input['state'] > -1) {
            $query->where('state', $input['state']);
        }
        $search_type = isset($input['search_type']) ? $input['search_type'] : '';
        $search_text = isset($input['search_text']) ? $input['search_text'] : '';
        //搜索字段处理
        if ($search_text != '' && $search_type != '' && in_array($search_type,
                ['linkman', 'mobile', 'content', 'reply'])) {
            $query->where($search_type, 'like', "%{$search_text}%");
        }
        $query->orderBy('id', 'desc');
        return $query->paginate(PageEnums::PAGE_SIZE, ['*'], isset($input['page']) ? $input['page'] : 1);
    }


    /**
     * 保存数据
     *
     * @param $id
     * @param $data
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:48
     */
    public static function save($id, $data)
    {
        try {
            if ($id > 0) {
                unset($data['id']);
                return Comment::where('id', $id)->update($data);
            } else {
                return Comment::insert($data);
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除
     */
    public static function delete(int $id)
    {
        if ($id <= 0) {
            return false;
        }
        try {
            $model = Comment::query()->find($id);
            if (empty($model)) {
                throw new \Exception('数据已删除');
            }
            return Comment::query()->where('id', $id)->delete();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 批量删除
     *
     * @param  array  $ids
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/9 下午4:20
     */
    public static function batchDelete(array $ids)
    {
        try {
            return Comment::query()->whereIn('id', $ids)->delete();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除
     */
    public static function setState(int $id, int $state)
    {
        if ($id <= 0) {
            return false;
        }
        try {
            $model = Comment::query()->find($id);
            if (empty($model)) {
                throw new \Exception('数据已删除');
            }
            return $model->delete();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 保存数据
     *
     * @param $id
     * @param $data
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:48
     */
    public static function saveFront($data)
    {
        $verifyCode = $data['verifyCode'] ?? '';
        $originCode = WebSession::getInstance()->get(config('session_auth_num_key'));
        log_error('args',$verifyCode .','.$originCode);
        if(strtolower($verifyCode) != strtolower($originCode)){
            throw new \Exception(ErrorEnums::LOGIN_VERIFY_ERROR, ErrorEnums::DEFAULT_ERROR);
        }
        return Comment::insert($data);
    }

}
