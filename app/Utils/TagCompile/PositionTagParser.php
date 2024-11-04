<?php
namespace App\Utils\TagCompile;

use App\Logics\ClassifyLogic;
use Core\Context;

/**
 * Class PageBarTagParse
 * 解析位置标签
 *
 * @package App\Libraries\HtmlMaker
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/17 下午3:10
 */
class  PositionTagParser{

    //处理自定义标记(模板内容)
    function processCustomTags(string & $templateContent)
    {
        $count = preg_match_all("/\<position(.*?)\>(.*?)\<\/position\>/i", $templateContent, $array);
        $tagBlockList = [];
        for ($i = 0; $i < $count; $i++) {
            $tagHtml = $array[0][$i];  //循环体, 包括<loop></loop>
            $props = trim($array[1][$i]); //属性
            //获取属性列表
            $propertyList = $this->getLoopProperty($props);
            $tagBody = $array[2][$i];
            $tagBlock = $this->parseOneTag($tagBody, $propertyList, $i);
            $tagBlock['loopHtml']= $tagHtml;
            $tagBlockList[] = $tagBlock;
            //$templateContent = str_replace($tagHtml, implode(PHP_EOL, $phpCode), $templateContent);
        }
        return $tagBlockList;
    }

    /**
     * 获取循环标记的所有属性
     *
     * @param  string  $loopProperty
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/22 下午4:18
     */
    function getLoopProperty(string $loopProperty)
    {
        //替换多余的空格
        $loopProperty = str_replace("  ", " ", trim($loopProperty));
        //替换掉标记中的单引号和双引号，不加也支持
        $loopProperty = str_replace(['"', "'"], '', $loopProperty);
        $list = explode(' ', $loopProperty);
        $propertyList = [];
        foreach ($list as $v) {
            if (empty($v)) {
                continue;
            }
            $tmp = explode('=', $v);
            //动态参数，从loop获取
            if (strpos($tmp[1], '[')) {
                $spos = strpos($tmp[1], '[') + 1;
                $epos = strpos($tmp[1], ']');
                $propertyList[$tmp[0]] = '$' . substr($tmp[1], $spos, $epos - $spos);
            } else {
                $propertyList[$tmp[0]] = $tmp[1];
            }
        }
        return $propertyList;
    }


    /**
     * 解析一个标签
     *
     * @param $tagBody
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/17 下午3:24
     */
    function parseOneTag($tagBody, $propertyList, $i){
        //位置标签，只需要class_id参数，其它不处理
        $phpCode = [];
        $phpCode[] = '<?php';
        $phpCode[] = 'function get_position_'. $i."(\$args){";
        if(isset($propertyList['class_id'])){
            $value = $propertyList['class_id'];
            //外部参数，这里只用"="查询
            if (strpos($value, ':') !== false) {
                $tmpField = str_replace(':', '', $value);
                $phpCode[] = "\$class_id = \$args['" . $tmpField . "'] ?? 0;";
            }
        }else{
            //从上下文中获取
            $phpCode[] = "\$class_id = context('request_args.class_id', 0);";
            $phpCode[] = "if(\$class_id == 0){\$class_id = context('global.class_id');}";
        }
        $phpCode[] = "if(\$class_id <=0 ){exit('<font color=\'red\'>参数错误</font>');}";
        $phpCode[] = "\$list = ClassifyLogic::getPosition(\$class_id);";
        $phpCode[] = "foreach(\$list as \$v){";
        $phpCode[] = "?>";
        //这里是标记体
        $phpCode[] = $this->getFields($tagBody);
        $phpCode[] = "<?php }";
        //函数结束
        $phpCode[] = "}?>";
        return [
            'function_html' => implode(PHP_EOL, $phpCode),
            'function_html_call' => "<?php get_position_{$i}(\$args); ?>"
        ];
    }




    /**
     * 将循环体中的所有字段, 转成php输出, 并返回所有字段名
     *
     * @param  string  $loopBody
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/17 上午9:51
     */
    function getFields(string & $loopBody)
    {
        $pattern = '/\{\$(.*?)\}/i';
        //构建loop的PHP代码时用
        $count = preg_match_all($pattern, $loopBody, $array);
        for ($i = 0; $i < $count; $i++) {
            $fieldTag = $array[0][$i];  //循环体
            $fieldName = $array[1][$i];
            //$v[字段名]
            if($fieldName == 'list_url'){
                $fieldHtml = "<?php echo list_url(\$v['url']); ?>";
            }else{
                $fieldHtml = "<?php echo \$v['{$fieldName}']; ?>";
            }
            //将字段替换成PHP语句
            $loopBody = str_replace($fieldTag, $fieldHtml, $loopBody);
        }
        return $loopBody;
    }
}