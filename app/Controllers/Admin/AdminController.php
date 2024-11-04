<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Logics\AdminLogic;
use App\Enums\PageEnums;
use App\Utils\PageBar;

class AdminController extends Controller {

    private $logic;

    public function __construct(){
        $this->logic = AdminLogic::getInstance();
    }

    public function index(){
        $page = \request()->input('page', 1);
        $pageSize = PageEnums::PAGE_SIZE;
        $result = $this->logic->getPageList($page, $pageSize);
        $pageBar = new PageBar();
        $data = [
            'list' => $result['list'],
            'pageBar' => $pageBar->show($result['total'], $pageSize, $page)
        ];

        $this->view(__FUNCTION__, $data);
    }

    public function info(){
        $id = input('id', 0);
        $data = $this->logic->getDetail($id);
        $this->view('info', ['id'=>$id, 'info' => $data]);
    }

    public function save(){
        $data = $this->request->all();
        $id = $data['id'] ?? 0;
        $this->logic->save($id, $data);
        $this->success([], 'success');
    }

    public function delete(){
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