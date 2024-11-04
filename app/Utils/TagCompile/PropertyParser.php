<?php
namespace App\Utils\TagCompile;

use App\Enums\TagTableEnums;

/**
 * Class PropertyParse
 * Loop属性解析
 *
 * @package App\Libraries\HtmlMaker
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/29 下午11:44
 */
class PropertyParser{

    private static $tableFields = ['col', 'table_width', 'td_align', 'table_class'];
    private static $pageFields = ['page', 'is_page', 'page_size', 'record_num', 'is_page_bar'];
    private static $searchFields = ['tag_name', 'tag_id', 'keywords', 'search_type', 'search_text'];

    /**
     * 获取loop标签中表的名称
     *
     * @param  string  $modelName
     * @param string $loopHtml 标签html 记录日志用
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/1 下午9:52
     */
    public function parsePropertyTableName(&$property_array, $loopHtml)
    {
        //有几种情况，没有table信息
        $loop_type = $property_array['loop_type'] ?? '';
        if(in_array($loop_type, ['data', 'extend_field', 'sub_menu'] )){
            return  "";
        }

        //支持传入表名的方式, 获取表名，并释放标签中的变量
        if (isset($property_array['table_name'])) {
            $tableName = trim($property_array['table_name']);
            unset($property_array['table_name']);
        } else {//将name映射成表
            if(isset($property_array['name'])){
                $modelName = $property_array['name'];
                $modelName = convertHump($modelName);   //转成小写+下划线
                $tableName = isset(TagTableEnums::$tagTable[$modelName]) ? TagTableEnums::$tagTable[$modelName] : '';
                unset($property_array['name']);
            }
        }
        if (empty($tableName)) {
            log_error('compile_template', $loopHtml);
            throw new \Exception('标签错误, 缺少name或table_name属性！');
        }
        return $tableName;
    }

    /**
     * 解析标签{tag}参数
     *
     * @param $property_array
     * @param $loop_var
     * @param $loop_query
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/29 下午5:52
     */
    public function parseTag(& $extendInfo, &$property_array, &$loop_var, &$loop_query)
    {
        //如果没有tag也不处理
        if (isset($property_array['tag']) == false) {
            return;
        }
        //取值并移除两个值
        $tag = $property_array['tag'];
        unset($property_array['tag']);
        //取第一个字母，根据第一个字母判断，是变量还是，固定的值，还是loop返回的结果
        $firstLetter = substr($tag, 0, 1);
        switch ($firstLetter) {
            case '{'://$loop_result
                $tag = str_replace(['{$', '$}', '}'], '', $tag);
                $tag = "\$loop_result['{$tag}']";
                break;
            case ':'://$args
                $tag = substr($tag, 1);
                $tag = "\$args['{$tag}']";
                break;
        }
        $loop_var[] = "\$tag = {$tag};";
        $loop_query[] = "\$db->where('tags', 'like', \"%{" . $tag . "}%\");";
    }

    /**
     * 解析分类ID
     *
     * @param $property_array
     * @param $loop_var
     * @param $loop_query
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/29 下午5:52
     */
    public function parseChildClassId(& $extendInfo, &$property_array, &$loop_var, &$loop_query)
    {
        //没有include_child不用处理
        if (isset($property_array['include_child']) == false) {
            return;
        }
        //如果没有class_id也不处理
        if (isset($property_array['class_id']) == false) {
            return;
        }
        $field_prefix = '';
        if($extendInfo['need_convert']){
            $field_prefix = 'm.';
        }
        //取值并移除两个值
        $class_id = $property_array['class_id'];
        unset($property_array['class_id']);
        unset($property_array['include_child']);
        //取第一个字母，根据第一个字母判断，是变量还是，固定的值，还是loop返回的结果
        $firstLetter = substr($class_id, 0, 1);
        switch ($firstLetter) {
            case '{'://$loop_result
                $class_id = str_replace(['{$', '$}', '}'], '', $class_id);
                $class_id = "\$loop_result['{$class_id}']";
                break;
            case ':'://$args
                $class_id = substr($class_id, 1);
                $class_id = "\$args['{$class_id}']";
                break;
        }
        //获取所有子分类
        $loop_var[] = "\$class_id = {$class_id};";
        //class_id只有一个表（classify）
        $loop_var[] = '$childIds = ClassifyLogic::getAllChildClassId($class_id); ' . PHP_EOL;
        $loop_query[] = 'if(! empty($childIds)){';
        $loop_query[] = '$db->whereIn(\''. $field_prefix .'class_id\', $childIds);';
        $loop_query[] = '}';
        return;
    }


