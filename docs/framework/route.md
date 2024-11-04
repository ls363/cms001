# 路由

#### 路由是 MVC 框架最重要的部分，将用户访问的URL映身到控制器，这就是路由的功能。
#### 目前支持变量路由，不支持*号路由，*路由的算法还没有完全想好
#### CMS001默认支持 module/controller/action 的路由方式

##### 后台路由
```
Route::get('admin/login/index', LoginController::class, 'index');
Route::post('admin/login/doLogin', LoginController::class, 'doLogin');
//登录中间件的用法，route.php 中已经配置了，Admin 模块必须登录。
Route::group(['middleware'  => ["admin_auth"]], function () {
    Route::get('admin', MainController::class, 'index');
    Route::get('admin/main/index', MainController::class, 'index');
});

```

#### 前台页面路由
```
//新闻列表分页的路由，news 对映的模型ID为2，分类ID也是2，如果访问路径为 news_5，则{page}的值就是5。
Route::any('news_{page}', PreviewController::class, 'index', ['class_id'=> 2, 'model_id' => 2]);

```