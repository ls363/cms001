<?php
namespace App\Logics;

use App\Models\Base\ContentModule;
use App\Utils\FileUtils;
use Core\DbQueryBuilder;

/**
 *
 * Class ModuleCopyLogic
 * 模块生成用的类
 *
 * @package App\Logics
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/30 下午1:38
 */
class ModuleCopyLogic{

    //复制模型表
    public static function copyTable($table){
        DbQueryBuilder::getInstance()->copyTable('content', $table);
    }

    //复制视图层
    public static function copyView($moduleName, $moduleTitle, $modelId){//获取模型信息
        //复制信息列表
        self::copyListView($moduleName, $moduleTitle);
        //复制信息编辑页面
        self::copyInfoView($modelId);
    }

    /**
     * 复制控制器
     *
     * @param string $moduleName
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 下午12:04
     */
    public static function copyController(string $moduleName, int $moduleId){
        $source = CONTROLLER_PATH . '/Admin/ContentController.php';
        $destination = CONTROLLER_PATH . "/Admin/{$moduleName}Controller.php";
        //读取文件
        $content = file_get_contents($source);
        //替换变量
        $content = str_replace(' MODEL_ID = 0;',' MODEL_ID = '. $moduleId .';', $content);
        $content = str_replace('ContentController', "{$moduleName}Controller", $content);
        $content = str_replace('ContentLogic', "{$moduleName}Logic", $content);
        //写入文件
        write_file($destination, $content);
    }

    /**
     * 复制逻辑器
     *
     * @param string $moduleName
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 下午12:04
     */
    public static function copyLogic(string $moduleName){
        $source = LOGIC_PATH . '/ContentLogic.php';
        $destination = LOGIC_PATH . "/{$moduleName}Logic.php";
        //读取文件
        $content = file_get_contents($source);
        //替换变量，
        $content = str_replace('Base\Content;', "Base\\{$moduleName};", $content);
        $content = str_replace('Content::', "{$moduleName}::", $content);
        $content = str_replace('ContentLogic', "{$moduleName}Logic", $content);
        //写入文件
        write_file($destination, $content);
    }

    /**
     * 复制模型层
     *
     * @param string $moduleName
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 下午12:04
     */
    public static function copyModel(string $moduleName){
        //模型基类复制
        $source = MODEL_PATH . '/Base/Content.php';
        $destination = MODEL_PATH . "/Base/{$moduleName}.php";
        //读取文件
        $content = file_get_contents($source);
        //替换变量
        $content = str_replace('class Content extends', "class $moduleName extends", $content);
        //写入文件
        file_put_contents($destination, $content);
        //模型扩展复制
        $source = MODEL_PATH . '/ContentModel.php';
        $destination = MODEL_PATH . "/{$moduleName}Model.php";
        //读取文件
        $content = file_get_contents($source);
        //替换变量
        $content = str_replace('Base\Content;', "Base\\{$moduleName};", $content);
        $content = str_replace('class ContentModel extends Content', "class {$moduleName}Model extends {$moduleName}", $content);
        //写入文件
        write_file($destination, $content);
    }

    //删除模型表
    public static function deleteTable($table){
        DbQueryBuilder::getInstance()->deleteTable($table);
    }

    //删除视图层
    public static function deleteView($moduleName)
    {
        FileUtils::deldir(VIEW_PATH .'/admin/'. $moduleName);
    }

    //删除逻辑层
    public static function deleteController($moduleName){
        $destination = CONTROLLER_PATH . "/Admin/{$moduleName}Controller.php";
        delete_file($destination);
    }


    //删除逻辑层
    public static function deleteLogic($moduleName){
        $destination = LOGIC_PATH . "/{$moduleName}Logic.php";
        delete_file($destination);
    }

    //删除逻辑层
    public static function deleteModel($moduleName){
        $destination = MODEL_PATH . "/Base/{$moduleName}.php";
        delete_file($destination);
        $destination = MODEL_PATH . "/{$moduleName}Model.php";
        delete_file($destination);
    }

