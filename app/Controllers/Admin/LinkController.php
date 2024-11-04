<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Enums\PageEnums;
use App\Logics\LinkCategoryLogic;
use App\Logics\LinkLogic;
use App\Utils\PageBar;
use Core\Request;

class LinkController extends Controller {

    public function index(){
        $input = \request()->all();
        $data = LinkLogic::getPageList($input);
        $pageBar = new PageBar();
        $page = \request()->input('page', 1);
        $data = [
            'list' => $data['list'],
            'pageBar' => $pageBar->show($data['total'], PageEnums::PAGE_SIZE, $page),
            'categoryList' => LinkCategoryLogic::getAll(),
            'input' => $input
        ];
        $this->view(__FUNCTION__, $data);
    }

    public function info(){
        $id = request()->getInt('id', 0);

        //下取拉框数据
        $info = $id ? LinkLogic::getById($id) : [];
        $data = [
            'id' => $id,
            'info' => $info,
            'categoryList' => LinkCategoryLogic::getAll()
        ];
        $this->view('info', $data);
    }

    public function save(){
        $data = \request()->all();
        $id = isset($data['id']) ? $data['id'] : 0;
        LinkLogic::save($id, $data);
        $this->success([], 'success');
    }

    public function delete(){
        $id = I('get.id', 0);
        LinkLogic::delete($id);
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