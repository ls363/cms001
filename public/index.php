<?php
error_reporting(E_ALL && E_WARNING);
define('ROOT_PATH', realpath(dirname(__FILE__) .'/../'));

//静态文件目录，物理目录
define('PUBLIC_PATH', ROOT_PATH . '/public');

//静态文件目录，前台访问路径
define('PUBLIC_URL', '');
//运行时缓存
define('RUNTIMES_PATH', ROOT_PATH . '/runtimes');

// PHP版本检测
if (version_compare(PHP_VERSION, REQUIRE_PHP_VERSION, '<')) {
    header('Content-Type:text/html; charset=utf-8');
    exit('您服务器PHP的版本太低，程序要求PHP版本不小于'. REQUIRE_PHP_VERSION);
}

require '../vendor/autoload.php';
if(!defined('ROOT_PATH') || !defined('PUBLIC_PATH')){
    exit('请检查常量 ROOT_PATH 与 PUBLIC_PATH');
}
try {
//检查是否已安装
    if (!file_exists('install.lock')) {
        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, 'home/install') === false) {
            redirect(url('home/install/index'));
        }
        define('HAS_INSTALL', false);
    }else{
        define('HAS_INSTALL', true);
    }

    \Core\Kernel::getInstance()->handle();
}catch (\Exception $e){
    log_exception($e);
    if(strpos($e->getMessage(), 'Connection refused')) {
        $message = '数据库连错有误，请联系开发者';
    }else{
        $message = $e->getMessage();
    }
    if($message == 'could not find driver'){
        $message .= " , 需要开启 pdo_mysql 。";
    }
    $path = 'error_page';
    $data = [
        'message' => $message,
        'file' => '',
        'line'  => 0
    ];
    \Core\View::getInstance()->display($path, $data);
}
