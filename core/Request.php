<?php

namespace Core;

class Request
{

    public static $_instance = null;
    private $headersData = null;

    /**
     * 获取单例
     *
     * @return null
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/23 下午11:18
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
            $headers = self::$_instance->headers();
            //[content-type] => application/json;charset=UTF-8
            if(strpos($headers['content-type'], 'application/json') !== false){
                $object =file_get_contents("php://input");
                $args = json_decode($object, true);
                if (!empty($args)) {
                    foreach ($args as $k => $v) {
                        self::$_instance->{$k} = $v;
                    }
                }
            }else {
                if (!empty($_REQUEST)) {
                    foreach ($_REQUEST as $k => $v) {
                        self::$_instance->{$k} = $v;
                    }
                }
            }
        }
        //注入路由参数
        $routeArgs = context('app_current_route_args');
        if(! empty($routeArgs)){
            foreach ($routeArgs as $k=>$v){
                self::$_instance->{$k} = $v;
            }
        }
        return self::$_instance;
    }

    /**
     * 获取当前页地址
     *
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/14 下午2:04
     */
    public function currentUrl(){
        return $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * 获取token
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/1 下午1:43
     */
    public function getToken(){
        //token也可以url中传入
        if(isset($this->token)){
            return $this->token;
        }
        $headers = $this->headers();
        return $headers['authorization'] ?? '';
    }

    /**
     * 获取整个服务器信息
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午4:04
     */
    public function server(){
        return $_SERVER;
    }

    /**
     * 通过请求头，判断是否为AJAX [x-requested-with: XMLHttpRequest]
     * @author lichunguang
     * @since 2024/10/27 17:59
     * @return void
     */
    public function ajax(){
        $headers = $this->headers();
        return $headers['ajax'];
    }

    public function referer(){
        return $_SERVER['HTTP_REFERER'] ?? '';
    }

    /**
     * 获取请求地址
     *
     * @return mixed|string
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/23 下午3:57
     */
    public function uri()
    {
        $uri = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '';
        $iPos = strpos($uri, '?');
        if($iPos){
            return substr($uri, 0, $iPos);
        }
        return $uri;
    }

    /**
     * 获取url?后面的参数
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/20 下午1:29
     */
    public function query()
    {
        return $_SERVER['QUERY_STRING'];
    }

    /**
     * 获取url?后面的参数
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/20 下午1:29
     */
    public function query_string()
    {
        return $_SERVER['QUERY_STRING'];
    }

    /**
     * 获取请求方式
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/23 下午3:28
     */
    public function method()
    {
        return isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : '';
    }

    /**
     * 获取全部参数
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/23 下午3:45
     */
    public function all()
    {
        $data = $this->toArray();
        unset($data['headerData']);
        return empty($data) ? [] :$data;
    }

    /**
     * 获取请求头信息
     *
     * @return array|false
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/1 上午7:49
     */
    public function headers(){
        if(is_null($this->headersData)){
            $data = getallheaders();
            if(empty($data)){
                return [];
            }
            $list = ['ajax' => false];
            foreach($data as $k=>$v){
                if($k == 'XMLHttpRequest'){
                    $list['ajax'] = true;
                }
                $list[strtolower($k)] = $v;
            }
            $this->headersData = $list;
            $data = null;
            return $list;
        }else{
            return $this->headersData;
        }
    }

    /**
     * 参数接收
     *
     * @param  string  $name
     * @param  string  $default
     * @param  string  $type
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午4:30
     */
    public function input(string $name, $default='', $function=''){
        $value = $this->{$name} ?? $default;
        //可以做取整操作
        if($default === 0){
            if(empty($function)) {
                return intval($value);
            }else{
                return $function($value);
            }
        }
        if(! empty($value) && ! empty($function)){
            return $function($value);
        }
        return $value;
    }

    /**
     * 获取全部的GET参数
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/23 下午3:46
     */
    public function get($key = '', $default='')
    {
        return $this->input($key, $default);
    }

    /**
     * 获取全部的POST参数
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/23 下午3:46
     */
    public function post($key = '', $default='')
    {
        return $this->input($key, $default);
    }

    public function getInt(string $key, int $default = 0)
    {
        return $this->input($key, $default);
    }

    public function postInt(string $key, int $default = 0)
    {
        return $this->input($key, $default);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

}