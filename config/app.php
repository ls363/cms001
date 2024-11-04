<?php

return [
    'url'   => env('APP_URL'),
    'admin_dir' => env('ADMIN_DIR', 'cms'),
    'name'                  => env('APP_NAME'),
    'auto_loader_config'    => ['app', 'database'],
    'controller_namespace'  => 'App\Controllers\\',
    'jwt_token_name'        => 'admin.token',
    'member_jwt_token_name' => 'member_token',
    'member_jwt_ttl'        => 3600,
    'admin_jwt_token_name'  => 'admin_token',
    'admin_jwt_ttl'         => 86400,
    'csrf_token'            => 'csrf_token',
    'auto_check_csrf_token' => true,    //默认开启检查_token的传值，安全校验
    'cookie_domain'         => env('COOKIE_DOMAIN', 'localhost'),
    'session_auth_num_key'  => 'authNum',
    'enable_template_cache'    => env('ENABLE_TEMPLATE_CACHE', true),  //是否启用前台模板缓存，调试阶段关闭
    'enable_views_cache'    => env('ENABLE_VIEWS_CACHE', false),  //是否启用视图缓存，调试阶段关闭
    'enable_route_cache'    => env('ENABLE_ROUTE_CACHE', true),  //是否启用路由缓存，调试阶段关闭
    'protected_folders' => ['app','config','helpers','core','database','docs','public','routes','runtimes', 'editor','url_rewrite', 'vendor']
];