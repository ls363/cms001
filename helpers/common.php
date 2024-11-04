<?php

use Core\Env;
use Core\ConfigCls;
use Core\Context;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Logics\UploadsLogic;
use App\Logics\ClassifyLogic;
use App\Logics\SystemLogic;
use App\Logics\ContentModuleExtendLogic;
use App\Logics\ContentModuleLogic;


/**
 * 获取环境变量
 *
 * @param  string  $name
 * @param  string  $default
 * @return array|mixed
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/24 上午11:43
 */
function env(string $name, $default = '')
{
    return Env::get($name, $default);
}

/**
 * 配置获取的助手函数
 *
 * @param  string  $name
 * @param  string  $default
 * @return array|mixed|string
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/25 上午10:06
 */
function config(string $name, $default = '')
{
    return ConfigCls::getInstance()->get($name, $default);
}

/**
 * 获取全部配置
 *
 * @return array
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/25 上午10:06
 */
function config_all()
{
    return ConfigCls::getInstance()->all();
}

/**
 * 设置缓存
 *
 * @param  string  $name
 * @param  string  $default
 * @return array|mixed|string
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/26 下午3:31
 */
function config_set(string $name, $value = '')
{
    return ConfigCls::getInstance()->set($name, $value);
}

/**
 * 快速设置上下文
 *
 * @param  string  $name
 * @param  string  $value
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/23 上午10:02
 */
function context_set(string $name, $value = '')
{
    return Context::set($name, $value);
}

/**
 * 快速设置上下文
 *
 * @param  string  $name
 * @param  string  $default
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/23 上午10:02
 */
function context(string $name='', $default = '')
{
    if(empty($name)){
        return Context::all();
    }
    return Context::get($name, $default);
}

/**
 * 返回当前时间
 *
 * @return false|string
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/22 下午12:56
 */
function getCurrent()
{
    return date('Y-m-d H:i:s');
}

/**
 * 字符串截取
 *
 * @param $str
 * @param $len
 * @return string
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/22 下午6:14
 */
function cutString($str, $len)
{
    return utf8_str($str, 0, $len);
}

/**
 * PHP截取utf-8中文字符串
 * @param  string  $str  被截取的字符串
 * @param  int  $start  起始长度
 * @param  int  $len  截取长度
 * @param  string  $suffix  后缀字符串
 */
function utf8_str(string $str, int $start, int $len, string $suffix = "")
{
    $tmpstr = "";
    $n = 0;
    $i = 0;
    $length = $start + $len;
    while ($i < strlen($str)) {
        $value = ord($str[$i]);
        if ($value >= 65 && $value <= 90) {//大写字母
            if ($n >= $start && $n < $length) {
                $tmpstr .= substr($str, $i, 1);
            }
            $n++;
            $i++;
        } elseif ($value >= 192 && $value <= 223) {
            if ($n >= $start && $n < $length) {
                $tmpstr .= substr($str, $i, 2);
            }
            $n++;
            $i += 2;
        } elseif ($value >= 224 && $value <= 239) {
            if ($n >= $start && $n < $length) {
                $tmpstr .= substr($str, $i, 3);
            }
            $n++;
            $i += 3;
        } elseif ($value >= 240 && $value <= 247) {
            if ($n >= $start && $n < $length) {
                $tmpstr .= substr($str, $i, 4);
            }
            $n++;
            $i += 4;
        } else {//其他情况下，包括小写字母和半角标点符号
            if ($n >= $start && $n < $length) {
                $tmpstr .= substr($str, $i, 1);
            }
            $n += 0.5;
            $i++;
        }
    }
    if ($n < $len) {
        return $tmpstr;
    } else {
        return $tmpstr . $suffix;
    }
}

/**
 * 将驼峰转成下划线
 *
 * @param  string  $str
 * @return string
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/24 上午11:41
 */
function convertHump(string $str)
{
    return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $str));
}

/**
 * 将下划线转驼峰
 *
 * @param  string  $str
 * @param  bool  $big  默认小驼峰
 * @return string|string[]|null
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/24 上午11:51
 */
