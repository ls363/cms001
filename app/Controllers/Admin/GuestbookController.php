<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Enums\PageEnums;
use App\Logics\GuestbookLogic;
use App\Utils\PageBar;
use Core\Request;

class GuestbookController extends Controller {

    private $logic;

    public function __construct(){
        $this->logic = new GuestbookLogic();
    }

    public function index(){
        $input = \request()->all();
        $data = GuestbookLogic::getPageList($input);
        $pageBar = new PageBar();
        $page = \request()->input('page', 1);
        $data = [
            'list' => $data['list'],
            'pageBar' => $pageBar->show($data['total'], PageEnums::PAGE_SIZE, $page),
            'input' => $input
        ];
        $this->view(__FUNCTION__, $data);
    }

    public function info(){
        $id = request()->getInt('id', 0);

        //下取拉框数据
        $info = $id ? GuestbookLogic::getById($id) : [];
        $data = [
            'id' => $id,
            'info' => $info,
        ];
        $this->view('info', $data);
    }

    public function save(){
        $data = \request()->all();
        $id = isset($data['id']) ? $data['id'] : 0;
        GuestbookLogic::save($id, $data);
        $this->success([], 'success');
    }

    public function delete(){
        $id = I('get.id', 0);
        GuestbookLogic::delete($id);
        $this->success();
    }

    //批量删除
    public function batchDelete(){
        $ids = I('get.ids');
        GuestbookLogic::batchDelete($ids);
        $this->success();
    }

    /**
     * 设置上下架状态
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午2:25
     */
    public function setStatus(){
        $id = input('id', 0);
        $state = input('state', 0);
        $this->logic->setState($id, $state);
        $this->success();
    }

}