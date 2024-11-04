<?php
namespace Core;

use App\Enums\HtmlEnums;

class View{

    use Singleton;

    /**
     * 直接预览模板
     *
     * @param $path
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/21 下午10:34
     */
    public function display(string $path, array $data = []){
        $path .= '.php';
        $templatePath = APP_PATH . '/Views/'. $path;
        $compilePath = RUNTIMES_PATH ."/views_cache/". $path;
        //缓存不存在或者不开启缓存，始终编译
        if(file_exists($compilePath) === false || config('enable_views_cache') == false){
            $this->makeCache($templatePath);
        }

        if(! empty($data)){
            extract($data);
        }
        include $compilePath;
    }

    /**
     * 调试页面标签
     *
     * @param string $path
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午11:42
     */
    public function debugTag(string $path){
        //读取缓存
        $str = file_get_contents($path);
        //如果有模板继承，直要处理模板继承，先提取section, 再处理解析
        if(strpos($str,'@extends')){
            $content = $this->parseExtends($str);
        }else{
            //替换标签
            $content = $this->parserViewContent($str);
        }
    }

    /**
     * 生成页面缓存
     *
     * @param string $path
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午11:42
     */
    public function makeCache(string $path){
        //读取缓存
        $templateContent = file_get_contents($path);
        //如果有模板继承，直要处理模板继承，先提取section, 再处理解析
        if(strpos($templateContent,'@extends')){
            $templateContent = $this->parseExtends($templateContent);
        }else{
            //替换标签
            $templateContent = $this->parserViewContent($templateContent);
        }
        //echo $path;exit;
        //保存视图缓存
        //$cache_url = str_replace('/views/', '/views_cache/', $path);
        $tmp = explode('/Views/', $path);
//        echo $tmp[1];exit;
        //$compilePath = md5('/'. $tmp[1]).'.php';
        $compilePath = $tmp[1];
        $cache_url = RUNTIMES_PATH . '/views_cache/'. $compilePath;
        $cache_folder = substr($cache_url, 0, strrpos($cache_url,"/"));
        //检查缓存目录
        if(! is_dir($cache_folder)) {
            mkdir($cache_folder, 0777, true);
        }
        //写缓存
        file_put_contents($cache_url, $templateContent);
    }

    /**
     * 解析@include('admin.article.info') 或 @include('admin/article/info')
     * @author lichunguang
     * @since 2024/10/24 13:30
     * @param string $pageContent
     * @return void
     */
    function parseInclude(string & $pageContent){
        if(strpos($pageContent, '@include') === false){
            return;
        }
        $pattern = '/\@include\([\'"]{1}(.*?)[\'"]{1}\)/';
        $num = preg_match_all($pattern, $pageContent,$match);
        for($i=0; $i < $num; $i++){
            $path = $match[1][$i];
            //获取包含文件内容
            $includeContent = $this->getExtendTemplate($path);
            //@include的标签
            $tagHtml = $match[0][$i];
            $pageContent = str_replace($tagHtml, $includeContent, $pageContent);
        }
    }

    //解析模板，这里缓存要单独成函数
    public function parserViewContent(string $str)
    {
        $this->parseInclude($str);
        //解析{if}{/if}
        $this->parseIf($str);
        //解析{for}{/for}
        $this->parseFor($str);
        //解析{foreach}{/foreach}
        $this->parseForeach($str);
        //解析常量{CONST},必须大写
        $this->parseConst($str);
        //解析函数{{ function($params) }}
        $this->parseFunction($str);
        //解析字段，类型最多，统一用preg_match处理
        $this->parseFieldsV2($str);
        return $str;
    }

    /**
     * 提取yield标签
     *
     * @param  string  $str
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/14 上午11:26
     */
    function parseYields(string & $str){
        $pattern = '/\@yield\((.*?)\)/';
        $count = preg_match_all($pattern, $str,$match);
        //print_r($match);exit;
        $list = [];
        for($i=0; $i < $count; $i++){
            $field = $match[0][$i]; //原始标签
            $value = $match[1][$i];
            $value = str_replace(['\'','"'], '', $value);
            $ary = explode(',', $value);
            $list[] = [
                'tagHtml' => $field,
                'sectionName' => $ary[0],
                'default' => isset($ary[1]) ? trim($ary[1]) : ''
            ];
        }
        return $list;
    }

