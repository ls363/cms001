<?php
namespace Core;

/**
 * Class Router
 * 仿Laravel 路由解析类, 路由缓存测试完成
 *
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/21 下午6:55
 */
class Route
{
    //缓存路径
    public static $cachePath = 'route/route.php';
    public static $prefix = '';
    public static $middleware = '';
    //注册的路由
    public static $routeList = [];
    //已经编译的路由
    public static $compileRouteList = [];
    //路由片断 /product/list_{class_id}/index 中间的一段，如 list_{class_id} 解析成 pattern 与 fields
    public static $compileRoutePartList = [];

    /**
     * GET路由注册
     *
     * @param  string  $rule
     * @param  string  $controller
     * @param $action
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/22 下午10:16
     */
    public static function get(string $rule, string $controller, $action, $args = [])
    {
        self::add($rule, 'get', $controller, $action, $args);
    }

    /**
     * POST路由注册
     *
     * @param  string  $rule
     * @param  string  $controller
     * @param $action
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/22 下午10:16
     */
    public static function post(string $rule, string $controller, $action, $args = [])
    {
        self::add($rule, 'post', $controller, $action, $args);
    }

    /**
     * POST GET都可以的
     *
     * @param  string  $rule
     * @param  string  $controller
     * @param $action
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/22 下午10:12
     */
    public static function any(string $rule, string $controller, $action, $args = []){
        self::add($rule, 'any', $controller, $action, $args);
    }

    /**
     * 添加任意的路由
     *
     * @param  string  $rule
     * @param $method
     * @param  string  $controller
     * @param $action
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/22 下午10:11
     */
    public static function add(string $rule, $method, string $controller, $action, $args = [])
    {
        $item = [
            'rule'       => self::$prefix . $rule,
            'method'     => $method,
            'controller' => $controller,
            'action'     => $action,
            'middleware' => self::$middleware,
            'args'      => $args
        ];
        if (is_array($action)) {
            $tmp = $item['rule'];
            foreach ($action as $a) {
                $item['rule'] = $tmp.'/'. $a;
                $item['action'] = $a;
                self::$routeList[] = $item;
            }
        } else {
            $item['action'] = $action;
            self::$routeList[] = $item;
        }
    }

    /**
     * 分组设置 前辍 prefix、中间件 middleware
     *
     * @param $args
     * @param  \Closure  $closure
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/20 下午11:22
     */
    public static function middleware(array $middleware, \Closure $closure)
    {
        self::$prefix = '';
        self::$middleware = $middleware;
        $closure();
        //注册完，恢复前辍和中间件
        self::$prefix = '';
        self::$middleware = [];
    }


    /**
     * 分组设置 前辍 prefix、中间件 middleware
     *
     * @param $args
     * @param  \Closure  $closure
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/20 下午11:22
     */
    public static function group(array $args, \Closure $closure)
    {
        if (isset($args['prefix'])) {
            self::$prefix = $args['prefix'];
        }
        if (isset($args['middleware'])) {
            self::$middleware = $args['middleware'];
        }
        $closure();
        //注册完，恢复前辍和中间件
        self::$prefix = '';
        self::$middleware = [];
    }

    /**
     * 批量设置路由，测试的时候使用
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/21 下午2:05
     */
    public static function setList(array $routeList)
    {
        self::$routeList = $routeList;
    }

    /**
     * 返回所有已注册的路由
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/21 下午2:05
     */
    public static function getList()
    {
        return self::$routeList;
    }

    /**
     * 返回所有已编译的路由
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/21 下午2:05
     */
    public static function getCompileList()
    {
        return self::$compileRouteList;
    }

    /**
     * 返回所有已编译的路由片断
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/21 下午2:05
     */
    public static function getCompilePartList()
    {
        return self::$compileRoutePartList;
    }


    /**
     * 转换一条路由规则, 参数 {id.int} {class_id.string}
     *
     * @param  string  $routerRule
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 上午11:06
     */
    public static function convertOneRule(string $routerRule)
    {
        //所有参数都是用{}包起来的
        $pattern = '/\{(.*?)\}/';
        $routerRule = str_replace('/', '\/', $routerRule);
        $count = preg_match_all($pattern, $routerRule, $result);
        $fieldList = [];
        $routerRule = str_replace('.', '\\.', $routerRule);
        for ($i = 0; $i < $count; $i++) {
            $fieldType = '';
            $field = $result[1][$i];
            if (strpos($field, '.')) {
                $tmp = explode('.', $field);
                $field = $tmp[0];
                $fieldType = $tmp[1];
            }
            $fieldList[] = $field;
            $partPattern = '(.+)';
            switch ($fieldType) {
                case 'int':
                    $partPattern = '(\d+)';
                    break;
                case 'string':
                    $partPattern = '(\w+)';
                    break;
            }
            $routerRule = str_replace($result[0][$i], $partPattern, $routerRule);
        }
        $routerRule = '/' . $routerRule . '/';
        return ['rule' => $routerRule, 'fields' => $fieldList];
    }

