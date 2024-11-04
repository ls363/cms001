<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Enums\ModelEnums;
use App\Logics\ContentModuleExtendLogic;
use App\Logics\ContentModuleLogic;

class ContentModuleExtendController extends Controller {

    public function index(){
        $module_id = $this->request->model_id;
        $list = ContentModuleExtendLogic::getList($module_id);
        foreach ($list as & $v){
            $v['field_type_name'] = ModelEnums::$inputType[$v['field_type']] ?? '';
        }
        $data = [
            'model_id' => $module_id,
            'list' => $list
        ];
        $this->view(__FUNCTION__, $data);
    }

    public function info(){
        $id = request()->getInt('id', 0);
        $data = ['sort' => 1];
        if($id > 0){
            $data = ContentModuleExtendLogic::getDetail($id);
            $model_id = $data['model_id'];
        }else{
            $model_id = $this->request->model_id ?? 0;
        }
        $module = ContentModuleLogic::getDetail($model_id);
        $modelName = $module['title'] ?? '';
        $this->view('info', ['model_id' => $model_id,'modelName' => $modelName, 'info' => $data, 'id' => $id, 'typeRange' => ModelEnums::$inputType]);
    }

    public function save(){
        $data = \request()->all();
        $id = $data['id'];
        //保存模型字段
        ContentModuleExtendLogic::save($id, $data);
        $this->success([], 'success');
    }

    public function delete(){
        $id = I('get.id', 0);
        ContentModuleExtendLogic::delete($id);
        $this->success();
    }

    /**
     * 设置上下架状态
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午2:25
     */
    public function setState(){
        $this->success();
    }

    /**
     * 设置排序
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/18 下午7:10
     */
    public function sort(){
        $id = I('post.id', 0);
        $sort = I('post.val', 0);
        $res = ContentModuleExtendLogic::setSort($id, $sort);
        $this->success();
    }

}