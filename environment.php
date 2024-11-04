<?php
error_reporting(E_ALL && E_WARNING);
//最低PHP版本 7.0.9
define('REQUIRE_PHP_VERSION', '7.0.9');
define('ROOT_PATH', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR));
//静态文件目录，物理目录
define('PUBLIC_PATH', ROOT_PATH . '/public');

//静态文件目录，前台访问路径
define('PUBLIC_URL', '/public');
//运行时缓存
define('RUNTIMES_PATH', ROOT_PATH . '/runtimes');