    /**
     * 解析beforeDay标签
     *
     * @param $property_array
     * @param $loop_var
     * @param $loop_query
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/29 下午10:12
     */
    public function parsePropertyBeforeDay(&$property_array, &$loop_var, &$loop_query)
    {
        if (isset($property_array['before_day']) == false) {
            return;
        }
        $loop_var[] = "\$before_day = " . intval($property_array['before_day']) . ";";
        $loop_query[] = "\$db->whereBetween('created_at', [date('Y-m-d H:i:s', time() - \$before_day * 86400), date('Y-m-d H:i:s')]];";
        unset($property_array['before_day']);
    }

    /**
     * 解析WHERE参数
     * @param $field_prefix 字段前辍 m. 主表 e. 扩展表
     * @param $field
     * @param $value
     * @param $loop_var
     * @param $loop_query
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/29 下午10:21
     */
    public function parsePropertyWhere($field_prefix, $field, $value, &$loop_var, &$loop_query)
    {
        //需要处理父标签返回的查询结果.
        if (strpos($value, '$')) {
            $tmpField = str_replace(['{$', '$}', '{','}'], '', $value);
            //$tmpField = substr($value, 1);
            $loop_query[] = "\$db->where('{$field_prefix}{$field}', '=', \$loop_result['{$tmpField}']);";
            return;
        }
        //外部参数，这里只用"="查询
        if (strpos($value, ':') !== false) {
            $tmpField = str_replace(':', '', $value);
            $loop_query[] = "if(array_key_exists('{$tmpField}', \$args)){";
            //这几个参数必须转整
            if(in_array($tmpField, ['id', 'class_id'])){
                $loop_query[] = "\$db->where('{$field_prefix}{$field}', intval(\$args['" . $tmpField . "']));";
            }else{
                $loop_query[] = "\$db->where('{$field_prefix}{$field}', remove_xss(\$args['" . $tmpField . "']));";
            }

            $loop_query[] = "}";
            return;
        }
        //逗号分隔的ID
        if (strpos($value, ',')) {
            //拆分成数组，whereIn查询
            $tmp = explode(',', $value);
            $loop_var[] = "\${$field}_arr = " . var_export($tmp, true) . ";";
            $loop_query[] = "\$db->whereIn('{$field_prefix}{$field}', \${$field}_arr);";
            return;
        }
        if (is_numeric($value)) {
            $loop_query[] = "\$db->where('{$field_prefix}{$field}', $value);";
            return ;
        } else {
            $loop_query[] = "\$db->where('{$field_prefix}{$field}', '{$value}');";
            return ;
        }
    }


    /**
     * 解析搜索参数 搜索是模糊的，需要用到like所以单独处理
     *
     * @param $searchArr
     * @param $loop_var
     * @param $loop_query
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/29 下午10:47
     */
    public function parseSearchArgs(&$searchArr, &$loop_var, &$loop_query)
    {
        if (!empty($searchArr)) {
            if (array_key_exists('search_type', $searchArr) && array_key_exists('search_text', $searchArr)) {
                $searchText = $searchArr['search_text'];
                if (strpos($searchText, ':') === false) {//参数是定死的，不需要处理
                } else {//参数是外部传入的, 防止SQL注入
                    $loop_var[] = "\$searchText = remove_xss(\$args['" . str_replace(':', '', $searchText) . "'] ?? '');";
                }
                $loop_var[] = "\$searchType = \$args['search_type'];";
                $loop_query[] = "if(! empty(\$searchType) && ! empty(\$searchText)){";
                $loop_query[] = "\$db->where(\$searchType, 'like', \"%{\$searchText}%\");";
                $loop_query[] = "}";
            }
            //根据关键字搜索
            if (array_key_exists('keywords', $searchArr)) {
                $searchText = $searchArr['keywords'];
                if (strpos($searchText, ':') !== false) {//参数外部传入要防止SQL注入
                    $loop_var[] = "\$searchText = remove_xss(\$args['" . str_replace(':', '', $searchText) . "'] ?? '');";
                }
                $loop_query[] = "if(array_key_exists('keywords', \$args)){";
                $loop_query[] = "\$db->where((\"title like '%{\$searchText}%' OR intro like '%{\$searchText}%'\"));";
                $loop_query[] = "}";
            }
        }
    }


    /**
     * 解析HTML表格参数
     *
     * @param $property_array
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/29 下午10:57
     */
    public function parseHtmlTable(& $property_array){
        //处理表格参数
        $htmlTable = [];
        foreach(self::$tableFields as $key){
            if(isset($property_array[$key])){
                $htmlTable[$key] = $property_array[$key];
                unset($property_array);
            }
        }
        return $htmlTable;
    }