    /**
     *  初始化路由
     *
     * @param string $path 路由配置路径
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 上午1:01
     */
    public static function init(string $path){
        $isRefresh = false;
        //是否强制刷新缓存
        if (config('enable_route_cache') == false) {
            $isRefresh = true;
        }
        //刷新缓存
        if($isRefresh) {
            //加载并注册路由
            require $path;
            //编译路由写入缓存
             self::compile();
        }else {
            $cachePath = RUNTIMES_PATH . '/' . self::$cachePath;
            $data = file_to_array($cachePath);
            if(empty($data)){
                //加载并注册路由
                require $path;
                //编译路由写入缓存
                self::compile();
            }else {
                //初始化路由变量
                self::initVar($data);
            }
        }
    }

    /**
     * 初始化路由变量
     *
     * @param  array  $data
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 上午1:05
     */
    public static function initVar(array $data){
        self::$compileRouteList = $data['list'];
        self::$compileRoutePartList = $data['partList'];
    }

    /**
     * 编译路由, 本算法漏考虑 本级的问题 article article/content article的数据无法存放了
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/21 下午2:13
     */
    public static function compile()
    {
        //路由缓存路径
        $cachePath = RUNTIMES_PATH . '/' . self::$cachePath;
        //路由列表
        $list = [];
        //参数片段列表
        $partList = [];
        //print_r(self::$routeList);exit;

        //指针用于动态构建数组
        foreach (self::$routeList as $r) {
            $rule = $r['rule'];
            if (substr($rule, 0, 1) == '/') {
                $rule = substr($rule, 1);
            }
            $pointer = &$list;
            $ruleArray = explode('/', $rule);
            foreach ($ruleArray as $k => $v) {
                //分段缓存，如某一段有变量
                if (strpos($v, '}')) {
                    //如果未编译
                    if (!isset($partList[$v])) {
                        $partList[$v] = self::convertOneRule($v);
                    }
                }
                if ($k == 0) {
                    //转成树型结构
                    if (!isset($pointer[$v])) {
                        $pointer[$v] = ['method' => '', 'middleware' => [], 'module' => '', 'controller'=>'', 'controller_name' => '', 'action' => '', 'args' => [], 'children' => []];
                    }
                    $pointer = &$pointer[$v];
                } else {
                    //转成树型结构
                    //print_r($pointer);
                    if (!isset($pointer['children'][$v])) {
                        $pointer['children'][$v] = ['method' => '', 'middleware' => [], 'module' => '', 'controller_name' => '', 'controller'=>'' , 'action' => '',  'args' => [], 'children' => []];
                    }
                    $pointer = &$pointer['children'][$v];
                }
            }

            //提取控制器信息
            $controllerInfo = self::parseController($r['controller']);
            //小写+下划线的的模块名称
            $pointer['module'] = $controllerInfo['module'];
            //小写+下划线的控制器名称
            $pointer['controller_name'] = $controllerInfo['controller'];
            $pointer['controller'] = $r['controller'];
            $pointer['action'] = $r['action'];
            $pointer['method'] = $r['method'];
            $pointer['middleware'] = $r['middleware'];
            $pointer['args'] = $r['args'];
        }
        $data = [
            'list'     => $list,
            'partList' => $partList
        ];
        //print_r($data);exit;
        //初始化路由变量
        self::initVar($data);
        //写到文件
        array_to_file($data, $cachePath);
        $data = null;
    }

    public static function parseController(string $namespace){
        //处理控制器与模块小写的名称
        $controller = str_replace(config('controller_namespace'), '', $namespace);
        $pos = strpos($controller, '\\');
        $moduleName = substr($controller, 0, $pos);
        $controllerName = str_replace('Controller', '', substr($controller,  $pos+1));
        //将模块及控制器转成下划线
        $moduleName = convertHump($moduleName);
        $controllerName = convertHump($controllerName);
        return ['module' => $moduleName, 'controller' => $controllerName];
    }