    //复制列表
    public static function copyListView($moduleName, $moduleTitle){
        //转成小写，模板名字小写
        $moduleName = convertHump($moduleName);
        $source = VIEW_PATH .'/admin/content/index.php';
        $destination = VIEW_PATH .'/admin/'. $moduleName .'/index.php';
        //读取文件
        $content = file_get_contents($source);
        $content = str_replace('{##模型名称##}', "{$moduleTitle}", $content);
        //替换变量
        $content = str_replace('内容列表', "{$moduleTitle}列表", $content);
        $content = str_replace('内容分类', "{$moduleTitle}分类", $content);
        $content = str_replace('添加内容', "添加{$moduleTitle}", $content);
        $content = str_replace('修改内容', "修改{$moduleTitle}", $content);
        //写入文件
        write_file($destination, $content);
    }

    /**
     * 复制前台模板
     * 因为所有的模型，都是基于content模型生成的，所以有通用模板可以复制，只是替换报名即可
     *
     * @author lichunguang
     * @since 2024/2/20 13:53
     * @param $tableNameEn
     * @return void
     */
    public static function copyInfoTemplate($tableNameEn){
        //加载系统配置
        $config = SystemLogic::getSystemCache();
        $skin = $config['skin'] ?? 'default';
        $baseUrl = TEMPLATE_PATH .'/'. $skin .'/html/';
        //复制列表页
        $content = file_get_contents($baseUrl . 'content/list.shtml');
        $content = str_replace('{##table##}', $tableNameEn, $content);
        $destination = $baseUrl . $tableNameEn .'/list.shtml';
        write_file($destination, $content);
        //复制内容页
        $content = file_get_contents($baseUrl . 'content/content.shtml');
        $content = str_replace('{##table##}', $tableNameEn, $content);
        $destination = $baseUrl . $tableNameEn .'/content.shtml';
        write_file($destination, $content);
        //复制标签页
        $content = file_get_contents($baseUrl . 'content/tag.shtml');
        $content = str_replace('{##table##}', $tableNameEn, $content);
        $destination = $baseUrl . $tableNameEn .'/tag.shtml';
        write_file($destination, $content);

    }

    /**
     * 处理信息编辑页面
     *
     * @param int $modelId       模型ID
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/4 下午9:58
     */
    public static function copyInfoView(int $modelId){
        log_error('module_make', $modelId);
        //获取模型信息
        $info = ContentModule::find($modelId);
        $moduleTitle = $info['title'];
        $moduleNameSmall = $info['table'];
        //echo $moduleName;exit;
        $source = VIEW_PATH .'/admin/content/info.php';
        $destination = VIEW_PATH .'/admin/'. $moduleNameSmall .'/info.php';
        //读取文件
        $content = file_get_contents($source);
        //替换变量
        $content = str_replace('{##模型名称##}', "{$moduleTitle}", $content);
        $content = str_replace('内容编辑', "{$moduleTitle}编辑", $content);
        //处理扩展字段，info.php中增加扩展项
        //['extendTab' => '','extendContent' => '']
        $extendArray = self::parseInfoExtend($modelId);
        log_error('module_make', $extendArray);
        if(empty($extendArray['extendTab'])){
            $content = str_replace('{##扩展TAB##}', '', $content);
            $content = str_replace('{##扩展CONTENT##}', '', $content);
        }else {
            $content = str_replace('{##扩展TAB##}', '<li lay-id="4">' . $extendArray['extendTab'] . '</li>', $content);
            $content = str_replace('{##扩展CONTENT##}', $extendArray['extendContent'], $content);
        }
        //写入文件
        write_file($destination, $content);
    }

    /**
     * 解析模型扩展字段，加到info.php
     *
     * @param $modelId
     * @return false|string
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/31 下午2:26
     */
    public static function parseInfoExtend($modelId){
        $extendFieldList = ContentModuleExtendLogic::getList($modelId);
        log_error('module_make',$extendFieldList);
        if(empty($extendFieldList)){
            return [
                'extendTab' => '',
                'extendContent' => ''
            ];
        }
        $path = VIEW_PATH .'/admin/content/info_extend.php';
        $content  = get_include_contents($path, ['extendFieldList' => $extendFieldList]);
        return [
            'extendTab' => '扩展信息',
            'extendContent' => $content
        ];
    }

}