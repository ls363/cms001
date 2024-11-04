<?php
namespace App\Utils\TagCompile;

use App\Enums\HtmlEnums;
use App\Logics\ClassifyLogic;

/**
 * Class CustomTagParser
 * 自定义标记解析类
 *
 * @package App\Libraries\html_maker
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/22 下午4:56
 */
class LoopTagParser
{

    protected $propertyParser = null;
    private $loopType = "";  //特殊标签类型的控制

    public function __construct()
    {
        $this->propertyParser = new PropertyParser();
    }

    //

    /**
     * 解析静态标签，包括网站配置，外部传入参数
     * 替换成PHP语句
     *
     * @param  string  $templateContent
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/15 下午4:50
     */
    public function parseStaticTag(string &$templateContent)
    {
        //解析静态文件路径
        $templateContent = str_replace('{TEMPLATE_SKIN}', "<?php echo context('template_skin'); ?>", $templateContent);
        //替换公共变量
        $templateContent = str_replace('{PUBLIC_URL}', PUBLIC_URL, $templateContent);
        //替换全局函数调用
        $pattern = "/\{\{(.*?)\}\}/i";
        $templateContent = preg_replace($pattern, "<?php echo $1; ?>", $templateContent);
        //替换公司信息
        if(strpos($templateContent, '{company.')){
            $pattern = "/\{company.(.*?)\}/i";
            $templateContent = preg_replace($pattern, "<?php echo \$company['$1']; ?>", $templateContent);
        }
        //替换栏目信息
        if(strpos($templateContent, '{classify.')){
            $pattern = "/\{classify.(.*?)\}/i";
            $templateContent = preg_replace($pattern, "<?php echo \$classify['$1']; ?>", $templateContent);
        }
        //替换信息
        if(strpos($templateContent, '{side_main.')){
            $pattern = "/\{side_main.(.*?)\}/i";
            $templateContent = preg_replace($pattern, "<?php echo context('side_main.$1'); ?>", $templateContent);
        }

        //替换网站配置, 可以支持函数了
        $pattern = "/\{\#(.*?)\#\}/i";
        $count = preg_match_all($pattern, $templateContent, $array);
        if ($count > 0) {
            foreach ($array[0] as $k => $v){
                $templateContent = str_replace($array[0][$k], $this->parseStaticField($array[1][$k]), $templateContent);
            }
        }
    }

    private function parseStaticField($fieldStr){
        if(strpos($fieldStr, ',') === false){
            return '<?php echo $site_config[\''. $fieldStr .'\']; ?>';
        }
        $arr = explode(',', $fieldStr);
        $field = array_shift($arr);
        $func = array_shift($arr);
        $field = '$site_config[\''. $field .'\']';
        if(empty($arr)){
            $arr = null;
            return "<?php echo {$func}({$field}); ?>";
        }else{
            $field .= ', '.implode(',', $arr);
            $arr = null;
            return "<?php echo {$func}({$field}); ?>";
        }
    }

    /**
     * 提取if标签中的字段
     *
     * @param  string  $str
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/16 下午12:53
     */
    public function parseIfFields(string & $str){
        //解析if开始标签, 区别于视图层，这里还需要做参数提取
        $pattern = "/\{(else)?if\s?(.*?)\}/";
        $count = preg_match_all($pattern, $str, $array);
        $fields = [];
        for ($i = 0; $i < $count; $i++) {
            //解析字段标签(循环标记, 字段TAG, 字段名称)
            $fieldBody = $array[2][$i];
            $pattern2 = '/\$item\[\'(.*?)\'\]/';
            $count2 = preg_match_all($pattern2, $fieldBody, $array2);
            if($count2 > 0){
                $fields = array_merge($fields, $array2[1]);
            }
        }
        return $fields;
    }

    /**
     * 解析if语句
     *
     * @param  string  $str
     * @return string|string[]
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午6:12
     */
    public function parseIf(string & $str){
        if(strpos($str, '{/if}') === false){
            return;
        }
        //解析if开始标签, 区别于视图层，这里还需要做参数提取
        $pattern = "/\{(else)?if\s?(.*?)\}/";
        $str = preg_replace($pattern, "<?php $1if ($2){ ?>", $str);
        //解析else标签
        $str = str_replace('{else}', '<?php }else{ ?>', $str);
        //解析if结束标签
        $str = str_replace(['{/if}', '{/endif}', '{endif}'], '<?php } ?>', $str);
    }


    public function getNamespacePHP(){
        $str = '';
        $str .= 'use App\Facades\Db;' . PHP_EOL;
        $str .= 'use App\Utils\PageBar;' . PHP_EOL;
        $str .= 'use App\Logics\SystemLogic;' . PHP_EOL;
        $str .= 'use App\Logics\CompanyLogic;' . PHP_EOL;
        $str .= 'use App\Logics\ClassifyLogic;' . PHP_EOL;
        $str .= 'use App\Logics\UploadsLogic;' . PHP_EOL;
        $str .= 'use App\Logics\ContentModuleExtendLogic;' . PHP_EOL;
        $str .= 'use App\Cache\CommonCache;' . PHP_EOL;
        return $str;
    }

