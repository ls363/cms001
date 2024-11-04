<?php
namespace App\Utils\TagCompile;
use App\Enums\HtmlEnums;

/**
 * 加载模板缓存
 * 1、判断缓存是否存在
 * 2、不存在读取模板文件，调用标签分析类，处理解析标签
 * 3、写入缓存
 * 4、返回缓存
 *
 * Class LoadTemplateCache
 * @package App\Libraries\HtmlMaker
 * @author lichunguang 153102250@qq.com
 * @since 2022/5/2 下午7:45
 */
class MainParser{

    private $templatePath = ''; //模板路径
    private $skin = ''; //皮肤路径
    private $isRefreshTemplateCache = false; //是否刷新模板缓存

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getIsRefreshTemplateCache()
    {
        return $this->isRefreshTemplateCache;
    }

    /**
     * @param  mixed  $isRefreshTemplateCache
     */
    public function setIsRefreshTemplateCache($isRefreshTemplateCache)
    {
        $this->isRefreshTemplateCache = $isRefreshTemplateCache;
    }

    /**
     * @return mixed
     */
    public function getSkin()
    {
        return $this->skin;
    }

    /**
     * @param  mixed  $skin
     */
    public function setSkin($skin)
    {
        $this->skin = $skin;
    }

    /**
     * @return mixed
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * @param  mixed  $templatePath
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
    }


    /**
     * 读取模板缓存，如果存在则直接返回
     *
     * @return string|string[]
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/1 下午9:38
     */
    public function getTemplateCache()
    {
        $path = $this->getTemplatePath();
        $path = str_replace('.shtml', '.php', $path);
        //先判断缓存文件是否存在，存在直接返回
        $cache_url = ROOT_PATH . HtmlEnums::TEMPLATE_CACHE_PATH . $path;
        //var_dump($this->isRefreshTemplateCache);exit;
        //如果文件存在
        if (file_exists($cache_url) && $this->isRefreshTemplateCache == false) {
            return $cache_url;
        }
        //加载模板缓存
        $this->LoadTemplateCache();
        return $cache_url;
    }

    /**
     * 加载模板缓存
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/2 下午7:53
     */
    public function LoadTemplateCache(){
        $path = $this->getTemplatePath();
        $tmpPath = explode('/', $path);
        $tmpFunction = str_replace('.shtml', '_function.php', end($tmpPath));
        //读取模板
        $templateLoader = new TemplateLoader();
        $templateContent = $templateLoader->getTemplate($this->getSkin(), $path);
        //echo $templateContent;exit;
        //去除模板中的换行符，防止正则会失效
        $templateContent = str_replace(array("\r\n", "\n", "\r"), HtmlEnums::HTML_BR_CODE, $templateContent);
        //判断模板类型   :id 内容页专属
        if(strpos($templateContent, ":id") > 0){
            context_set('template_type', 'content');
        }else{
            context_set('template_type', 'list');
        }

        //替换全局变量
        $pattern = '/\{global.(.*?)\}/i';
        $templateContent = preg_replace($pattern, "<?php echo \\\$global['$1'] ?? ''; ?>", $templateContent);

        //替换请求参数
        $pattern = '/\{request.(.*?)\}/i';
        $templateContent = preg_replace($pattern, "<?php echo \\\$args['$1'] ?? ''; ?>", $templateContent);

        $customTagParse = new LoopTagParser();
        //解析静态标签，还原为PHP语句
        $customTagParse->parseStaticTag($templateContent);

        //循环标签的解析
        $loopTagList = $customTagParse->processCustomTags($templateContent, 'loop');
        //print_r($loopTagList);exit;
        /**
         * $v的结构
         * function_data
         * function_html        html渲染， 包含了nextloop的解析
         * function_data_call
         * function_html_call
         * loopHtml         原始的html标签
         * nextHtmlFunction next的html渲染
         * nextDataFunction 数据函数
         */

        //loop转换出来的函数列表
        $functionCallList = [];
        $functionList = [];

        foreach ($loopTagList as $v) {
            $functionCallList[] = $v['function_data_call'];
            $functionList[] = $v['function_data'];
            $functionList[] = $v['function_html'];
            if(! empty($v['nextHtmlFunction'])){
                $functionList[] = $v['nextHtmlFunction'];
            }
            if(! empty($v['nextDataFunction'])) {
                $functionList = array_merge($functionList, $v['nextDataFunction']);
            }
            $phpCode =  PHP_EOL. $v['function_html_call'];
            //$phpCode = $v['nextHtmlFunction'] . PHP_EOL. $v['function_html_call'];
//            $phpCode = $v['nextHtmlFunction'] . PHP_EOL  . $v['function_html'] . PHP_EOL. $v['function_html_call'];
            $templateContent = str_replace($v['loopHtml'], $phpCode, $templateContent);
        }

        //处理分页条
        if(strpos($templateContent, '</pagebar>')){
            $p = new PageBarTagParser();
            $pageTagList = $p->processCustomTags($templateContent);
            if(! empty($pageTagList)){
                foreach ($pageTagList as $tag){
                    //调用部分
                    $templateContent = str_replace($tag['loopHtml'], $tag['function_html_call'], $templateContent);
                    //函数部分
                    $functionList[] = $tag['function_html'];
                }
            }
        }

        //解析位置标签
        if(strpos($templateContent, '</position>')) {
            $positionParser = new PositionTagParser();
            $positionTagList = $positionParser->processCustomTags($templateContent);
            if(! empty($positionTagList)){
                foreach ($positionTagList as $tag){
                    //调用部分
                    $templateContent = str_replace($tag['loopHtml'], $tag['function_html_call'], $templateContent);
                    //函数部分
                    $functionList[] = $tag['function_html'];
                }
            }
        }

        //处理完毕，组装成完整的页面
        $templateContent = $customTagParse->getStaticData() . PHP_EOL
            . '<?php require_once("'. $tmpFunction .'"); ?>'. PHP_EOL
            . implode(PHP_EOL, $functionCallList) . PHP_EOL
            . '<?php if(! isset($args[\'id\']) && !isset($args[\'class_id\'])){'.PHP_EOL
            . ' context_set(\'global\', $site_config);'.PHP_EOL
            . '} ?>'.PHP_EOL
            . '<?php $global = context(\'global\'); ?>'.PHP_EOL
            . $templateContent .PHP_EOL;

        //解析Loop标签之外的if语句
        $this->parseIf($templateContent);

        $templateContent = str_replace(HtmlEnums::HTML_BR_CODE, PHP_EOL, $templateContent);
        //print_r($templateContent);exit;
        //写入到缓存文件
        $this->writeToCache($templateContent);
        //写入文件
        $functionContent = '<?php ' . PHP_EOL;
        $functionContent .= $customTagParse->getNamespacePHP();
        $functionContent .= ' ?> ' . PHP_EOL;
        $functionContent .= implode(PHP_EOL, $functionList);
        $functionContent = str_replace(HtmlEnums::HTML_BR_CODE, PHP_EOL, $functionContent);
        $this->writeToCache($functionContent, 'function');
        return $templateContent;
    }

