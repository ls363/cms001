<?php
return [
    'entry_url'     => '',
    'url_rewrite'   => env('URL_REWRITE', false),
    'public_module' => ['Home'], //开放的模块
    'auth_module' => ['Admin'], //需要认证的模块
    'auth_middleware' => ['Admin' => ['admin_auth', 'csrf_token']],//设置模块对应的中间件
    'auth_module_white_list' => ['Login','Test'], //免验证的控制器白名单
    'default_module' => 'Home',
    'default_controller' => 'Preview',
    'default_action' => 'index',
    'admin_login_url' => env('ADMIN_DIR', 'cms').'/Login/index', //管理员登录的地址
    'member_login_url' => 'home/Login/index' //会员登录的地址
];