    /**
     * 获取搜索参数
     *
     * @param $property_array
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/29 下午10:57
     */
    public function getSearchArgs(& $property_array){
        $args = [];
        foreach(self::$searchFields as $key){
            if(isset($property_array[$key])){
                $args[$key] = $property_array[$key];
                unset($property_array[$key]);
            }
        }
        return $args;
    }

    /**
     * 获取分页参数
     *
     * @param $property_array
     * @param $loop_var
     * @param $loop_query
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/29 下午11:14
     */
    public function getPagerArgs(& $property_array, &$loop_var){
        $pageArr = [];
        foreach(self::$pageFields as $field){
            if(isset($property_array[$field])){
                $value = $property_array[$field];
                $pageArr[$field] = $value;
                unset($property_array[$field]);
                if (strpos($value, ':') !== false) {
                    $tmpField = str_replace(':', '', $value);
                    if($tmpField == 'page'){//分页默认是第一页
                        $loop_var[] = "\${$field} = \$args['page'] ?? 1;";
                    }else{
                        $loop_var[] = "\${$field} = \$args['" . $tmpField . "'];";
                    }
                } else {
                    $loop_var[] = "\${$field} = $value;";
                }
            }
        }
        return $pageArr;
    }

    /**
     * 解析排序方式
     *
     * @param  array  $property_array
     * @return array|false|string[]
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/2 上午11:11
     */
    public function parsePropertyOrderBy(array &$property_array)
    {
        if (isset($property_array['order_by'])) {
            $ary = explode(',', $property_array['order_by']);
            foreach ($ary as $v) {
                $tmp = explode('|', $v);
                $orderBy[] = [$tmp[0], isset($tmp[1]) ? $tmp[1] : 'desc'];
            }
            unset($property_array['order_by']);
            return $orderBy;
        }
        return [];
    }

    /**
     * 处理loop_type属性, 每个loop标签内只可能有一个loop_type
     *
     * @param  array  $where
     * @param  array  $property_array
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/2 上午11:09
     */
    public function parsePropertyLoopType(
        string $tableName,
        string $loopType,
        array &$property_array,
        array &$loop_var,
        array &$loop_query
    ) {
        //循环的特殊类型，处理上一条 下一条记录的这种
        //没有loop_type直接返回
        if (empty($loopType)) {
            return;
        }
        if (isset($property_array['id'])) {
            $id = $property_array['id'];
            //动态参数
            if (substr($id, 0, 1) == ':') {
                $id = str_replace(':', '', $id);
                $loop_var[] = "\$id=\$args['{$id}'];";
            } else {
                $loop_var[] = "\$id={$id};";
            }
            unset($property_array['id']);
        }
        //处理上一篇文章与下一篇文章
        switch ($loopType) {
            case "previous":
                $loop_query[] = '$db->where(\'class_id\',  $args[\'class_id\']);';
                $loop_query[] = '$db->where(\'id\', \'<\',  $id);';
                break;
            case "next":
                $loop_query[] = '$db->where(\'class_id\',  $args[\'class_id\']);';
                $loop_query[] = '$db->where(\'id\', \'>\',  $id);';
                break;
            case "position": //处理你的位置
                //解析的时候，动态调用函数
                $loop_var[] = '\$positionIds = getParentPath($tableName, $id);';
                $loop_query[] = '$db->whereIn(\'id\', positionIds)';
                break;
            case "related": //相关文章，应该是拿来选择的，在编辑文章的时候配置
                $loop_var[] = '\$relatedIds = getRelatedInfo($tableName, $id);';
                $loop_query[] = '$db->whereIn(\'id\',  $relatedIds);';
                break;
            case "side_menu"://侧边栏的分类获取
//                $loop_var[] = '$classifyInfo = ClassifyLogic::getByIdCache($id);';
                $loop_var[] = '$classify = context(\'classify\');';
                $loop_var[] = 'if($classify["parent_id"] == 0){';
                $loop_var[] = 'context_set(\'side_main\',$classify);';
                $loop_var[] = '}else{';
                $loop_var[] = '$classifyInfo = ClassifyLogic::getByIdCache($classify[\'parent_id\']);';
                $loop_var[] = 'context_set(\'side_main\',$classifyInfo);';
                $loop_var[] = '}';
                $loop_query[] = 'if($classify["parent_id"] == 0){';
                $loop_query[] = '$db->where(\'parent_id\',  $classify[\'id\']);';
                $loop_query[] = '}else{';
                $loop_query[] = '$db->where(\'parent_id\',  $classify[\'parent_id\']);';
                $loop_query[] = '}';
            default:
        }

    }

}
