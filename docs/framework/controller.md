## 控制层

#### 控制层是API的入口，必须要继承Controller的基类

```
<?php
namespace App\Controllers\Home;

use App\Controllers\Controller;

class CommentController extends Controller{

    /**
     * 保存留言
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 下午8:02
     */
    public function save(){
        if(! checkReferer()){
            $this->error('非法提交');
        }
        //####
    }
}
```

### 视图加载

#### 视图的根目录： app/Views，视图的文件和文件夹命名，均采用小写字母+下划线。后台的视图规则：后台目录/控制层名称/方法名.php。

```
//下面这种，$viewPath 只能以"/"开头，加载Views下的文件，如/welcome.php
View::getInstance()->display($viewPath, $data);

//模板的路径为 /admin/admin/info.php
class AdminController extends Controller {
    public function info(){
        $id = input('id', 0);
        $data = $this->logic->getDetail($id);
        $this->view('info', ['id'=>$id, 'info' => $data]);
    }```
}
### AJAX输出
```
//成功返回的输出
$this->success(array $data = [], $message='success', $code=200);
//错误信息的输出
$this->error($message='failed', $code=500);
```