function convertUnderline(string $str, bool $big = false)
{
    $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches) {
        return strtoupper($matches[2]);
    }, $str);
    return $big ? ucfirst($str) : $str;
}

/**
 *
 *
 * @param $url
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/24 下午12:10
 */
function redirect($url)
{
    //重定向浏览器
    header("Location: {$url}");
    //确保重定向后，后续代码不会被执行
    exit;
}

/**
 * JS页面跳转
 *
 * @param  string  $url
 * @param  string  $message
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/24 下午2:44
 */
function jump(string $url, string $message = '')
{
    $url = "http://www.guanwei.org";
    echo "<script language='javascript' type='text/javascript'>";
    if (!empty($message)) {
        echo "alert('{$message}');";
    }
    echo "window.location.href='$url'";
    echo "</script>";
    exit;
}

/**
 * JWT加密
 * @param $data
 * @param  null  $valid_period
 * @return string
 */
function jwt_encode($data, $valid_period = 86400)
{
    if (empty($valid_period)) {
        $valid_period = env("JWT_TTL");
    }
    $key = env("JWT_SECRET");
    $payload = array(
        "iat" => time(),
        "exp" => time() + intval($valid_period),
    );
    $payload = array_merge($payload, $data);
    $token = JWT::encode($payload, $key, env("JWT_ALG"));
    return $token;
}

/**
 * JWT加解密
 * @param $token
 * @return object
 */
function jwt_decode($token)
{
    $key = env("JWT_SECRET");
    $payload = JWT::decode($token, new Key($key, env("JWT_ALG")));
    return $payload;
}

//解析所有的token, 包含过期的
function jwt_decode_all($token)
{
    $tks = \explode('.', $token);
    if (\count($tks) != 3) {
        return false;
    }
    list($headb64, $bodyb64, $cryptob64) = $tks;
    if (null === ($header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64)))) {
        return false;
    }
    if (null === $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64))) {
        return false;
    }
    return $payload;
}

/**
 * 获取随机数
 * @return int
 */
function getRandom(int $length = 6)
{
    return rand(pow(10, ($length - 1)), pow(10, $length) - 1);
}

/**
 * 生成指定位数的随机字符串
 *
 * @param  int  $num  位数
 * @param  bool  $isLower 英文是否小写，默认小写
 * @return string
 * @author lichunguang 153102250@qq.com
 * @since 2022/10/8 下午1:30
 */
function getRandomString(int $num, bool $isLower=true){
    $newFileName = '';
    $chars = "1234567890qwertyuiopasdfghjklzxcvbnm";//随机生成图片名
    if($isLower === false){
        $chars .= 'QWERTYUIOPASDFGHJKLZXCVBNM';
    }
    $totalLen = strlen($chars);
    for ($i = 0; $i < $num; $i++) {
        $newFileName .= $chars[mt_rand(0, $totalLen - 1)];
    }
    return $newFileName;
}

function front_url($url){
    if (config('route.url_rewrite')) {
        $url = '/' . $url;
    } else {
        $url = config('route.entry_url') . '?/' . $url;
    }
    return $url;
}


/**
 * 视图层中，用于转换访问路径的函数
 *
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/26 上午7:48
 */
function url(string $path = '', array $args = [])
{
    $module = convertHump(context('app_current_module'));
    //对管理后台目录进行隐藏
    if($module == 'admin'){
        $module = config('admin_dir');
    }
    if (strpos($path, '/') === false) {
        $path =  $module . '/' . convertHump(context('app_current_controller')) . '/' . $path;
    } else {
        $ary = explode('/', $path);
        //对管理后台路径处理
        if($ary[0] == 'admin'){
            $ary[0] = config('admin_dir');
            $path = implode('/', $ary);
        }
        if (count($ary) == 2) {
            $path = $module . '/' . $path;
        }
    }
    if (config('route.url_rewrite')) {
        $url = '/' . $path;
    } else {
        $url = config('route.entry_url') . '?/' . $path;
    }
    $url = str_replace('//', '/', $url);
    if (!empty($args)) {
        if(strpos($url, '?') !== false) {
            $url .= '&' . http_build_query($args);
        }else{
            $url .= '?' . http_build_query($args);
        }
    }
    return $url;
}