    /**
     * 解析if语句
     *
     * @param  string  $str
     * @return string|string[]
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午6:12
     */
    private function parseIf(string & $str){
        if(strpos($str, '{/if}') === false){
            return;
        }
        //解析if开始标签
        $pattern = "/\{(else)?if\s?(.*?)\}/";
        $str = preg_replace($pattern, "<?php $1if ($2){ ?>", $str);
        //解析else标签
        $str = str_replace('{else}', '<?php }else{ ?>', $str);
        //解析if结束标签
        $str = str_replace(['{/if}', '{/endif}', '{endif}'], '<?php } ?>', $str);
    }

    /**
     * 将模板解析结果写入缓存
     *
     * @param $data
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/2 下午7:50
     */
    public function writeToCache(& $data, $type='page'){
        $path = $this->getTemplatePath();
        //函数前辍，防止列表与内容页一起生成的时候，名称重复,PHP变量，必须以字母开头, 暂时用PHP的p
        $functionPrefix = 'p'. md5($this->getSkin() .'/'. $path);
        $data = str_replace('get_nextloop_',"get_{$functionPrefix}_nextloop_", $data);
        $data = str_replace('$nextloop_',"\${$functionPrefix}_nextloop_", $data);
        $data = str_replace('$loop_',"\${$functionPrefix}_loop_", $data);
        $data = str_replace('get_loop_',"get_{$functionPrefix}_loop_", $data);
        $data = str_replace('get_position_',"get_{$functionPrefix}_position_", $data);
        $data = str_replace('get_pagebar_',"get_{$functionPrefix}_pagebar_", $data);
        if($type == 'page'){
            $path = str_replace('.shtml', '.php', $path);
        }else{
            $path = str_replace('.shtml', '_function.php', $path);
        }
        //先判断缓存文件是否存在，存在直接返回
        $cache_url = ROOT_PATH . HtmlEnums::TEMPLATE_CACHE_PATH . $path;
        $cache_folder = substr($cache_url, 0, strrpos($cache_url,"/"));
        if(! is_dir($cache_folder)) {
            mkdir($cache_folder, 0777, true);
        }
        file_put_contents($cache_url, $data);
    }

}