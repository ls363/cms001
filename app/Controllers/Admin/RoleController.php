<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Logics\MenuLogic;
use Core\Request;

class RoleController extends Controller {

    private $logic;

    public function __construct(){
        $this->logic = MenuLogic::getInstance();
    }

    public function index(){
        $list = $this->logic->getPageList();
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
        $data = $this->logic->getDetail($id);
        $this->view('info', ['data' => $data]);
    }

    public function save(){
        $data = [];
        $id = I('post.id', 0);
        $data['name'] = I('post.name');
        $data['link_url'] = I('post.link_url');
        log_error('article', $data);
        $this->logic->save($id, $data);
        $this->success([], 'success');
    }

    public function delete(){
        //print_r($_GET);
        //print_r($_POST);
        $id = I('get.id', 0);
        $this->logic->delete($id);
        $this->success();
    }

    /**
     * 设置上下架状态
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午2:25
     */
    public function setStatus(){
        $this->success();
    }

}