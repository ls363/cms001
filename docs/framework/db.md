# CMS001后台框架使用指南

### 数据库加载如下
```
//引入环境变量
require 'environment.php';
//composer包加载
require 'vendor/autoload.php';
//数据库门面
use App\Facades\Db;
//加载.env
\Core\Env::load();
//加载数据库配置
$config = \App\Facades\Config::getInstance();
$config->load('database');
```

### 数据查询
````
查询方式一：
$data = \App\Models\Base\Article::where('id', 15)->first();
查询方式二：
$data = Db::table('article')->where('id', 15)->first();
print_r($data);
关闭数据库连接
Db::close();
````

### 模型层，表模型的基类，一般不在这里写方法
```
<?php
namespace App\Models\Base;

class Comment extends Model {

}
```

### 模型层扩展，主要是写一些数据库相关的业务
```
<?php

namespace App\Models;

use App\Enums\PageEnums;
use App\Models\Base\Comment;

class CommentModel extends Comment
{

    /**
     * 根据用户名查询
     *
     * @param  string  $username
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午3:53
     */
    public static function getByUserName(string $username)
    {
        return self::where('username', $username)->first();
    }

```