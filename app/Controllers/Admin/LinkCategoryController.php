<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Logics\LinkCategoryLogic;
use Core\Request;

class LinkCategoryController extends Controller {

    private $logic;

    public function __construct(){
        $this->logic = LinkCategoryLogic::getInstance();
    }

    public function index(){
        $list = LinkCategoryLogic::getPageList();
        //print_r($list);exit;
        $data = [
            'classifyList' => $list
        ];
        $this->view(__FUNCTION__, $data);
    }

    public function add(){
        $this->view('info');
    }

    public function edit(){
        $id = request()->getInt('id');
        $data = LinkCategoryLogic::getDetail($id);
        $this->view('info', ['data' => $data]);
    }

    public function save(){
        $data = \request()->all();
        $id = $data['id'] ?? 0;
        LinkCategoryLogic::save(intval($id), $data);
        $this->success([], 'success');
    }

    public function delete(){
        $id = I('get.id', 0);
        LinkCategoryLogic::delete($id);
        $this->success();
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
        LinkCategoryLogic::setState($id, $state);
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
        $res = $this->logic->setSort($id, $sort);
        $this->success();
    }

}