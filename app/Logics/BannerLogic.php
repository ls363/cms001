<?php
namespace App\Logics;

use App\Models\Base\Banner;

class BannerLogic
{

    /**
     * 获取前台用到的广告
     *
     * @param  int  $type
     * @param  int  $status
     * @author lichunguang 153102250@qq.com
     * @since 2022/6/1 下午11:28
     */
    public static function getList(int $type=0, int $state = -1)
    {
        $query = Banner::query()->with('file');
        if($type > 0){
            $query->where('type', $type);
        }
        if($state > -1){
            $query->where('state', $state);
        }
        $query->orderBy('sort', 'asc');
        $list = $query->get();
        if (empty($list)) {
            return [];
        }
        /*
        foreach ($list as & $v) {
            //$v['picurl'] = empty($v['file']) ? '' : config('app.url') . '/storage/' . $v['file']['folder'] . '/' . $v['file']['url'];
            //unset($v['file']);
        }*/
        return $list;
    }

    /**
     * 返回ID为key的数据
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:47
     */
    public static function getKvData()
    {
        return Banner::query()->pluck('name', 'id');
    }


    /**
     * 获取全部
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:59
     */
    public static function getAll()
    {

        $list = Banner::query()->orderBy('sort', 'asc')->get();
        if (empty($list)){
            return [];
        }

        /*
        $list = Banner::query()->with('file', function($query){
            return $query->select(['id','folder', 'original','name', 'extension']);
        })->orderBy('sort', 'asc')->get();
        if (empty($list)){
            return [];
        }

        foreach ($list as & $v) {
            if(empty($v['file'])){
                $v['picurl'] = '';
            }else{
                $v['picurl'] = config('url') . UPLOAD_URL .'/'. $v['file']['folder'] .'/'. $v['file']['name'] . $v['file']['extension'];
                unset($v['file']);
            }
        }*/
        return $list;
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
        $result = Banner::query()->find($id);
        //$result['picurl'] = empty($result['file']) ? '' : config('app.url') . '/storage/' . $result['file']['folder'] . '/' . $result['file']['url'];
        //unset($result['file']);
        return $result;
    }

    /**
     * 设置百叶窗状态
     *
     * @param  int  $id
     * @param  int  $state
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/6/1 下午11:46
     */
    public static function setState(int $id, int $state)
    {
        return  Banner::where('id', $id)->update(['state' => $state]);
    }

    /**
     * 保存数据
     *
     * @param $id
     * @param $data
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:48
     */
    public static function save($data)
    {
        $id = isset($data['id']) ? $data['id'] : 0;
        try {
            if ($id > 0) {
                return Banner::where('id', $id)->update($data);
            } else {
                unset($data['id']);
                return Banner::insert($data);
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除
     */
    public static function delete($id)
    {
        if($id == 0){
            return false;
        }
        try {
            return Banner::query()->where('id', $id)->delete();
        } catch (\Exception $e) {
            return false;
        }
    }

}
