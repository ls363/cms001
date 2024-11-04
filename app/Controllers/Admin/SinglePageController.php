<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Logics\SinglePageLogic;
use App\Enums\PageEnums;
use App\Logics\SystemLogic;
use App\Utils\PageBar;
use App\Logics\ClassifyLogic;

class SinglePageController extends Controller {

    public function index(){
        $data = [
            'system' => SystemLogic::getSystemCache(),
            'list' => SinglePageLogic::getList()
        ];
        $this->view(__FUNCTION__, $data);
    }

    public function info(){
        $class_id = $this->request->input('class_id', 0);
        $data = ['content' => '', 'class_id' => $class_id];
        if($class_id > 0){
            //这里有新增单页的情况
            $data = SinglePageLogic::getDetailByClassId($class_id);
            if(empty($data)){
                $data = ['content' => '', 'class_id' => $class_id];
            }
        }
        $this->view('info', ['id'=>0,'info' => $data]);
    }

    public function save(){
        $data = request()->all();
        SinglePageLogic::save($data);
        $message = '编辑成功';
        $this->success([], $message);
    }

    public function delete(){
        $id = I('get.id', 0);
        SinglePageLogic::delete($id);
        $this->success([], '删除成功');
    }

    /**
     * 设置上下架状态
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午2:25
     */
    public function setState(){
        $id = I('id', 0);
        $state = I('state', 0);
        SinglePageLogic::setState($id, $state);
        $this->success();
    }

}