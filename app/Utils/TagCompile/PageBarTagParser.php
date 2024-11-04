<?php
namespace App\Utils\TagCompile;

/**
 * Class PageBarTagParse
 * 解析分页标签
 *
 * @package App\Libraries\HtmlMaker
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/17 下午3:10
 */
class  PageBarTagParser{

    //处理自定义标记(模板内容)
    function processCustomTags(string & $templateContent)
    {
        $count = preg_match_all("/\<pagebar(.*?)\>(.*?)\<\/pagebar\>/i", $templateContent, $array);
        $tagBlockList = [];
        for ($i = 0; $i < $count; $i++) {
            $tagHtml = $array[0][$i];  //循环体, 包括<loop></loop>
            //属性列表，用于设置样式
            $propertyList = getPropertyFromString($array[1][$i]);
            $tagBody = $array[2][$i];
            $tag = $this->parseOneTag($propertyList, $tagBody, $i);
            $tag['loopHtml'] = $tagHtml;
            $tagBlockList[] = $tag;
        }
        return $tagBlockList;
    }

    /**
     * 解析一个标签
     *
     * @param $tagBody
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/17 下午3:24
     */
    function parseOneTag($propertyList, $tagBody, $i){
        //转义单引号，设置模板的时候要用
        $tagBody = str_replace('\'', '\\\'', $tagBody);
        $phpCode = "<?php \n
        function get_pagebar_{$i}(){
        \$str = '{$tagBody}';
        \$pagebar = new PageBar();
        ";
        //处理样式
        if(! empty($propertyList)){
            //按钮样式
            if(isset($propertyList['button_class'])){
                //多个样式，之间是用 ｜ 隔开，要转换成空格
                $propertyList['button_class'] = str_replace('|',' ', $propertyList['button_class']);
                $phpCode .= "\$pagebar->setButtonClass('{$propertyList['button_class']}');". PHP_EOL;
            }
            //数字样式
            if(isset($propertyList['number_class'])){
                //多个样式，之间是用 ｜ 隔开，要转换成空格
                $propertyList['number_class'] = str_replace('|',' ', $propertyList['number_class']);
                $phpCode .= "\$pagebar->setNumberClass('{$propertyList['number_class']}');". PHP_EOL;
            }
            //数字选中样式
            if(isset($propertyList['number_active_class'])){
                //多个样式，之间是用 ｜ 隔开，要转换成空格
                $propertyList['number_active_class'] = str_replace('|',' ', $propertyList['number_active_class']);
                $phpCode .= "\$pagebar->setNumberActiveClass('{$propertyList['number_active_class']}');". PHP_EOL;
            }
            //数字选中的HTML标签, a span
            if(isset($propertyList['number_active_html'])){
                $phpCode .= "\$pagebar->setNumberActiveHtml('{$propertyList['number_active_html']}');". PHP_EOL;
            }
        }
        $phpCode .= "\$args = context('request_args');
        \$class_id = \$args['class_id'] ?? 0;
        \$url = list_url_by_class_id(\$class_id);   
        \$pagebar->setPageUrl(\$url);
        \$pagebar->setTemplate(\$str);
        \$config = context('pageInfo'); 
        return \$pagebar->show(\$config['total'], \$config['pageSize'], \$config['page']);
        } ?>";

        return [
            'function_html' => $phpCode,
            'function_html_call' => "<?php echo get_pagebar_{$i}(); ?>"
        ];
    }

}