    /**
     * 增加获取系统配置的函数调用
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/15 下午4:57
     */
    public function getStaticData()
    {
        $str = '<?php ' . PHP_EOL;
        //$str .= 'use App\Facades\Config;' . PHP_EOL;
        $str .= $this->getNamespacePHP();
        //路由中已经设置了，所以先注释
        //$str .= '$args = request()->all();' . PHP_EOL;
        //$str .= 'context_set(\'request_args\', $args);' . PHP_EOL;
        $str .= '$site_config = SystemLogic::getSystemCache();' . PHP_EOL;
        $str .= 'context_set(\'site_config\', $site_config);' . PHP_EOL;
        $str .= '$company = CompanyLogic::getCompanyCache();' . PHP_EOL;
        $str .= '$classify = ClassifyLogic::getByIdCache(input(\'class_id\'));' . PHP_EOL;

        $str .= 'if(input(\'class_id\') > 0){' . PHP_EOL;
        //全局的栏目信息
        $str .= 'context_set(\'classify\', $classify);' . PHP_EOL;
        //默认global为栏目信息
        $str .= 'context_set(\'global\', $classify);' . PHP_EOL;
        $str .= '}'. PHP_EOL;;
        $str .= '$template_skin = TEMPLATE_URL .\'/\'. $site_config[\'skin\'];';
        $str .= 'context_set(\'template_skin\', $template_skin);' . PHP_EOL;
        $str .= '?>';
        return $str;
    }

    //解析自由标签, 暂未启用
    public function parseAutoTag(string $templateContent)
    {
        //分析包含文件
        //$count = preg_match_all("/\{Auto_(.*?)\}/i", $templateContent, $array);
        //print_r($array);
        /*
        for ($i = 0; $i < $count; $i++) {

        }*/
    }

    //处理自定义标记(模板内容)
    function processCustomTags(string $templateContent, string $loopTag)
    {
        $content_html = $templateContent;
        $count = preg_match_all("/\<" . $loopTag . "(.*?)\>(.*?)\<\/" . $loopTag . "\>/i", $content_html, $array);
        //print_r($array);exit;
        $loopList = [];
        for ($i = 0; $i < $count; $i++) {
            //ParseLoopTag(循环标记，属性集，循环体)
            $loopHtml = $array[0][$i];  //循环体, 包括<loop></loop>
            $loopProperty = $array[1][$i];  //loop标记中的属性
            $loopBody = $array[2][$i];      //loop标记中间的内容(<loop ..>{LoopBody}</loop>), 不包括<loop></loop>
            $emptyBody = ""; //数据为空的处理，主要用于列表
            //处理数据为空的情况
            if(strpos($loopBody, '</empty>')){
                //每个loop只允许其中一个empty标签
                 preg_match("/\<empty\>(.*?)\<\/empty\>/i", $loopBody, $arrayTemp);
                 //$loopBody移除empty标记的内容
                 $loopBody = str_replace($arrayTemp[0], '', $loopBody);
                 $emptyBody = $arrayTemp[1];
            }
            //获取loop属性，返回数据
            $propertyList = $this->getLoopProperty($loopProperty);
            //获取SQL语句用的字段，用于构造变量
            $sql_fields = $this->getOnlyFields($loopBody, $loopTag);
            //获取记录数
            $recordNum = isset($propertyList['record_num']) ? $propertyList['record_num'] : 0;

            //先处理nextloop再来处理外层的loop
            $nextPhpCodeList = $this->processNextLoopTag($loopBody, $i, $recordNum);
            $nextHtmlFunction = '';
            $nextDataFunction = [];
            if (!empty($nextPhpCodeList)) {
                foreach ($nextPhpCodeList as $v) {
                    //将标签改为函数调用
                    $loopBody = str_replace($v['loopHtml'], $v['function_data_call'] . $v['function_html_call'],
                        $loopBody);
                    //将nextloop转成单独的function
                    $nextHtmlFunction .= PHP_EOL . $v['function_html'];
                    $nextDataFunction[] = $v['function_data'];
                    //增加loop_data中的字段
                    if(isset($v['loopFields'])){
                        $sql_fields[] = $v['loopFields'];
                    }
                }
            }

            //echo $nextPhpFunction;exit;
            //echo $loopBody;exit;

            //$loopBody = $array[2][$i];      //loop标记中间的内容(<loop ..>{LoopBody}</loop>), 不包括<loop></loop>
            //{function=>'', function_call=>''}
            $phpCode = $this->parseLoopTag($loopHtml, $loopTag, $propertyList, $loopBody, $sql_fields, $i, 0, $emptyBody);    //标签转成PHP函数<loop..></loop>
            $phpCode['loopHtml'] = $loopHtml;
            $phpCode['nextHtmlFunction'] = $nextHtmlFunction;
            $phpCode['nextDataFunction'] = $nextDataFunction;
            $loopList[] = $phpCode;
        }
        return $loopList;
        //$content_html = str_replace(HtmlEnums::HTML_BR_CODE, "\r\n", $content_html);//恢复换行符
        //return $content_html;
    }