/**
 * 字符串的implode, 每个字符加 ''
 *
 * @param  string  $split
 * @param $list
 * @return string
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/26 上午9:13
 */
function stringImplode(string $split, $list)
{
    $str = '';
    foreach ($list as $v) {
        if ($str != '') {
            $str .= $split;
        }
        $str .= "'" . addslashes($v) . "'";
    }
    return $str;
}


/**
 * 记录异常Log
 *
 * @param  Exception  $e
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/23 上午10:49
 */
function log_exception(\Exception $e){
    $log_content = $e->getMessage() .','. $e->getFile() .','. $e->getLine();
    log_error('exception', $log_content);
}

/**
 * 记录错误日志
 *
 * @param  string  $logName
 * @param $logContent
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/26 上午11:34
 */
function log_error(string $logName='log', $logContent)
{
    //return false;
    $date = date('Ymd');
    $folder = RUNTIMES_PATH . '/logs/' . $date;
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }
    $lopPath = $folder . '/' . $logName . '.txt';

    if (is_array($logContent)) {
        //保存日志，中文不编码
        $str = json_encode($logContent, JSON_UNESCAPED_UNICODE);
    } else {
        $str = $logContent;
    }
    $str = date('Y-m-d H:i:s') . ' ' . $str . PHP_EOL;
    file_put_contents($lopPath, $str, FILE_APPEND);
}

/**
 * POST请求用的csrf_token，通过Token校验
 *
 * @param  bool  $html
 * @return string
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/27 上午8:55
 */
function csrf_token($html = true)
{
    $randNum = md5(getRandom(6));
    \Core\WebSession::getInstance()->set(config('csrf_token'), $randNum);
    if (!$html) {
        return $randNum;
    }
    $html = '<input type="hidden" name="_token" value="' . $randNum . '">';
    return $html;
}

/**
 * 检查csrf_token
 *
 * @param  string  $csrf_token
 * @return bool
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/27 上午8:58
 */
function check_csrf_token(string $csrf_token)
{
    return \Core\WebSession::getInstance()->get(config('csrf_token')) == $csrf_token ? true : false;
}

function request()
{
    return \Core\Request::getInstance();
}

function input(string $name='', $default = '', $function = '')
{
    if(empty($name)){
        return \Core\Request::getInstance()->all();
    }
    return \Core\Request::getInstance()->input($name, $default, $function);
}

function I(string $name, $default = '', $function = '')
{
    if(strpos($name, '.')) {
        $name = str_replace(['get.','post.'], '', $name);
    }
    return \Core\Request::getInstance()->input($name, $default, $function);
}

/**
 * 自定义错误处理函数
 *
 * @param  int  $errNo
 * @param  string  $errStr
 * @param $errfile
 * @param $errline
 * @return bool
 * @throws Exception
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/29 下午12:56
 */
function myErrorHandler(int $errNo, string $errStr, $errfile, $errline)
{
    switch ($errNo) {
        case E_NOTICE:
            $message = "<b>My NOTICE</b> [$errNo] $errStr<br />\n";
            break;
        case E_ERROR:
            $message = "<b>My ERROR</b> [$errNo] $errStr<br />\n";
            break;
        default:
            $message = "Unknown error type: [$errNo] $errStr<br />\n";
            break;
    }
    //echo $message;
    throw new \Exception($errStr, $errNo);
    return true;
}

/**
 * 加密密码
 *
 * @param  string  $password
 * @param  string  $salt
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/29 下午4:03
 */
function encodePassword(string $password, string $salt = '')
{
    return md5($password);
}

/**
 * AJAX标签
 *
 * @return string
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/29 下午8:45
 */
function ajax_hidden()
{
    return '<input name="_ajax" type="hidden" value="1">';
}

/**
 * 返回public目录
 *
 * @return string
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/29 下午8:45
 */
function public_path()
{
    return PUBLIC_PATH;
}

/**
 * 获取用户ID
 *
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/30 上午11:22
 */