    /**
     * 解析继承的父模板
     *
     * @param  string  $pageContent
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/14 下午2:49
     */
    function parseExtends(string & $pageContent){
        $pattern = '/\@extends\([\'"]{1}(.*?)[\'"]{1}\)/';
        preg_match_all($pattern, $pageContent,$match);
        $path = $match[1][0];
        //获取父模板内容
        $parentContent = $this->getExtendTemplate($path);
        //提取yield标签
        $yieldList = $this->parseYields($parentContent);
        //解析section字段
        $sectionFieldList = $this->parseSectionField($pageContent);
        //提取section块
        $sectionBlockList = $this->parseSectionBlock($pageContent);
        //print_r($sectionBlockList);exit;
        $sectionList = array_merge($sectionFieldList, $sectionBlockList);
        //print_r($sectionList);exit;
        //替换模板标签
        foreach ($yieldList as $v){
            $tagContent = isset($sectionList[$v['sectionName']]) ? $sectionList[$v['sectionName']] : $v['default'];
            $parentContent = str_replace($v['tagHtml'], $tagContent, $parentContent);
        }
        //替换父模板标签
        $parentContent = $this->parserViewContent($parentContent);
        return $parentContent;
    }


    /**
     * 获取扩展的模板内容
     *
     * @param  string  $pathName
     * @return false|string
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/14 下午2:38
     */
    public function getExtendTemplate(string $pathName){
        $path = str_replace('.', '/', $pathName);
        $path = APP_PATH ."/Views/". $path .'.php';
        return  file_get_contents($path);
    }



    /**
     * 解析section块
     *
     * @param  string  $str
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/14 下午1:26
     */
    function parseSectionBlock(string & $str){
        $str = str_replace(["\r\n", "\n", "\r"], 'zxkvbcrlf',  $str);
        $pattern = '/\@section\([\'"]{1}(\w+)[\'"]{1}\)(.*?)\@endsection/';
        $count = preg_match_all($pattern, $str,$match);
        $list = [];
        for($i=0; $i < $count; $i++){
            $field = $match[0][$i];
            $sectionName = $match[1][$i];
            $list[$sectionName] = str_replace('zxkvbcrlf', PHP_EOL, $match[2][$i]);
        }
        return $list;
    }

    /**
     * 提取页面中的所有section, 保存到数组
     *
     * @param  string  $str
     * @author lichunguang 153102250@qq.com
     * @since 2022/7/20 下午12:32
     */
    function parseSectionField(string & $str){
        $pattern = '/\@section\([\'"]{1}(\w+)[\'"]{1},(.*?)\)/';
        $count = preg_match_all($pattern, $str,$match);

        $list = [];
        for($i=0; $i < $count; $i++){
            $sectionName = $match[1][$i];
            $list[$sectionName] = "<?php echo ". $match[2][$i] ."; ?>";
        }
        return $list;
    }

    /**
     * 解析foreach
     *
     * @param  string  $str
     * @return string|string[]
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午6:14
     */
    function parseForeach(string & $str){
        if(strpos($str, '{/foreach}') === false){
            return;
        }
        //解析foreach
        $pattern = "/\{foreach\s+\\$(.*?)\s+as\s+(.*?)\}/";
        $str = preg_replace($pattern, "<?php foreach (\\$$1 as $2){ ?>", $str);
        $str = str_replace(['{/foreach}', '{endforeach}'], '<?php } ?>', $str);
    }

    /**
     * 解析 for
     *
     * @param  string  $str
     * @return string|string[]
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午6:14
     */
    function parseFor(string & $str){
        if(strpos($str, '{/for}') === false){
            return;
        }
        //解析for开始标签
        $pattern = "/\{for\s+\\$([a-z0-9_]+)\=(\d+)\s+to\s+(\d+)\}/";
        $str = preg_replace($pattern, "<?php for(\\$$1 = $2; \\$$1 < $3; \\$$1++){ ?>", $str);
        //解析for结束标签
        $str = str_replace(['{/for}','{endfor}'], '<?php } ?>', $str);
    }