    /**
     * 处理nextloop标签
     * 1. 外层有nextloop
     * 2. 属性中有
     *
     * @param $loopHtml
     * @param $i 父级loop是第几个
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/16 下午11:03
     */
    public function processNextLoopTag($loopHtml, $i, $parentRecordNum)
    {
        if (strpos($loopHtml, '<' . HtmlEnums::NEXT_LOOP_TAG)) {
            $loopTag = HtmlEnums::NEXT_LOOP_TAG;
            $count2 = preg_match_all("/\<" . HtmlEnums::NEXT_LOOP_TAG . "(.*?)\>(.*?)\<\/" . HtmlEnums::NEXT_LOOP_TAG . "\>/i",
                $loopHtml, $array);
            $nextLoopList = [];
            for ($j = 0; $j < $count2; $j++) {
                //ParseLoopTag(循环标记，属性集，循环体)
                $loopHtml = $array[0][$j];  //循环体, 包括<loop></loop>
                $loopProperty = $array[1][$j];  //loop标记中的属性
                $loopBody = $array[2][$j];      //loop标记中间的内容(<loop ..>{LoopBody}</loop>), 不包括<loop></loop>
                //获取loop属性，返回数据
                $propertyList = $this->getLoopProperty($loopProperty);
                $sql_fields = $this->getOnlyFields($loopBody, $loopTag);
                $phpCode = $this->parseLoopTag($loopHtml, $loopTag, $propertyList, $loopBody, $sql_fields,
                    $i . '_' . $j, $parentRecordNum);    //标签转成PHP函数<loop..></loop>
                $phpCode['loopHtml'] = $loopHtml;
                //返回扩展性中的的字段
                $loopType = $propertyList['loop_type'] ?? '';
                //用于处理loop_type=data的情况
                if($loopType == 'data' && isset($propertyList['data'])){
                    $phpCode['loopFields'] = $this->convertField($propertyList['data']);
                }
                $nextLoopList[] = $phpCode;
            }
            return $nextLoopList;
        }
        return [];
    }

    /**
     * 解板Loop标签，nextLoop还未加入, 处理了一种特殊情况，loop_type=data, 数组扩展的情况
     *
     * @param string $loopHtml 用于生成缓存key
     * @param  string  $loopTag
     * @param  array  $propertyList
     * @param  string  $loopBody
     * @param $sql_fields
     * @param $i
     * @param $parentLoopRecordNum
     * @return string[]
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 上午12:00
     */
    function parseLoopTag(string $loopHtml, string $loopTag, array $propertyList, string $loopBody, $sql_fields, $i, $parentLoopRecordNum, $emptyBody='')
    {
        //将loop还原为可执行的PHP语句
        //查询记录数量，区别于记录处理
        $recordNum = isset($propertyList['record_num']) ? intval($propertyList['record_num']) : 0;
        //处理循环体, 转成SQL语句
        $this->getFields($loopBody, $loopTag, intval($i), $recordNum, $emptyBody);
        //解析loop的属性, 返回PHP语句，及调用的变量（调用变量不能重名，函数内部的变量可以重名）
        $loopResult = $this->parseLoopProperty($loopHtml, $loopTag, $propertyList, $sql_fields, $i);

        //函数名称
        $functionDataName = "get_{$loopTag}_{$i}_data";
        $functionHtmlName = "get_{$loopTag}_{$i}_html";
        $functionDataArgs = '';
        $functionDataArgsCall = '';
        //函数是列表还是单条记录
        if($loopResult['varType'] == 'list'){
            $functionHtmlArgs = 'array & $list';
        }else{
            $functionHtmlArgs = 'array & $result';
        }
        $functionHtmlArgsCall = $loopResult['varName'];

        //只有nextloop需要传入上一个查询结果
        if ($loopTag == 'nextloop') {
            $functionDataArgs .= '$loop_result';
            if ($parentLoopRecordNum == 1) {//loop中是单个记录用result
                $functionDataArgsCall .= '$result';
                $functionHtmlArgs .= ' , $loop_result';
                $functionHtmlArgsCall .= ', $result';
            } else {//loop中是列表
                $functionDataArgsCall .= '$item';// '$loop_' . $tmp[0] . '_item';
                $functionHtmlArgs .= ', $loop_result';
                $functionHtmlArgsCall .= ', $item'; //', $loop_' . $tmp[0] . '_item';
            }
        }

        //增加数据缓存
        $function_data = '<?php ' . PHP_EOL . ' function ' . $functionDataName . '(' . $functionDataArgs . '){' . PHP_EOL
            //. ' $args = context("request_args");' . PHP_EOL
            . $loopResult['phpCode'] . PHP_EOL
            . "}?>" . PHP_EOL;

        $function_html_call = "<?php {$functionHtmlName}(" . $functionHtmlArgsCall . "); ?>" . PHP_EOL;
        //生成用完释放变量的语句
        $tmpHtmlArgsCall = explode(', ', $functionHtmlArgsCall);
        $function_html_call .= "<?php {$tmpHtmlArgsCall[0]} = null; ?>" . PHP_EOL;

        return [
            //获取数据的函数
            'function_data'      => $function_data,
            //显示Html的函数
            'function_html'      => '<?php ' . PHP_EOL
                . ' function ' . $functionHtmlName . '(' . $functionHtmlArgs . '){' . PHP_EOL
                . '$site_config = context("site_config");' . PHP_EOL
                . ' $args = context("request_args");' . PHP_EOL . '?>'
                . $loopBody
                . '<?php $site_config = null;'. PHP_EOL
                . '$args = null;'. PHP_EOL
                . "\n }?>",
            //获取DATA存放到放量
            'function_data_call' => "<?php {$loopResult['varName']} = {$functionDataName}({$functionDataArgsCall}); ?>" . PHP_EOL,
            //调用HTML函数
            'function_html_call' => $function_html_call,
        ];
    }