function get_admin_user_id()
{
    return Context::get('adminId');
}

/**
 *
 * 获取用户ID 管理员
 *
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/30 上午11:22
 */
function get_user_id()
{
    return \Core\Route::getInstance()->getLoginId();
}

/**
 *
 * 获取会员用户ID
 *
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/30 上午11:22
 */
function get_member_id()
{
    return \Core\Context::get('memberId');
}

function get_real_ip()
{
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } else {
            if (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
    }

    return $realip;

}

function echo_exception($e){
    $str = $e->getFile() .','. $e->getLine().','. $e->getMessage();
    print_r($str);
    exit;
}

/**
 * 成功的返回
 *
 * @param  int  $code
 * @param  array  $data
 * @param  string  $message
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/24 下午12:21
 */
function api_success(array $data = [], $message='success', $code=200){
    $json = [
        'code' => $code,
        'message' => $message == 'success' ? '操作成功' : $message,
        'data' => $data
    ];
    echo json_encode($json);
}

/**
 * 失败的返回
 *
 * @param  string  $message
 * @param  int  $code
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/24 下午12:21
 */
function api_error($message='failed', $code=500){
    $json = [
        'code' => $code,
        'message' => $message == 'failed' ? '操作失败' : $message,
    ];
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}

/**
 * 获取包含文件内容，用于静态页面生成
 *
 * @param  string  $filename
 * @return false|string
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/15 下午4:11
 */
function get_include_contents(string $filename, $data = []) {
    if (is_file($filename)) {
        if(! empty($data)){
            extract($data);
        }
        ob_start();
        include $filename;
        $contents = ob_get_contents();
        ob_end_clean();
        //先不压缩，这个会影响html代码中部分JS的执行
        //$contents = str_replace(["\r\n", "\n", "\r"], "", $contents);
        return $contents;
    }
    return '';
}


/**
 * 将数组写入到文件，可以通过return return require_once $file; 引入
 *
 * @param  array  $data
 * @param  string  $path
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/21 下午8:01
 */
function array_to_file(array & $data, string $path){
    $str_cache = var_export($data, true);
    $str_cache = '<?php return '. $str_cache . '; ?>';
    //检查目录
    check_dir_by_path($path);
    file_put_contents($path, $str_cache);
    return true;
}

/**
 * 根据路径创建文件夹
 *
 * @param  string  $path
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/30 上午11:11
 */
function check_dir_by_path(string $path){
    $folder = get_folder_from_path($path);
    if(! is_dir($folder)) {
        mkdir($folder, 0777, true);
    }
}

/**
 * 写文件，不用判断文件是否存在
 *
 * @param  string  $path 物理路径
 * @param  string  $content
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/30 下午12:49
 */
function write_file(string $path, string $content){
    //写入文件
    check_dir_by_path($path);
    file_put_contents($path, $content);
}

/**
 * 写文件，不用判断文件是否存在
 *
 * @param  string  $path 物理路径
 * @param  string  $content
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/30 下午12:49
 */
function delete_file(string $path){
    if(file_exists($path)){
        unlink($path);
    }
}




/**
 * 将文件缓存转到数组
 *
 * @param  string  $path
 * @return array|mixed
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/21 下午8:13
 */
function file_to_array(string $path){
    if(file_exists($path)){
        return require($path);
    }
    return [];
}

/**
 * 从路径中找到文件夹
 *
 * @param  string  $path
 * @return false|string
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/21 下午8:09
 */
function get_folder_from_path(string $path)
{
    //找到文件中最后一个/的位置
    $rPos = strrpos($path,"/");
    if($rPos === false){
        return '';
    }
    return substr($path, 0, $rPos);
}

/**
 * 输出断点
 *
 * @param $data
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/22 下午7:43
 */
function dd($data, $var_dump=false){
    if($var_dump){
        var_dump($data);
    }else{
        print_r($data);
    }
    exit;
}

//获取当前时间
if (!function_exists('now')) {
    function now() {
        return date('Y-m-d H:i:s');
    }
}
/**
 * 检查请求来源，确保是本站提交的
 *
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/30 下午7:55
 */
