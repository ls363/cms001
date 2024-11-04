<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Enums\ModelEnums;
use App\Logics\ContentModuleLogic;
use App\Logics\ModuleCopyLogic;

class ContentModuleController extends Controller {

    public function index(){
        $list = ContentModuleLogic::getList();
        foreach ($list as & $v){
            $v['type_name'] = ModelEnums::$typeList[$v['type']] ?? '';
        }
        $data = [
            'list' => $list
        ];
        $this->view(__FUNCTION__, $data);
    }

    public function info(){
        $id = request()->getInt('id', 0);
        $data = ['sort' => 1];
        if($id > 0){
            $data = ContentModuleLogic::getDetail($id);
        }
        $this->view('info', ['info' => $data, 'id' => $id, 'typeRange' => ModelEnums::$typeList]);
    }

    public function save(){
        $data = \request()->all();
        //log_error('article', $data);
        $id = $data['id'];
        ContentModuleLogic::save($id, $data);
        ContentModuleLogic::getModuleList(true);
        $message = $id > 0 ? '内容模型修改成功' : '内容模型添加成功';
        $this->success([], $message);
    }

    //刷新模型
    public function refresh(){
        $data = \request()->all();
        $id = $data['id'];
        try{
            $result = ContentModuleLogic::refresh($id);
            ContentModuleLogic::getModuleList(true);
            $message = $result['title'].'模型刷新成功';
        }catch (\Exception $e){
            $message = $e->getMessage();
        }
        $this->success([], $message);
    }

    public function delete(){
        //print_r($_GET);
        //print_r($_POST);
        $id = I('get.id', 0);
        ContentModuleLogic::delete($id);
        $this->success([], '删除成功');
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
        $res = ContentModuleLogic::setSort($id, $sort);
        $this->success();
    }

    /**
     * 初始化模板
     *
     * @author lichunguang
     * @since 2024/2/20 14:10
     * @return void
     */
    public function initTemplate(){
        $table = input('table');
        ModuleCopyLogic::copyInfoTemplate($table);
        $this->success();
    }

}