    /**
     * 生成函数定义
     *
     * @param $functionName
     * @param $functionArgs
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/14 上午7:54
     */
    function makeFunction($functionName, $functionArgs, $functionBody){
        //获取数据的函数
        return '<?php ' . PHP_EOL
                . ' function ' . $functionName . '(' . $functionArgs . '){' . PHP_EOL
                . ' $args = context("request_args");' . PHP_EOL
                . $functionBody . PHP_EOL
                . "}?>" . PHP_EOL;
    }


    /**
     * 生成函数调用
     *
     * @param $functionName
     * @param $functionArgs
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/14 上午7:54
     */
    function makeFunctionCall($functionName, $functionArgs){
        return "<?php {$functionName}(" . $functionArgs . "); ?>" . PHP_EOL;
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
        //部分标签纠错,存在id,未设置record_num的情况
        if(isset($propertyList['id']) && !isset($propertyList['record_num'])){
           $loopType = $propertyList['loop_type'] ?? '';
            if(in_array($loopType, ['side_menu', 'related', 'position']) === false){
                $propertyList['record_num'] = 1;
            }
        }
        return $propertyList;
    }

    /**
     * 将循环体中的所有字段, 转成php输出, 并返回所有字段名
     *
     * @param  string  $loopBody
     * @param  string  $loopTag
     * @param int $k
     * @param int $recordNum 用于判断单条记录的PHP语句生成
     * @param string $emptyBody
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/17 上午9:51
     */
    function getFields(string &$loopBody, string $loopTag, int $k=0, int $recordNum=1, string $emptyBody='')
    {
        $loopBody = str_replace('{index}', '<?php echo $index; ?>', $loopBody);
        $loopBody = str_replace('{i}', '<?php echo $index+1; ?>', $loopBody);
        $this->parseIf($loopBody);
        if ($loopTag == "nextloop") {
            $pattern = '/\[\$(.*?)\]|{\$(.*?)\}/i';
            $tmp = explode('_', $k);
            $j = $tmp[0];
        } else {
            $pattern = '/\{\$(.*?)\}/i';
        }
        //构建loop的PHP代码时用
        //$loop_list = "\${$loopTag}_{$k}_list";
        $loop_list = "\$list";  //局部变量，直接用$list $v
        //返回字段列表，组装SQL要用到
        $fieldList = [];
        $count = preg_match_all($pattern, $loopBody, $array);
        for ($i = 0; $i < $count; $i++) {
            //字段标记
            $fieldTag = $array[0][$i];
            //解析字段标签(循环标记, 字段TAG, 字段名称)
            $fieldBody = str_replace(['{$', '$}', '}', '[$', '$]', ']'], '', $array[0][$i]);
            if ($loopTag == 'nextloop' && strpos($fieldTag, '}')) {
                $loop_item = "\$loop_result";
            } else {
                if ($recordNum == 1) {
                    //$loop_item = "\${$loopTag}_{$k}_result";
                    $loop_item = "\$result";    //局部变量，直接用result
                } else {
                    //$loop_item = "\${$loopTag}_{$k}_item";
                    $loop_item = "\$item"; //局部变量，直接用item
                }
            }

            //字段存在函数调用的情况 $title,cutString,1 格式：$字段名,函数名,参数2,参数3,参数4
            if (strpos($fieldBody, ',')) {
                $extension = explode(',', $fieldBody);
                $fieldNameArr = array_splice($extension, 0, 1);
                $fieldName = $fieldNameArr[0]; //字段名称
                //$v[字段名]
                $fieldValue = "{$loop_item}['{$fieldName}']";
                $len = count($extension);
                if ($len == 1) {
                    $functionName = $extension[0];
                    //方法名($v[字段名])
                    $fieldHtml = "<?php echo {$functionName}({$fieldValue}); ?>";
                } else {
                    //获取函数名称, 并抛出参数
                    $functionName = array_splice($extension, 0, 1);
                    //处理参数中有$的情况
                    foreach($extension as $ek => $ev){
                        if(substr($ev, 0 ,1) == '$'){
                            $tmp = substr($ev, 1);
                            $extension[$ek] = $loop_item . "['{$tmp}']";
                        }else{
                            if(is_numeric($ev) === false){
                                $extension[$ek] = "'{$ev}'";
                            }
                        }
                    }
                    //将字段值，加到第一个
                    array_unshift($extension, $fieldValue);
                    //方法名($v[字段名], 参数1, 参数2, 参数n...)
                    $fieldHtml = "<?php echo {$functionName[0]}(" . implode(', ', $extension) . "); ?>";
                }
            } else {
                $fieldName = $fieldBody;
                //内容页地址转换
                switch ($fieldName){
                    case 'list_url': //list_url($v['id'])
                        $fieldHtml = "<?php echo list_url({$loop_item}['url']); ?>";
                        break;
                    case 'content_url': //content_url($v['id'], $v['class_id'])
                        $fieldHtml = "<?php echo content_url({$loop_item}['id'], {$loop_item}['class_id']); ?>";
                        break;
                    default: //$v[字段名]
                        $fieldHtml = "<?php echo {$loop_item}['{$fieldName}']; ?>";
                }
            }
            $fieldList[] = $this->convertField($fieldName);
            //将字段替换成PHP语句
            $loopBody = str_replace($fieldTag, $fieldHtml, $loopBody);
        }
        if ($recordNum == 1) {
            //$loop_item = "\${$loopTag}_{$k}_result";
            $loop_item = "\$result";
            $loopBody = '<?php if(empty(' . $loop_item . ')){ ?>' . PHP_EOL
                . $emptyBody . PHP_EOL
                . '<?php }else{ ?>'
                . $loopBody . PHP_EOL
                . '<?php  }' . PHP_EOL
                . ' ?>';
        } else {
            //$loop_item = "\${$loopTag}_{$k}_item";
            //始终带上 index 索引 ，替换 {index} 0 开始 {sequnece} 从1开始
            $loop_item = '$index => $item';
            $loopBody = '<?php' . PHP_EOL
                . 'if( empty(' . $loop_list . ')){?>' . PHP_EOL
                . $emptyBody . PHP_EOL
                . '<?php }else{' .PHP_EOL
                . '   foreach(' . $loop_list . ' as ' . $loop_item . '){' . PHP_EOL
                . '?>' . PHP_EOL
                . $loopBody . PHP_EOL
                . '<?php  }' . PHP_EOL
                . '}' . PHP_EOL
                . ' ?>';
        }
        return $fieldList;
    }


