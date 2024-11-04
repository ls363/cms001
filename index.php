<?php
require 'environment.php';
require 'vendor/autoload.php';

//最低PHP版本 7.0.9
define('REQUIRE_PHP_VERSION', '7.0.9');

if(!defined('ROOT_PATH') || !defined('PUBLIC_PATH')){
    exit('请检查常量 ROOT_PATH 与 PUBLIC_PATH');
}

// PHP版本检测
if (version_compare(PHP_VERSION, REQUIRE_PHP_VERSION, '<')) {
    header('Content-Type:text/html; charset=utf-8');
    exit('您服务器PHP的版本太低，程序要求PHP版本不小于'. REQUIRE_PHP_VERSION);
}

try {
//检查是否已安装
    if (!file_exists('public/install.lock')) {
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
