<?php
//ROOT_PATH改到index.php入口文件定义了
//define('ROOT_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR .'..'.DIRECTORY_SEPARATOR));

//定义模板URL
define('TEMPLATE_URL', PUBLIC_URL .'/templates');
//定义模板目录
define('TEMPLATE_PATH', PUBLIC_PATH .'/templates');

//上传文件目录
define('UPLOAD_PATH', PUBLIC_PATH .'/upload');

//上传文件目录 前台展示目录
define('UPLOAD_URL',  PUBLIC_URL .'/upload');

//应用的目录
define('APP_PATH', ROOT_PATH . '/app');

//应用控制器的目录
define('CONTROLLER_PATH', ROOT_PATH . '/app/Controllers');

//应用逻辑层的目录
define('LOGIC_PATH', ROOT_PATH . '/app/Logics');

//应用模型层的目录
define('MODEL_PATH', ROOT_PATH . '/app/Models');

//应用的目录
define('VIEW_PATH', ROOT_PATH . '/app/Views');