    /**
     * 将循环体中的所有字段名
     *
     * @param  string  $loopBody
     * @param  string  $loopTag
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/17 上午9:51
     */
    function getOnlyFields(string &$loopBody, string $loopTag)
    {
        if ($loopTag == "nextloop") {
            $pattern = '/\[\$(.*?)\]/i';
        } else {
            $pattern = '/\{\$(.*?)\}/i';
        }
        //返回字段列表，组装SQL要用到
        $fieldList = [];
        if(strpos($loopBody, '{/if}')){
            $fieldsIf = $this->parseIfFields($loopBody);
            if(! empty($fieldsIf)){
                $fieldList = $fieldsIf;
            }
        }
        $count = preg_match_all($pattern, $loopBody, $array);
        for ($i = 0; $i < $count; $i++) {
            //解析字段标签(循环标记, 字段TAG, 字段名称)
            $fieldBody = $array[1][$i];
            //字段可能的情况 $title,cutString,1
            if (strpos($fieldBody, ',')) {
                $extension = explode(',', $fieldBody);
                $fieldName = $extension[0]; //字段名称
                $fieldList[] = $fieldName;
                if(count($extension) > 2){
                    for ($j=2; $j < count($extension); $j++){
                        if(substr($extension[2], 0,1) == '$'){
                            $fieldList[] = substr($extension[2], 1);
                        }
                    }
                }
            } else {//fieldBody就是字段的情况
                $fieldList[] = $fieldBody;
            }
        }
        //字段去重
        $fieldList = array_unique($fieldList);
        //有些字段要做转换
        $otherFields = [];
        foreach ($fieldList as $fk => $fv){
            $tmpConvert = $this->convertField($fv);
            if(is_array($tmpConvert)) {
                //释放该元素，循环完毕再加入
                unset($fieldList[$fk]);
                //暂存数据
                $otherFields = array_merge($otherFields, $tmpConvert);
            }else{
                $fieldList[$fk] = $tmpConvert;
            }
        }
        if(! empty($otherFields)){
            $fieldList = array_merge($fieldList, $otherFields);
        }
        //再次去重
        $fieldList = array_unique($fieldList);
        return $fieldList;
    }

