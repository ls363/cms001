<?php
namespace App\Logics;

use App\Facades\Db;
use App\Models\Base\ContentModule;
use App\Utils\MakeHtml;


class MakeHtmlLogic{

    /**
     * 生成首页
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/14 下午2:27
     */
    public static function makeIndex(){
        //加载系统配置
        $system = SystemLogic::getSystemCache();
        //获取首页内容
        $html = PreviewLogic::getContentHtmlForMake($system['index_template'], $system, []);
        //生成首页文件
        MakeHtml::makeFile( 'index.html', $html);
    }

    public static function makeSingle($classId){
        //获取模板信息
        $classInfo = ClassifyLogic::getTemplate($classId);
        $args = [
            'class_id' => $classId,
            'model_id' => $classInfo['model_id'],
            'page' => 1
        ];
        //加载系统配置
        $system = SystemLogic::getSystemCache();
        //栏目页的目录，以目录作为URL
        $folder = $classInfo['url'];
        $args['folder'] = $folder;
        //获取列表页模板
        $templateUrl = $classInfo['list_template'];
        //获取列表页内容
        $html = PreviewLogic::getContentHtmlForMake($templateUrl, $system, $args);
        //生成列表页文件，这里是第一页
        MakeHtml::makeFile($classInfo['url']. '/index.html', $html);
    }

    /**
     * 生成列表页，需要加入配置，跟栏目绑定，配置每页显示数，然后再传入pageId进行生成。
     *
     * @param int $classId 栏目ID
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/14 下午2:38
     */
    public static function makeList(int $classId, bool $makeContent=true){
        //获取模板信息
        $classInfo = ClassifyLogic::getTemplate($classId);
        //1列表+内容 2仅列表 3仅内容
        if($classInfo['make_html'] == 3){
            return;
        }
        if($makeContent){
            //生成列表的内容页
            self::makeClassContent($classInfo['model_id'], $classId);
        }
        $args = [
            'class_id' => $classId,
            'model_id' => $classInfo['model_id'],
            'page' => 1
        ];
        context_set('request_args', $args);
        context_set('model_id', $classInfo['model_id']);
        context_set('class_id', $classId);
        //加载系统配置
        $system = SystemLogic::getSystemCache();
        //栏目页的目录，以目录作为URL
        $folder = $classInfo['url'];
        $args['folder'] = $folder;
        //获取列表页模板
        $templateUrl = $classInfo['list_template'];
        //获取列表页内容
        $html = PreviewLogic::getContentHtmlForMake($templateUrl, $system, $args);
        $html = preg_replace('/\n{2,}/', "\n", $html);
        //生成列表页文件，这里是第一页
        MakeHtml::makeFile($classInfo['url']. '/index.html', $html);
        //获取总页数
        $totalPage = context('pageInfo.totalPages');
        log_error('total_page', $totalPage);
        for ($i=2; $i<=$totalPage; $i++){
            $args['page'] = $i;
            context_set('request_args', $args);
            $html = PreviewLogic::getContentHtmlForMake($templateUrl, $system, $args);
            $html = preg_replace('/\n{2,}/', "\n", $html);
            //生成列表页文件，这里是第一页
            MakeHtml::makeFile($classInfo['url'].'_'. $i. '/index.html', $html);
        }
    }

    /**
     * 内容页可以配置为发布的时候，自动生成
     *
     * @param  int  $id
     * @param  int  $classId
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/15 下午7:04
     */
    public static function makeContent(int $id, int $classId){
        //获取模板信息
        $classInfo = ClassifyLogic::getTemplate($classId);
        //1列表+内容 2仅列表 3仅内容
        if($classInfo['make_html'] == 2){
            return ;
        }
        $args = [
            'id' => $id,
            'class_id' => $classId,
            'model_id' => $classInfo['model_id']
        ];
        context_set('request_args', $args);
        //加载系统配置
        $system = SystemLogic::getSystemCache();
        //获取列表页模板
        $templateUrl = $classInfo['content_template'];
        //获取列表页内容'
        $tmp =  config('route.url_rewrite');
        config_set('route.url_rewrite', true);
        $html = PreviewLogic::getContentHtmlForMake($templateUrl, $system, $args);
        if(! empty($html)){
            $html = preg_replace('/\n{2,}/', "\n", $html);
            //生成内容页，暂时使用 folder/id.html 这种格式
            MakeHtml::makeFile( $classInfo['url']. '/'. $id .'.html', $html);
        }
        config_set('route.url_rewrite', $tmp);
    }

    /**
     * 获取页面信息，生成html用
     *
     * @param $classId
     * @param $page
     * @return false|string
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/15 下午11:09
     */
    public static function page($classId, $page){
        //获取模板信息
        $classInfo = ClassifyLogic::getTemplate($classId);
        $args = [
            'class_id' => $classId,
            'model_id' => $classInfo['model_id'],
            'page' => $page
        ];
        context_set('model_id', $classInfo['model_id']);
        context_set('class_id', $classInfo['class_id']);
        //加载系统配置
        $system = SystemLogic::getSystemCache();
        //获取列表页模板
        $templateUrl = $classInfo['list_template'];
        //获取列表页内容
        return PreviewLogic::getContentHtmlForMake($templateUrl, $system, $args);
    }

    /**
     * 获取页面信息，生成html用
     *
     * @param $classId
     * @param $page
     * @return false|string
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/15 下午11:09
     */
    public static function contentPage($classId, $id){

        //获取模板信息
        $classInfo = ClassifyLogic::getTemplate($classId);
        $args = [
            'class_id' => $classId,
            'model_id' => $classInfo['model_id'],
        ];
        context_set('model_id', $classInfo['model_id']);
        context_set('class_id', $classInfo['class_id']);
        context_set('id', $id);
        //加载系统配置
        $system = SystemLogic::getSystemCache();
        //获取列表页模板
        $templateUrl = $classInfo['content_template'];
        //获取列表页内容
        return PreviewLogic::getContentHtmlForMake($templateUrl, $system, $args);
    }


    /**
     * 生成某个分类下的所有内容
     *
     * @author lichunguang
     * @since 2024/2/1 20:55
     * @return void
     */
    public static function makeClassContent($moduleId, $classId){
        context_set('model_id', $moduleId);
        context_set('class_id', $classId);
        $module = ContentModule::find($moduleId);
        //查询所有的ID
        $ids = Db::table($module['table'])->where('class_id', $classId)->pluck('id');
        if(empty($ids)){
            return;
        }
        foreach ($ids as $v){
            log_error('make','make start: '. $v);
            try {
                self::makeContent($v, $classId);
            }catch (\Exception $e){
                log_error('make', $e->getMessage());
            }
            log_error('make','make end: '. $v);
        }
    }
}
