<?php

namespace App\Controllers\Home;

use App\Controllers\Controller;
use App\Logics\MakeHtmlLogic;
use App\Logics\PreviewLogic;
use App\Logics\SystemLogic;

class PreviewController extends Controller
{

    /**
     * 前台页面入口，获取首页、列表页、内容页的Html
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/14 上午11:25
     */
    public function index()
    {
        //处理首页加载
        $uri = request()->uri();
        if($uri == '/'){
            if(file_exists('index.html')){
                include 'index.html';
                return false;
            }
        }
        //加载系统配置
        $config = SystemLogic::getSystemCache();
        //读取内容页
        $args = $this->request->all();
        context_set('request_args', $args);

        //获取模板地址
        $templateUrl = PreviewLogic::getTemplateUrl($args, $config);
        header('Content-Type:text/html; charset=utf-8');
        echo PreviewLogic::getContentHtml($templateUrl, $config, $args);
    }

    /**
     * 前台页面入口，获取首页、列表页、内容页的Html
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/14 上午11:25
     */
    public function tag()
    {
        //加载系统配置
        $config = SystemLogic::getSystemCache();
        //读取内容页
        $args = $this->request->all();
        context_set('request_args', $args);

        //获取模板地址
        $templateUrl = 'article/tag.shtml';
        header('Content-Type:text/html; charset=utf-8');
        echo PreviewLogic::getContentHtml($templateUrl, $config, $args);
    }

    /**
     * 前台页面入口，获取首页、列表页、内容页的Html
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/14 上午11:25
     */
    public function diyform()
    {
        //加载系统配置
        $config = SystemLogic::getSystemCache();
        //读取内容页
        $args = $this->request->all();
        context_set('request_args', $args);

        //获取模板地址
        $templateUrl = 'single/diyform.shtml';
        header('Content-Type:text/html; charset=utf-8');
        echo PreviewLogic::getContentHtml($templateUrl, $config, $args);
    }

    /**
     * 前台页面入口，获取首页、列表页、内容页的Html
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/14 上午11:25
     */
    public function search()
    {
        //加载系统配置
        $config = SystemLogic::getSystemCache();
        //读取内容页
        $args = $this->request->all();
        context_set('request_args', $args);
        $modelId = $args['model_id'] ?? 0;
        $templateUrl = PreviewLogic::getSearchTemplateUrl($modelId);
        header('Content-Type:text/html; charset=utf-8');
        echo PreviewLogic::getContentHtml($templateUrl, $config, $args);
    }


    public function page(){
        $args = input();
        //获取栏目ID
        $class_id = isset($args['class_id']) ? intval($args['class_id']) : 0;
        $page = $args['page'] ?? 1;
        echo MakeHtmlLogic::page($class_id, $page);
    }

    public function content(){
        $args = input();
        //获取栏目ID
        $class_id = isset($args['class_id']) ? intval($args['class_id']) : 0;
        $id = $args['id'] ?? 0;
        echo MakeHtmlLogic::contentPage($class_id, $id);
    }

    function addHits(){
        $args = input();
        $id = $args['id'] ?? 0;
        $modelId = $args['model_id'] ?? 0;
        $hits = PreviewLogic::addHits($id, $modelId);
        $this->success(['hits' => $hits]);
    }

}
