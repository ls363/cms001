<?php
namespace App\Utils\TagCompile;

use App\Enums\HtmlEnums;
use App\Enums\ErrorEnums;

/**
 * Class TemplateLoader
 * 模板加载类
 *
 * @package App\Libraries\html_maker
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/22 下午4:57
 */
class TemplateLoader
{

    //用于静态文件，相对路径转换，暂时不启用
    private static $allowConvertExt = ['.jpg','.jpeg','.gif','.png','.bmp','.swf','.css','.js'];

    /**
     * 暂未启用
     *
     * @return string|string[]
     * @throws \Exception
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/16 上午9:09
     */
    public function doParser()
    {
        $templateContent = $this->getTemplate('index.shtml');
        //$this->getIncludeFile($templateContent);
        //return 'doParse';
        //测试静态标签的解析
        //$staticFields = $this->parseStaticTag($templateContent);
        //print_r($staticFields);
        //测试自由标签的解析
        //$this->parseAutoTag($templateContent);
        //$this->processCustomTags($templateContent, 'loop');
        return $templateContent;
    }

    /**
     * 读取模板内容
     *
     * @param  string  $skin 皮肤
     * @param  string  $url  模板路径
     * @return string|string[]
     * @throws \Exception
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/7 下午9:32
     */
    public function getTemplate(string $skin, string $url)
    {
//        $folderString = '/templates/a/b/c';
//        $folder = explode('/', $folderString);
//        $img = '../../111a.jpg';
//
//        $img_new = $this->convertStatic($folderString, $folder, $img);
//        echo $img_new;exit;

        $content = $this->getHtmlContent($skin, $url);
        $content = $this->getIncludeFile($content, $skin, $url);
        //关闭相对路径转换，使用固定的变量
        //$content = $this->definiteUrl($content, '/templates/'. $skin.'/'. $url);
        return $content;
    }

    /**
     * 解析包含文件的内容
     *
     * @param  string  $html
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/22 下午2:17
     */
    public function getIncludeFile(string $templateContent, string $skin, string $url)
    {
        $folder = '';
        $iPos = strrpos($url, '/');
        if($iPos > 0) {
            $folder = substr($url, 0, $iPos);
        }
        if (strpos($templateContent, '#include file') === false && strpos($templateContent, '#include virtual') === false) {
            return $templateContent;
        }
        //分析包含文件
        $count = preg_match_all("/\<\!--#include (file|virtual)\=\"(.*?)\"--\>/i", $templateContent, $array);
        //print_r($array);exit;
        for ($i = 0; $i < $count; $i++) {
            $includeTag = $array[0][$i];    //包含文件标记体
            $includeFileName = $array[2][$i];                               //包含文件名
            if(strpos($includeFileName, '/templates') !== false){
                $includeFileName = str_replace('/templates/', '', $includeFileName);
            }else{
                $includeFileName = $folder .'/'. $includeFileName;
            }
            $includeContent = $this->getHtmlContent($skin, $includeFileName);        //取得包含文件内容
            $templateContent = str_replace($includeTag, $includeContent, $templateContent);    //替换包含文件
        }
        return $templateContent;
    }

    /**
     * 读取模板内容
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/22 下午2:02
     */
    public function getHtmlContent(string $skin, string $url)
    {
        $url = TEMPLATE_PATH .'/'. $skin .'/html/'. $url;
        //echo $url;exit;
        if (!file_exists($url)) {
            throw new \Exception(ErrorEnums::TEMPLATE_NOT_FOUND . '-' . $url, 500);
        }
        return file_get_contents($url);
    }

    /**
     * 功能:将相对地址转换为绝对地址
     *
     * @param  string  $strContent
     * @param  string  $templatePath
     * @return string|string[]
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/23 上午10:17
     */
    function definiteUrl(string $strContent, string $templatePath)
    {
        //echo $templatePath;exit;
        $html = $strContent;
        $template_dir = substr($templatePath, 0, strrpos($templatePath, "/"));// . "/";
        $folder_array = explode('/', $template_dir);
        //echo $template_dir .PHP_EOL;
        $count = preg_match_all("/(href|src|value|background){1}\=\"(.*?)\"/i", $html, $array);
        for ($i = 0; $i < $count; $i++) {
            $full_tag = $array[0][$i];    //匹配到的整个字符串
            $content = $array[2][$i];            //获得子模式数据
            if (substr($content, 0, 7) != "http://" && substr($content, 0, 1) != "/") {
                //匹配到的有可能有某些预字符串，所以要判断后再进行替换。
                if ($this->allowExt($content)) {
                    //转换路径
                    $content_deal = $this->convertStatic($template_dir, $folder_array, $content);
                    $full_tag_deal = str_replace($content, $content_deal, $full_tag);    //替换单个字符串
                    $html = str_replace($full_tag, $full_tag_deal, $html);    //全文匹配
                }
            }
        }
        return $html;
    }


    /**
     * 转换路径
     *
     * @param  string  $folder_url
     * @param  array  $folder 数组
     * @param  string  $uri
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/18 上午11:03
     */
    function convertStatic(string $folder_url, array $folder, string $uri){
        if(strpos($uri, '../') === false){
            return $folder_url .'/'. $uri;
        }
        $list = explode('../', $uri);
        $level = count($list)-1;
        for($i=1; $i<= $level; $i++){
            array_pop($folder);
        }
        return  implode('/', $folder) .'/'. end($list);
    }

    /**
     * 根据文件扩展名判断是否需要转化路径
     *
     * @param  string  $value
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/23 上午10:16
     */
    function allowExt(string $value)
    {
        $location = strrpos($value, ".");
        if ($location > 0) {
            $file_ext = substr($value, $location);
            if (in_array($file_ext, self::$allowConvertExt)) {
                return true;
            }
        } else {
            return false;
        }
    }


}