    /**
     * 解析if语句
     *
     * @param  string  $str
     * @return string|string[]
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午6:12
     */
    function parseIf(string & $str){
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
     * 解析常量
     *
     * @param  string  $str
     * @return string|string[]|null
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午6:11
     */
    public function parseConst(string & $str){
        //处理常量
        $pattern = '/\{([A-Z]+_?[A-Z]*)\}/';
        $str = preg_replace($pattern, "<?php echo $1; ?>", $str);
    }

    /**
     * 解析函数
     * @param  string  $str
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午6:11
     */
    public function parseFunction(string & $str)
    {
        $pattern = '/\{\{\s?([a-zA-Z0-9_]+)\((.*?)\)\s?\}\}/';
        $str = preg_replace($pattern, "<?php echo $1($2); ?>", $str);
    }

    /**
     * 处理变量一次匹配
     *
     * @param  string  $str
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午6:20
     */
    public function parseFieldsV2(string & $str){
        $pattern = '/\{\\$(.*?)\}/';
        $count = preg_match_all($pattern, $str,$match);
        for($i=0; $i < $count; $i++){
            $field = $match[0][$i];
            $value = $this->parseOneField($match[1][$i]);
            $value = '<?php echo '. $value .'; ?>';
            $str = str_replace($field, $value, $str);
        }
    }

    /**
     * 处理一个字段
     *
     * @param  string  $str
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午6:30
     */
    public function parseOneField(string $str){
        //$a.b 编译成 $a['b']
        if(strpos($str, '.')){
            if(strpos($str, '??')){
                $tmp = explode('??', $str);
                $fieldName = $this->parseDotField(trim($tmp[0]));
                $defaultValue = $tmp[1];
                //翻译成PHP5.6支持的语法
                return"isset(\${$fieldName}) ? \${$fieldName} : ". $defaultValue;
                //PHP7 可以用下面的语法
                //return '$'. $fieldName .' ??'. $tmp[1];
            }else{
                return '$'.$this->parseDotField($str);
            }
        }else{
            return '$'. $str;
        }
    }


    /**
     * 处理.分隔的字段
     *
     * @param string $str
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午6:35
     */
    public function parseDotField(string $str){
        $ary = explode('.', $str);
        $field = '';
        foreach ($ary as $v){
            if($field == ''){
                $field = $v;
            }else {
                $field .= "['" . $v . "']";
            }
        }
        return $field;
    }

    /**
     * 处理变量
     *
     * @param $str
     * @return string|string[]|null
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午6:11
     */
    public function parseFields(string & $str){
        //解析变量 两种格式 {$field}, {$field.field}, $s3原来就有空格了，就不用再加了
        //处理这种$a['b']['c'] ?? ''
        $pattern = '/\{\\$([a-zA-Z0-9_]+)\.+([a-zA-Z0-9_]+)\.+([a-zA-Z0-9_]+)\s+\?\?(.*?)\}/';
        $str = preg_replace($pattern, "<?php echo \\$$1['$2']['$3'] ??$4;?>", $str);
        //echo $str;//exit;
        //处理这种$a['b']['c']
        $pattern = '/\{\\$([a-zA-Z0-9_]+)\.+([a-zA-Z0-9_]+)\.+([a-zA-Z0-9_]+)\}/';
        $str = preg_replace($pattern, "<?php echo \\$$1['$2']['$3']; ?>", $str);
        //处理这种$a['b'] ?? '0'
        $pattern = '/\{\\$([a-zA-Z0-9_]+)\.+([a-zA-Z0-9_]+)\s+\?\?(.*?)\}/';
        $str = preg_replace($pattern, "<?php echo \\$$1['$2'] ??$3;?>", $str);
        //exit;
        //处理这种$a['b']
        $pattern = '/\{\\$([a-zA-Z0-9_]+)\.+([a-zA-Z0-9_]+)\}/';
        $str = preg_replace($pattern, "<?php echo \\$$1['$2']; ?>", $str);
        //处理$a
        $pattern = '/\{\\$([a-zA-Z0-9_]+)\}/';
        $str = preg_replace($pattern, "<?php echo \\$$1; ?>", $str);
    }
}