    /**
     * 解析路由参数, 未匹配到，返回false
     *
     * @param  string  $rulePart
     * @param  string  $urlPart
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/21 下午2:46
     */
    public static function parseRouterArgs(string $rulePart, string $urlPart)
    {
        //获取已编译的路由片断
        $ruleData = self::$compileRoutePartList[$rulePart];
        $pattern = $ruleData['rule'];
        //路由参数字段
        $fields = $ruleData['fields'];
        //没有参数，返回空数组
        if(empty($fields)){
            return [];
        }

        //使用路由规则，匹配URL, 提取出URL中对应的参数值
        $count = preg_match_all($pattern, $urlPart, $matchResult);
        if ($count == 0) {
            return [];
        }
        //$ruleArgs = [];
        //print_r($matchResult);
        foreach ($matchResult as $k => $v) {
            if ($k == 0) {//第一个是完整的字符串
                continue;
            }
            $ruleArgs[$fields[$k - 1]] = $v[0];
        }
        return $ruleArgs;
    }

    /**
     * 查找URL段的参数, 找到返回命中的规则及参数
     *
     * @param  array  $ruleList
     * @param  string  $urlPart
     * @return array
     * @throws \Exception
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/21 下午6:40
     */
    public static function matchUrlPart(array $ruleList, string $urlPart)
    {
        if (empty($ruleList)) {
            throw new \Exception('路由规则为空,' . 'URL段：' . $urlPart);
        }
        //匹配全部带参数的路由, ruleContent暂时不需要, 临时路由变量$r
        foreach ($ruleList as $rule => $ruleContent) {
            //含有参数，需要匹配
            if (strpos($rule, '}')) {
                //查找参数
                $result = self::parseRouterArgs($rule, $urlPart);
                if (!empty($result)) {
                    return ['rule' => $rule, 'args' => $result];
                }
            }
        }
        //找不到返回空
        throw new \Exception('没有符合条件的路由,' . 'URL段：' . $urlPart);
    }

    /**
     * 解析整个URL对应的参数、控制器、方法
     *
     * @param  string  $url
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/21 下午2:47
     */
    public static function matchUrl(string $url)
    {
        //完整的路由树
        $ruleList = self::$compileRouteList;
        //Url分段
        $urlArray = explode('/', $url);
        $currentRule = []; //每一段的URL路由规则
        $ruleArgs = []; //路由中的参数{param}提取后的结果
        //禁用admin的路由，防止攻击
        if($urlArray[0] == 'admin'){
            throw new \Exception('该路由已被禁用');
        }
        //如果是后台目录, 进行路由转换，防止后台目录暴露
        if($urlArray[0] == config('admin_dir')){
            $urlArray[0] = 'admin';
        }
        try {
            foreach ($urlArray as $k => $urlPart) {
                //URL中的第一段路径
                if ($k == 0) {
                    //没有匹配到，说明，路由规则中可能有参数
                    if (isset($ruleList[$urlPart])) {
                        $currentRule = $ruleList[$urlPart];
                        continue;
                    }
                    //匹配路由规则，并映射参数, 找不到则有异常抛出
                    $matchResult = self::matchUrlPart($ruleList, $urlPart);
                    //符合条件的路由
                    $matchRule = $matchResult['rule'];
                    //切到子路由，因为路由已经按URL规则，形成树型结构
                    $currentRule = $ruleList[$matchRule];
                    //URL中的参数
                    $ruleArgs = array_merge($ruleArgs, $matchResult['args']);
                } else {//第二个之后的路由，因为第一级没有children
                    if (isset($currentRule['children'][$urlPart])) {
                        $currentRule = $currentRule['children'][$urlPart];
                        continue;
                    }
                    //匹配路由规则，并映射参数，找不到则有异常抛出
                    $matchResult = self::matchUrlPart($currentRule['children'], $urlPart);
                    //符合条件的路由
                    $matchRule = $matchResult['rule'];
                    //切到子路由，因为路由已经按URL规则，形成树型结构
                    $currentRule = $currentRule['children'][$matchRule];
                    //URL中的参数
                    $ruleArgs = array_merge($ruleArgs, $matchResult['args']);
                }
            }
            //print_r($currentRule['args']);exit;
            //合并路由中自带的参数
            if(isset($currentRule['args']) && ! empty($currentRule['args'])) {
                $ruleArgs = array_merge($ruleArgs, $currentRule['args']);
            }
            //保存上下文，用于url()
            context_set('app_current_module', $currentRule['module']);
            context_set('app_current_controller', $currentRule['controller_name']);
            context_set('app_current_action', $currentRule['action']);
            context_set('app_current_route_args', $ruleArgs);
            if(! empty($ruleArgs)){
                foreach ($ruleArgs as $k=>$v){
                    context_set($k, $v);
                }
            }
            return [
                'args'       => $ruleArgs,
                'module' => $currentRule['module'],
                'controller_name' => $currentRule['controller_name'],
                'controller' => $currentRule['controller'],
                'action' => $currentRule['action'],
                'middleware' => $currentRule['middleware'] ?? '',
                'method' => $currentRule['method']
            ];
        } catch (\Exception $e) {//未找到路由
            return [];
        }
    }

}