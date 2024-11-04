<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Logics\ClassifyLogic;
use App\Logics\ContentModuleLogic;
use App\Logics\Maker\TemplateLogic;
use App\Logics\SystemLogic;
use App\Logics\UploadsLogic;

class ClassifyController extends Controller {

    public function index(){
        $list = ClassifyLogic::getList();
        $data = [
            'system' => SystemLogic::getSystemCache(),
            'list' => $list
        ];
        $this->view(__FUNCTION__, $data);
    }

    public function info(){
        $id = request()->getInt('id', 0);
        $parent_id = request()->getInt('parent_id', 0);
        $data = ClassifyLogic::getDetail($id);
        if($id == 0){
            $data['state'] = 1;
            $data['parent_id'] = $parent_id;
        }
        $modelList = ContentModuleLogic::getInstance()->getAll();
        //模型配置
        $modelJson = ContentModuleLogic::getModuleList();
        $result = [
            'id' =>$id,
            'info' => $data,
            'modelList' => $modelList,
            'modelJson' => json_encode($modelJson, JSON_UNESCAPED_UNICODE),
            'parentOption' => ClassifyLogic::getSelectOption()
        ];

        //print_r($result);exit;
        $this->view('info', $result);
    }

    public function check(){
        $data = \request()->all();
        $folder = $data['url'];
        if(in_array($folder,config('protected_folders'))){
            $this->error('受保护的文件夹，请变更');
            return;
        }
        $this->success();
    }

    public function save(){
        $data = \request()->all();
        $id = isset($data['id']) ? $data['id'] : 0;
        $folder = $data['url'];
        if(in_array($folder,config('protected_folders'))){
            $this->error('受保护的文件夹，请变更');
            return;
        }
        ClassifyLogic::save($id, $data);
        $message = $id > 0 ? '修改成功' : '添加成功';
        $this->success([], $message);
    }

    public function delete(){
        $id = I('get.id', 0);
        ClassifyLogic::delete($id);
        $this->success([], '删除成功');
    }

    /**
     * 设置上下架状态
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午2:25
     */
    public function setState(){
        $id = I('get.id', 0);
        $state = I('get.state', 0);
        $res = ClassifyLogic::setState($id, $state);
        $this->success();
    }

    /**
     * 设置排序
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/18 下午7:10
     */
    public function sort(){
        $id = I('id', 0);
        $sort = I('val', 0);
        $res = ClassifyLogic::setSort($id, $sort);
        $this->success();
    }

}