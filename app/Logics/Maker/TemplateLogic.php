<?php
namespace App\Logics\Maker;

use App\Enums\HtmlEnums;
use App\Logics\ClassifyLogic;
use App\Logics\SystemLogic;

class TemplateLogic{

    protected static $templateDir = '';

    /**
     * 递归获取目录下的文件，返回的是相对路径
     *
     * @param $folder
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/15 下午4:28
     */
    public static function scandirs($folder)
    {
        $list = [];
        $result = scandir($folder);
        //$basePath =
        foreach ($result as $v){
            //跳过不需要展示的
            if(in_array($v, HtmlEnums::$ignoreFolder)){
                continue;
            }

            $path = str_replace(self::$templateDir, '', $folder .'/'. $v);
            if(is_dir($folder.'/'. $v)){
                $list[] = ['title' => $v, 'path' => $path,'children' => self::scandirs($folder.'/'. $v)];
            }else {
                $list[] = ['title' => $v, 'path' => $path, 'children' => []];
            }
        }
        return $list;
    }

    /**
     * 获取模板目录下的文件
     *
     * @param  string  $folder
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/9 下午10:24
     */
    public static function getDir($folder = ''){
        $systemConfig = SystemLogic::getSystemCache();
        $templatePath = TEMPLATE_PATH . '/'. $systemConfig['skin'] . '/html';
        self::$templateDir = $templatePath .'/';
        if(is_dir($templatePath) === false){
            return [];
        }
        return self::scandirs($templatePath);
    }

    /**
     * 模板列表，用于配置栏目模板
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/26 下午11:21
     */
    public static function getTemplateList(){
        $systemConfig = SystemLogic::getSystemCache();
        $templatePath = PUBLIC_PATH . HtmlEnums::TEMPLATE_PATH . '/'. $systemConfig['skin'];
        $list = self::scanDir($templatePath);
        foreach ($list as $k =>& $v){
            $iPos = strrpos($v, '/');
            if($iPos === false){
                $tmpFolder = $v;
            }else{
                $tmpFolder = substr($v, $iPos+1);
            }
            if(in_array($tmpFolder, ['header.php', 'footer.php','right.php'])){
                unset($list[$k]);
                continue;
            }
            $v = str_replace($templatePath. '/', '', $v);
        }
        return array_values($list);
    }

    /**
     * * 循环获取文件
     */
    public static function scanDir($dir) {
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                $filter_list = HtmlEnums::$ignoreFolder;
                if(in_array($file, $filter_list)){ continue;}

                if(is_file($dir.'/'.$file)) {
                    $file_list[] = $dir.'/'.$file;
                    continue;
                }

                $file_list[$file] = self::scanDir($dir.'/'.$file);
                foreach($file_list[$file] as $infile) {
                    $file_list[] = $infile;
                }
                unset($file_list[$file]);
            }
            closedir($handle);

            return $file_list;
        }
    }
}