    /**
     * 字段转换
     *
     * @param  string  $fieldName
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/27 下午8:37
     */
    public function convertField(string $fieldName)
    {
        switch ($fieldName) {
            case 'slide_list':
                return ['slide','remark'];
            case 'cover':
            case 'cover_pic_big':
                return 'cover';
            case 'content_url';
                return ['id','class_id'];
            case 'list_url':
                return 'url';
        }
        return $fieldName;
    }


    /**
     * 转换扩展表的字段，使用content + content_extend_1这种情况下使用
     * 2022-09-05 关闭扩展表，新建模型复制表，增加模型字段，在复制的表上新增
     *
     * @param  string  $tableName
     * @param  array  $fields
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/29 下午9:45
     */
    public function convertExtendFields(string $tableName, array $fields)
    {
        //判断是否需要转换
        //if (in_array($tableName, ['article', 'content']) === false) {
            return [
                'need_convert'  => false,
                'table'         => '',    //扩展名称
                'extend_fields' => [],   //扩展字段
                'select_fields' => $fields  //select用的字段
            ];
        //}
        //模型扩展信息
        $extendInfo = ClassifyLogic::getExtendFields(1);
        $newFields = [];
        foreach ($fields as $v) {
            if (in_array($v, $extendInfo['fields'])) {
                $newFields[] = 'e.' . $v;
            } else {
                $newFields[] = 'm.' . $v;
            }
        }
        return [
            'need_convert'  => true,
            'table'         => $extendInfo['table'],    //扩展名称
            'extend_fields' => $extendInfo['fields'],   //扩展字段
            'select_fields' => $newFields  //select用的字段
        ];
    }


