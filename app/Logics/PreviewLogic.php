<?php
namespace App\Logics;
use App\Facades\Db;
use App\Utils\TagCompile\MainParser;

class PreviewLogic{

    /**
     * 增加点击数
     *
     * @param  int  $id
     * @param  int  $model_id
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/15 下午12:22
     */
    public static function addHits(int $id, int $model_id){
        $modelList = ContentModuleLogic::getModuleList();
        $model = $modelList[$model_id];
        $hits = Db::table($model['table'])->where('id', $id)->value('hits');
        Db::table($model['table'])->where('id', $id)->update(['hits' => Db::raw('hits+1')]);
        return $hits++;
    }

    //获取搜索页模板
    public static function getSearchTemplateUrl($modelId){
        $info = ContentModuleLogic::getByIdCache($modelId);
        return $info['search_template'];
    }

    /**
     * 获取模板地址，三种类型 首页 列表页 内容页
     *
     * @param array  $args
     * @param array $config
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/14 上午11:24
     */
    public static function getTemplateUrl(array $args, array $config){
        $class_id = isset($args['class_id']) ? intval($args['class_id']) : 0;
        if($class_id > 0){
            //获取模板信息
            $classInfo = ClassifyLogic::getTemplate($class_id);
            $id = isset($args['id']) ? intval($args['id']) : 0;
            if($id > 0){
                $templateUrl = $classInfo['content_template'];
                PreviewLogic::addHits($id, $classInfo['model_id']);
            }else{
                $templateUrl = $classInfo['list_template'];
            }
        }else{//首页模板
            $templateUrl = $config['index_template'];
        }
        return $templateUrl;
    }

    /**
     * 获取Html内容
     *
     * @param  string  $templateUrl
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/14 上午11:21
     */
    public static function getContentHtml(string $templateUrl, array $config, $args){
        $instance = new MainParser();
        $instance->setSkin($config['skin'] ?? 'default');
        $instance->setTemplatePath($templateUrl);
        //true表示，始终刷新缓存
        $instance->setIsRefreshTemplateCache(config('enable_template_cache') ? false : true);
        //获取模板缓存 V2, 将模板，直接编译为PHP文件，加快速度
        $cachePath = $instance->getTemplateCache();
        return get_include_contents($cachePath, ['args' => $args]);
        //include $cachePath;
    }

    /**
     * 获取Html内容, 用于静态页面生成
     *
     * @param  string  $templateUrl
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/14 上午11:21
     */
    public static function getContentHtmlForMake(string $templateUrl, array $config, array $args){
        log_error('make', [$templateUrl, $args]);
        $instance = new MainParser();
        $instance->setSkin($config['skin'] ?? 'default');
        $instance->setTemplatePath($templateUrl);
        //true表示，始终刷新缓存
        $instance->setIsRefreshTemplateCache(config('enable_template_cache') ? false : true);
        //获取模板缓存 V2, 将模板，直接编译为PHP文件，加快速度
        $cachePath = $instance->getTemplateCache();
        return get_include_contents($cachePath, ['args' => $args]);
    }

}