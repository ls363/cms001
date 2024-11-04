<?php

use App\Logics\ClassifyLogic;
use App\Logics\ContentModuleLogic;
use Core\Route;
use App\Controllers\Admin\MainController;
use App\Controllers\Admin\LoginController;
use App\Controllers\Home\PreviewController;

//登录
Route::get('admin/login/index', LoginController::class, 'index');
Route::post('admin/login/doLogin', LoginController::class, 'doLogin');
Route::group(['middleware'  => ["admin_auth"]], function () {
    Route::get('admin', MainController::class, 'index');
    Route::get('admin/main/index', MainController::class, 'index');
});

//Route::get('admin/login/index', LoginController::class, 'index');
Route::get('diyform', PreviewController::class, 'diyform');



/**
 * 处理模块标标签页的路由
 *
 * @author lichunguang
 * @since 2024/2/22 21:11
 * @return void
 */
function registerTagRule(){
    if(HAS_INSTALL === false){
        return false;
    }
    $list = ContentModuleLogic::getModuleList();
    foreach ($list as $v){
        if($v['type'] == 2){
            //注册TAG路由
            Route::get($v['table'].'_tag_{tag}', PreviewController::class, 'tag', ['model_id' => $v['id']]);
            //注册搜索页路由
            Route::get($v['table'].'_search', PreviewController::class, 'search', ['model_id' => $v['id']]);
        }
    }
}

/**
 * 处理栏目的路由
 *
 * @author lichunguang 153102250@qq.com
 * @since 2022/9/7 下午7:57
 */
function registerClassRule(){
    if(HAS_INSTALL === false){
        return false;
    }
    $list = ClassifyLogic::getRouteList();
    foreach ($list as $v){
        if(empty($v['url'])){
            continue;
        }
        //通过栏目添加注入的路由规则
        Route::any($v['url'].'_{page}', PreviewController::class, 'index', ['class_id'=> $v['id'], 'model_id' => $v['model_id']]);
        Route::any($v['url'], PreviewController::class, 'index', ['class_id'=> $v['id'], 'model_id' => $v['model_id']]);
        Route::any($v['url'] .'/{id}.html', PreviewController::class, 'index', ['class_id'=>$v['id'], 'model_id' => $v['model_id']]);
    }
}

//注册栏目页路由
registerClassRule();
//注册标签页路由
registerTagRule();