    /**
     * 解析Loop标记中的属性，转成SQL查询用到的参数
     *
     * @param  array  $property_array
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/2 下午5:46
     */
    public function parseLoopProperty(string & $loopHtml, string $loopTag, array $property_array, array $fields, $i)
    {
        $this->loopType = $property_array['loop_type'] ?? '';
        //获取标签中的表名，并释放标签中的变量name 或 table_name, 自定义表单，需要通过table作为缓存key
        $tableName = $this->propertyParser->parsePropertyTableName($property_array, $loopHtml);
        //处理数据缓存
        $cache = $property_array['cache'] ?? 0;
        $cacheKey = '';
        if($cache > 0){
            unset($property_array['cache']);
            if($tableName == 'diy_form_field'){
                $cacheKey = $tableName .'-'. $property_array['form_id'];
            }else{
                $cacheKey = md5($loopHtml);
            }
        }
        $strCache = '';
        //提取loop_type
        $loop_type = '';
        if(isset($property_array['loop_type'])){
            $loop_type = $property_array['loop_type'];
            unset($property_array['loop_type']);
        }

        //处理扩展字段
        if($loop_type == 'extend_field'){
            $resultVarName = "\${$loopTag}_{$i}_list";
            $field = $property_array['field'];
            $loop_var = [];
            $loop_var[] = "\$model_id = context('model_id');";
            $loop_var[] = "return ContentModuleExtendLogic::getExtendFieldOption(\$model_id, '{$field}');";
            $loop_content = implode(PHP_EOL, $loop_var);
            return ['phpCode' => $loop_content, 'varName' => $resultVarName, 'varType' => 'list'];
        }

        //处理loop_type=data的情况，文章的轮播图扩展
        if ($loopTag == 'nextloop' && $loop_type == 'data') {
            $resultVarName = "\${$loopTag}_{$i}_list";
            $data_field = $property_array['data'];
            $data_field = str_replace(['{$', '$}', '{','}'], '', $data_field);
            $loop_var = [];
            $loop_var[] = "return \$loop_result['" . $data_field . "'];";
            $loop_content = implode(PHP_EOL, $loop_var);
            return ['phpCode' => $loop_content, 'varName' => $resultVarName, 'varType' => 'list'];
        }

        //处理二级菜单的场景
        if ($loopTag == 'nextloop' && $loop_type == 'sub_menu') {
            $resultVarName = "\${$loopTag}_{$i}_list";
            //二级菜单，可以写死为child_list
            $loop_var = [];
            $loop_var[] = "return \$loop_result['child_list'];";
            $loop_content = implode(PHP_EOL, $loop_var);
            return ['phpCode' => $loop_content, 'varName' => $resultVarName, 'varType' => 'list'];
        }

        //单条信息页面，分类列表和文章列表，页面标题的处理
        $global = 0;
        if(isset($property_array['global'])){
            $global = $property_array['global'];
            unset($property_array['global']);
        }
        //$global==1的时候，只能取一条记录
        if ($global == 1) {//纠错
            $property_array['record_num'] = 1;
        }

        //处理HTML表格参数, 表格暂时未使用，但需要处理掉参数
        $this->propertyParser->parseHtmlTable($property_array);

//        dd($property_array);


        //生成的PHP语句
        $loop_var = [];     //变量声明语句
        $loop_query = [];   //执行的语句

        if($cache == 1) {
            $loop_var[] = '$tmpCache = CommonCache::get(\''. $cacheKey .'\');' . PHP_EOL
                . 'if(! empty($tmpCache)){' . PHP_EOL
                . 'return $tmpCache;' . PHP_EOL
                . '}' . PHP_EOL;
        }
        $loop_var[] = ' $args = context("request_args");' . PHP_EOL;

        //获取扩展表信息，并判断是否需要转换
        $extendInfo = $this->convertExtendFields($tableName, $fields);
        $extendFields = [];

        //生成主表查询
        $loop_query[] = "\$db = Db::table('{$tableName}');";
        //需要联表的情况
        if ($extendInfo['need_convert']) {
            $loop_query[] = "\$db->alias('m');";
            $loop_query[] = "\$db->join('{$extendInfo['table']}', 'e', 'id', 'content_id');";
        }

        //非全局的情况，只查询所选字段的数据，global=1 查询全部
        if ($global != 1) {
            if(in_array('id', $extendInfo['select_fields']) === false){
                $extendInfo['select_fields'][] = 'id';
            }
            //处理字段选择sub_menu
            $loop_var[] = "\$fields = " . var_export($extendInfo['select_fields'], true) . ';';
            $loop_query[] = "\$db->select(\$fields);";
        }

        //解析包含子类 class_id + include_child=1
        $this->propertyParser->parseChildClassId($extendInfo, $property_array, $loop_var, $loop_query);

        //解析标签 tag
        $this->propertyParser->parseTag($extendInfo, $property_array, $loop_var, $loop_query);

        //解析loop_type 处理特殊类型
        $this->propertyParser->parsePropertyLoopType($tableName, $loop_type, $property_array, $loop_var, $loop_query);
        //处理分页参数
        $pageArr = $this->propertyParser->getPagerArgs($property_array,$loop_var);

        //搜索相关的参数
        $searchArr = $this->propertyParser->getSearchArgs($property_array);

        //排序相关
        $orderBy = $this->propertyParser->parsePropertyOrderBy($property_array);

        //几天前的数据
        $this->propertyParser->parsePropertyBeforeDay($property_array, $loop_var, $loop_query);
        //这里剩下的都是where参数
        foreach ($property_array as $field => $value) {
            if($extendInfo['need_convert'] === false){
                $this->propertyParser->parsePropertyWhere('', $field, $value, $loop_var, $loop_query);
                continue;
            }
            //扩展字段
            if (in_array($field, $extendInfo['extend_fields'])) {
                $extendFields[$field] = $value;
                $this->propertyParser->parsePropertyWhere('e.', $field, $value, $loop_var, $loop_query);
            } else {//主表的查询条件
                //处理where参数
                $this->propertyParser->parsePropertyWhere('m.', $field, $value, $loop_var, $loop_query);
            }
        }

        //处理搜索
        $this->propertyParser->parseSearchArgs($searchArr,$loop_var, $loop_query);

        //排序
        if (!empty($orderBy)) {
            $loop_var[] = "\$orderBy = " . var_export($orderBy, true) . ";";
            $loop_query[] = "\$db->orderby(\$orderBy);";
        }

        //不分页，取几条
        if (isset($pageArr['record_num'])) {
            $loop_query[] = "\$db->limit(\$record_num);";
        }
        $varType = 'list';
        $isPage = isset($pageArr['is_page']) ? $pageArr['is_page'] : 0;
        if ($isPage == 0) {//不分页
            //最终取数据
            if (isset($pageArr['record_num']) && $pageArr['record_num'] == 1) {//取一行的情况
                $varType = 'result';
                //详情页结果集获取, 单个分类也是有可能的
                $resultVarName = $this->packDetailResultLoopQuery( $loopHtml, $cacheKey, $loop_query, $loopTag, $i, $fields, $global, $tableName);
            } else {
                $resultVarName = $this->packListResultLoopQuery($cacheKey, $loop_query, $loopTag, $i,$fields);
            }
        } else {//处理分页
            $resultVarName = $this->packPagerResultLoopQuery($loop_query, $loopTag, $i, $fields);
        }
        //函数体中间的PHP代码
        $loop_content = implode(PHP_EOL, $loop_var) . PHP_EOL . implode(PHP_EOL, $loop_query);
        //PHP代码，结果集变量
        return ['phpCode' => $loop_content, 'varName' => $resultVarName, 'varType' => $varType];
    }

