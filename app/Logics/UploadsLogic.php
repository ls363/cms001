<?php
namespace App\Logics;

use App\Models\UploadsModel;

class UploadsLogic{

    /**
     * 获取全部
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:59
     */
    public static function getAll()
    {
        return UploadsModel::query()->orderBy('id', 'desc')->get();
    }

    /**
     * 根据ID获取图片地址
     *
     * @param  int  $id
     * @return string[]
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/27 下午2:06
     */
    public static function getUrlById(int $id){
        $data = [
            'url' => '',
            'url_pre' => ''
        ];
        if($id == 0){
            return $data;
        }
        $result = UploadsModel::find($id);
        if(empty($result)){
            return $data;
        }
        $prefix = config('url') . UPLOAD_URL .'/'. $result['folder'] .'/'. $result['name'];
        $data['url'] =  $prefix . $result['extension'];
        $data['url_pre'] =  $prefix .'_pre'.  $result['extension'];
        return $data;
    }

    /**
     * 批量获取图片信息
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:47
     */
    public static function getUrlList($ids)
    {
        if(empty($ids)){
            return [];
        }
        if(is_array($ids) === false){
            $ids = explode(',', $ids);
        }
        $model = UploadsModel::query();
        if(! empty($ids)){
            $model->whereIn('id', $ids);
        }

        $result = $model->get();
        if(empty($result)){
            return  [];
        }

        //支持将public设置运行目录的主机， 本地测试用public
        $prefix = config('url') . UPLOAD_URL .'/';
        foreach ($result as & $v){
            $v['url_pre'] = $prefix . $v['folder'] .'/'. $v['name']  .'_pre'. $v['extension'];
            $v['url'] = $prefix . $v['folder'] .'/'. $v['name'] . $v['extension'];
            if($v['size'] > 0){
                if($v['size'] > 1024 * 1024) {
                    $v['size'] = round($v['size'] / 1024 * 1024, 2) . ' MB';
                } elseif($v['size'] > 1024) {
                    $v['size'] = round($v['size'] / 1000, 2) . ' KB';
                }
            }
        }
        return $result;
    }

    /**
     * 返回ID为key的数据
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:47
     */
    public static function getKvData(array $ids = [])
    {
        $model = UploadsModel::query();
        if(! empty($ids)){
            $model->whereIn('id', $ids);
        }
        return $model->pluck('name','id');
    }

    /**
     * 返回ID为key的数据, 多个字段
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 上午11:47
     */
    public static function getKvList(array $ids = [], array $fields = ['name','size','folder','extension'])
    {
        $model = UploadsModel::query();
        if(! empty($ids)){
            $model->whereIn('id', $ids);
        }
        if($fields != '*') {
            $fields[] = 'id';
        }
        $result = $model->get($fields);
        if(empty($result)){
            return  [];
        }

        //支持将public设置运行目录的主机， 本地测试用public
        $uploadPath = config('url') . UPLOAD_URL .'/' ;

        $list = [];
        foreach ($result as $v){
            $v['url_pre'] = $uploadPath. $v['folder'] .'/'. $v['name']  .'_pre'. $v['extension'];
            $v['url'] = $uploadPath. $v['folder'] .'/'. $v['name'] . $v['extension'];
            if($v['size'] > 0){
                if($v['size'] > 1024 * 1024) {
                    $v['size'] = round($v['size'] / 1024 * 1024, 2) . ' MB';
                } elseif($v['size'] > 1024) {
                    $v['size'] = round($v['size'] / 1000, 2) . ' KB';
                }
            }
            $list[$v['id']] = $v;
        }
        return $list;
    }

    public static function getPageList(){
        return UploadsModel::getInstance()->getPageList();
    }

    /**
     * 保存数据
     * @param array $data
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午4:55
     */
    public static function save(array $data){
        return UploadsModel::insert($data);
    }

    /**
     * 单条记录删除, 暂时禁用
     *
     * @param  int  $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午5:00
     */
    public static function delete(int $id){
        return false;
        log_error('delete', $id);
        return UploadsModel::getInstance()->where('id', $id)->delete();
    }

    /**
     * 批量更新备注
     *
     * @param  string  $ids
     * @param  array  $remarks
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/27 下午5:40
     */
    public static function batchsaveRemark(string $ids, array $remarks){
        if(empty($ids) || empty($remarks)){
            return false;
        }
        $ids_list = explode(',', $ids);
        //ID与备注，数量不一致，报错
        if(count($ids_list) != count($remarks)){
            return false;
        }
        foreach($ids_list as $k=>$v){
            UploadsModel::query()->where('id', $v)->update(['remark' => $remarks[$k]]);
        }
        return true;
    }

    /**
     * 批量转换cover封面图
     *
     * @param  array  $list
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/27 下午10:04
     */
    public static function BatchConvertCover(array & $list){
        if(empty($list)){
            return [];
        }
        $coverIds = [];
        foreach ($list as $v1){
            if(! empty($v1['cover'])){
                $coverIds[] = $v1['cover'];
            }
        }
        //未找到文件的情况
        if(empty($coverIds)){
            foreach ($list as & $v){
                $v['cover_pic'] = '';
                $v['cover_pic_pig'] = '';
            }
            return ;
        }
        $coverList = self::getKvList($coverIds);
        foreach ($list as & $v){
            if(empty($v['cover'])){
                $v['cover_pic'] = '';
                $v['cover_pic_big'] = '';
            }else {
                $v['cover_pic'] = $coverList[$v['cover']]['url_pre'] ?? '';
                $v['cover_pic_big'] = $coverList[$v['cover']]['url'] ?? '';
            }
        }
    }

    /**
     * 转换cover封面图 单个
     *
     * @param  array  $list
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/27 下午10:04
     */
    public static function convertCover(array & $result){
        if(isset($result['cover'])) {
            $data = self::getUrlById($result['cover']);
            $result['cover_pic'] = $data['url_pre'];
            $result['cover_pic_big'] = $data['url'];
        }else{
            $result['cover_pic'] = '';
            $result['cover_pic_big'] = '';
        }
    }

    public static function convertPicAndRemark($strPicList, $strRemarkList){
        if(empty($strPicList)){
            return [];
        }
        $picList = explode('|', $strPicList);
        $remarkList = explode('|', $strRemarkList);
        $list = [];
        foreach ($picList as $k => $v){
            $list[] = [
                'url' => $v,
                'remark' => empty($remarkList) ? '' : ($remarkList[$k] ?? '')
            ];
        }
        $picList = null;
        $remarkList = null;
        return $list;
    }

    public static function BatchConvertPicAndRemark(array & $list){
        foreach ($list as $k => $v){
            $list[$k]['slide_list'] = self::convertPicAndRemark($v['slide'], $v['slide_remark']);
            unset($list[$k]['slide'], $list[$k]['slide_remark']);
        }
        return $list;
    }

}