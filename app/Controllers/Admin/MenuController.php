<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Logics\MenuLogic;
use Core\Request;

class MenuController extends Controller {

    private $logic;

    public function __construct(){
        $this->logic = new MenuLogic();
    }

    public function index(){
        $list = $this->logic->getTreeList();
        $data = [
            'menus' => $list
        ];
        $this->view(__FUNCTION__, $data);
    }

    public function add(){
        $data = ['id' => 0, 'menus' => $this->logic->getParentList()];
        $this->view('add', $data);
    }

    public function edit(){
        $id = request()->getInt('id');
        $data = $this->logic->getDetail($id);
        $this->view('edit', ['menu' => $data, 'menus' => $this->logic->getParentList(), 'id' => $id]);
    }

    public function save(){
        //print_r(\request()->all());exit;
        $data = \request()->all();
        //log_error('article', $data);
        $id = $data['id'];
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