function checkReferer(){
    //获取请求来源
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if(empty($referer)){
        return false;
    }
    //获取主机信息
    $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
    if(empty($host)){
        return false;
    }
    //解析获取主机
    /*Array(
        [scheme] => http
        [host] => local.phpcms
        [path] => /admin/guestbook/info
        [query] => id=1
    )*/
    $url_array = parse_url($referer);
    if($host == $url_array['host']){
        return true;
    }
    return false;
}

//获取http头的所有参数
if (!function_exists('getallheaders')) {
    function getallheaders(){
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

/**
 * 获取图片
 *
 * @param $id
 * @return string
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/9 上午10:08
 */
function getImage($id){
    $res = UploadsLogic::getUrlById($id);
    return $res['url'];
}

//如果字符串长度超过10，则截取并以省略号结尾
function strCutLen($str, $num){
    $str=(string)$str;
    if( mb_strlen($str,'utf-8') > $num){
        return mb_substr($str,0, $num,'utf-8').'...';
    }else{
        return $str;
    }
}

/**
 * 获取内容页的链接
 *
 * @param $id
 * @param $class_id
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/10 下午4:45
 */
function content_url($id, $class_id){
    $classInfo = \App\Logics\ClassifyLogic::getByIdCache($class_id);
    $question_mark = config('route.url_rewrite') ? '':'?';
    return config('url') .'/'. $question_mark . $classInfo['url'] .'/'.$id . '.html';
}

/**
 * 获取列表页的链接，处理伪静态
 *
 * @param $url
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/10 下午4:45
 */
function list_url($url){
    if(strpos($url, '://')){
        return $url;
    }
    $question_mark = config('route.url_rewrite') ? '':'?';
    return config('url') .'/'. $question_mark . $url;
}

/**
 * 获取TAG页的链接，tag目前只支持文章
 *
 * @param $url
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/10 下午4:45
 */
function tag_url($tag, $model='article'){
    $question_mark = config('route.url_rewrite') ? '':'?';
    return config('url') .'/'. $question_mark . $model .'_tag_'.$tag;
}

/**
 * 获取列表页的链接，处理伪静态
 *
 * @param $url
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/10 下午4:45
 */
function list_url_by_class_id($class_id){
    $question_mark = config('route.url_rewrite') ? '':'?';
    $classInfo = ClassifyLogic::getByIdCache($class_id);
    $url = $classInfo['url'] ?? '';
    return config('url') .'/'. $question_mark . $url;
}

/**
 * 构造扩展字段的搜索地址
 *
 * @param $value
 * @param $field
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/13 下午11:00
 */
function extend_search_url($value, $field){
    $model_id = \context('model_id');
    $class_id = \context('class_id');
    $question_mark = config('route.url_rewrite') ? '':'?';
    $classInfo = ClassifyLogic::getByIdCache($class_id);
    $url = $classInfo['url'] ?? '';
    $url =  config('url') .'/'. $question_mark . $url;
    //print_r(\Core\Request::getInstance()->all());
    $fields = ContentModuleExtendLogic::getModelFields($model_id);
    $args = \Core\Request::getInstance()->all();
    $params = [];
    foreach ($args as $k=>$v){
        if(in_array($k, $fields) && $k != $field){
            $params[$k] = $v;
        }
    }
    $params[$field] = $value;
    if(! empty($params)) {
        $strArgs = http_build_query($params);
    }else{
        $strArgs = '';
    }
    //有？的情况
    if(strpos($url, '?')){
        $url .= '&'. $strArgs;
    }else{
        $url .= '?'. $strArgs;
    }
    return $url;
}

/**
 * 构造扩展字段的搜索地址
 *
 * @param $value
 * @param $field
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/13 下午11:00
 */
function form_search_url($model_id=''){
    $question_mark = config('route.url_rewrite') ? '':'?';
    if(empty($model_id)){
        $name = 'product';
    }else{
        $modelInfo = ContentModuleLogic::getByIdCache($model_id);
        $name = $modelInfo['table'];
    }

    $url =  config('url') .'/'. $question_mark . $name .'_search';
    return $url;
}

/**
 * 显示全部按钮的样式
 *
 * @param string $field
 * @param string $css_active 选中的样式
 * @param string $css_common 未选中的
 * @return mixed
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/14 上午9:48
 */
function all_button_css(string $field, string $value, string $css_active, string $css_common){
    return input($field) == $value ? $css_active : $css_common;
}

/**
 * 显示当前页的二维码
 *
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/14 下午2:16
 */
function show_current_page_qrcode(){
    $currentUrl = \Core\Request::getInstance()->currentUrl();
    return url('/home/welcome/qrcode', ['url' => urlencode($currentUrl)]);
}

/**
 * 替换标题中的关键字
 *
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/14 下午10:25
 */
function replace_keywords(string $str){
    $keywords = input('keywords', '');
    //关键字不为空
    if(! empty($keywords)){
        $str = str_replace($keywords, '<span style="color:#ff0000;">' . $keywords .'</span>', $str);
    }
    return $str;
}

/**
 * 转换默认图片
 *
 * @param  string  $url
 * @return mixed|string
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/16 上午10:24
 */
function get_default_inner_nanner(string $url){
    if(empty($url)){
        $config = SystemLogic::getSystemCache();
        return $config['cover'] ?? '';
    }
    return $url;
}

/**
 * 解析属性 a="1" b="1,2"
 *
 * @param  string  $str
 * @return array
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/19 下午5:35
 */
function getPropertyFromString(string $str){
    if(empty($str)){
        return [];
    }
    $str = trim($str);
    $str = str_replace("  ", " ", $str);
    $str = str_replace("\"", "",$str);
    $ary = explode(' ', $str);
    $list = [];
    foreach ($ary as $v){
        $tmp = explode('=', $v);
        $list[$tmp[0]] = str_replace(',', ' ', $tmp[1]);
    }
    return $list;
}

/**
 * 安全字符过滤，移除html标签
 *
 * @param $str
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/26 上午9:26
 */
function safe_filter($str){
    //过滤<>标签
    $str = strip_tags($str);
    //过滤PHP及SQL危险字符
    $replaceArr = ['exec','eval','\\','/',"\"",'\'','`','&',' and ','(',')','%'];
    return str_replace($replaceArr, '', $str);
}

/**
 * 批量过滤
 *
 * @param $ary
 * @return mixed
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/26 上午10:04
 */
function save_filter_array($ary){
    if(empty($ary)){
        return $ary;
    }
    foreach ($ary as & $v){
        $v = safe_filter($v);
    }
    return $ary;
}

/**
 * 跨站攻击字符串过滤, 引用的其实是ThinkPHP中的函数
 *
 * @param $val
 * @return string|string[]|null
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/26 上午9:34
 */
function remove_xss($val) {
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(�{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
    }
    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);
    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(�{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }
    return $val;
}

function tag_list_html($tagStr, $model, $template=""){
    if(empty($tagStr)){
        return '';
    }
    $tagList = explode("|", $tagStr);
    $html = [];
    if(empty($template)){
        $template = '<a href="tag_url">tag_name</a>';
    }
    foreach ($tagList as $tag){
        $item = str_replace('tag_name', $tag, $template);
        $item = str_replace('tag_url', tag_url($tag, $model), $item);
        $html[] = $item;
    }
    return implode(" ", $html);
}

//内链处理
function inner_link($content){
    $model_id = \context('model_id');
    \App\Logics\InnerLinkLogic::replaceInnerLink($model_id, $content);
    return $content;
}

function convert_time_to_date($dateStr){
    return substr($dateStr, 0, strpos($dateStr, ' '));
}

function message_save_url(){
    return url('home/guestbook/save');
}

function comment_save_url(){
    return url('home/comment/save');
}

function get_current_route(){
    $uri = $_SERVER['REQUEST_URI'];// => /goods/search?dsgdsg
    if(strpos($uri,'?')){
        $uri = substr($uri, 0, strpos($uri, '?'));
    }
    return $uri;
}

function get_current_url(){
    $url = $_SERVER['REQUEST_SCHEME']  . '://'. $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
    return $url;
}