    /**
     * 列表获取的结果封装
     * @param  string  $cacheKey
     * @param $loop_query
     * @param $loopTag
     * @param $i
     * @param $fields
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/29 下午11:30
     */
    public function packListResultLoopQuery(string $cacheKey, & $loop_query, $loopTag, $i,$fields){
        $resultVarName = "\${$loopTag}_{$i}_list";
        $loop_query[] = '$list = $db->get();';
        //如果存在子菜单调用，批量处理子菜单
        if($loopTag == 'loop' && $this->loopType == 'main_menu'){
            //如果是菜单调用，批量增加子菜单
            $loop_query[] = 'ClassifyLogic::batchGetChildList($list);';
        }
        //列表转换封面图 cover 已经存图片地址了
//        if (in_array('cover', $fields)) {
//            $loop_query[] = "UploadsLogic::BatchConvertCover({$resultVarName});";
//        }
        //如果有缓存Key值
        if(! empty($cacheKey)){
            $loop_query[] = "CommonCache::set('". $cacheKey. "', \$list);";
        }
        $loop_query[] = '$args = null;';
        $loop_query[] = 'return $list;';
        return $resultVarName;
    }

    /**
     * 详情页, 结果获取语句的封装
     *
     * @param  string  $cacheKey
     * @param $loop_query
     * @param $loopTag
     * @param $i
     * @param $fields
     * @param $global
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/29 下午11:25
     */
    public function packDetailResultLoopQuery(string & $loopHtml, string $cacheKey, & $loop_query, $loopTag, $i, $fields, $global, $tableName){
        $resultVarName = "\${$loopTag}_{$i}_result";
        $loop_query[] = '$result = $db->first();';
        //字段处理，如轮播图
        if (in_array('slide', $fields)) {
            $loop_query[] = "\$result['slide_list'] = UploadsLogic::ConvertPicAndRemark(\$result['slide'], \$result['slide_remark']);";
        }else{//nextloop中用到了, 先判断下是否存在slide_list
            if(strpos($loopHtml, 'data="slide_list"')) {
                //$loop_query[] = "if(isset($result['slide'])){";
                $loop_query[] = "\$result['slide_list'] = UploadsLogic::ConvertPicAndRemark(\$result['slide'], \$result['slide_remark']);";
                //$loop_query[] = "}";
            }
        }
        if ($global == 1) {
            //设为本页的全局变量，解析的时候用
            if(context('template_type') == 'content'){
                //内容页，不能将分类设为全局变量
                if($tableName != 'classify'){
                    $loop_query[] = "context_set('global', '\$result');";
                }
            }else{
                $loop_query[] = "context_set('global', \$result);";
            }

        }
        //如果有缓存Key值
        if(! empty($cacheKey)){
            $loop_query[] = "CommonCache::set('{$cacheKey}', \$result);";
        }
        $loop_query[] = '$args = null;';
        $loop_query[] = 'return $result;';
        return $resultVarName;
    }

    /**
     * 分页用的PHP语句
     *
     * @param $loop_query
     * @param $loopTag
     * @param $resultVarName
     * @param $i
     * @param $loop_query
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/29 下午11:20
     */
    public function packPagerResultLoopQuery(& $loop_query, $loopTag, $i, $fields){
        $resultVarName = "\${$loopTag}_{$i}_list";
        /* 封装成函数后，可以使用局部变量了
        $loop_query[] = "\${$loopTag}_{$i}_data = \$db->paginate(\$page_size, \$fields, \$page);";
        $loop_query[] = "{$resultVarName} = \${$loopTag}_{$i}_data['list'];";
        $loop_query[] = "context_set('pageInfo.total', \${$loopTag}_{$i}_data['total']);";
        $loop_query[] = "context_set('pageInfo.totalPages', \${$loopTag}_{$i}_data['totalPages']);";
        $loop_query[] = "context_set('pageInfo.pageSize', \${$loopTag}_{$i}_data['pageSize']);";
        $loop_query[] = "context_set('pageInfo.page', \${$loopTag}_{$i}_data['page']);";
*/
        $loop_query[] = '$result = $db->paginate($page_size, $fields, $page);';
        $loop_query[] = "\$list = \$result['list'];";
        $loop_query[] = "context_set('pageInfo.total', \$result['total']);";
        $loop_query[] = "context_set('pageInfo.totalPages', \$result['totalPages']);";
        $loop_query[] = "context_set('pageInfo.pageSize', \$result['pageSize']);";
        $loop_query[] = "context_set('pageInfo.page', \$result['page']);";
        $loop_query[] = '$args = null;';
        $loop_query[] = 'return $list;';
        return $resultVarName;
    }





}