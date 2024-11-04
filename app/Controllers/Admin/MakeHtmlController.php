<?php

/**
 * HTML页面生成
 *
 * @author      fzs
 * @Time: 2017/07/14 15:57
 * @version     1.0 版本号
 */

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Logics\MakeHtmlLogic;


class MakeHtmlController extends Controller
{
    /**
     * 首页生成静态
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/14 上午9:41
     */
    public function makeIndex()
    {
        MakeHtmlLogic::makeIndex();
        return api_success([]);
    }

    /**
     * 栏目页
     */
    public function makeClassify()
    {
        try {
            $args = input();
            //获取栏目ID
            $class_id = isset($args['class_id']) ? intval($args['class_id']) : 0;
            MakeHtmlLogic::makeList($class_id);
            return api_success();
        }catch (\Exception $e){
            log_error('make', $e->getMessage());
            print_r($e->getFile() .','.$e->getLine().','.$e->getMessage());
        }
    }

    /**
     * 内容页
     */
    public function makeContent()
    {
        $args = input();
        //获取内容ID
        $id = isset($args['id']) ? intval($args['id']) : 0;
        //获取栏目ID
        $class_id = isset($args['class_id']) ? intval($args['class_id']) : 0;
        MakeHtmlLogic::makeContent($id, $class_id);
        return api_success();
    }

    /**
     * 单页
     */
    public function makeSingle()
    {
        $args = input();
        //获取内容ID
        $classId = isset($args['class_id']) ? intval($args['class_id']) : 0;
        //获取栏目ID
        MakeHtmlLogic::makeSingle($classId);
        return api_success();
    }


}
