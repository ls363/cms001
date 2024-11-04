<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Enums\PageEnums;
use App\Logics\ContentModuleLogic;
use App\Logics\InnerLinkLogic;
use App\Utils\PageBar;
use Core\Request;

class InnerLinkController extends Controller {

    private $logic;

    public function __construct(){
        $this->logic = InnerLinkLogic::getInstance();
    }

    public function index(){
        $input = \request()->all();
        $data = InnerLinkLogic::getPageList($input);
        $pageBar = new PageBar();
        $page = \request()->input('page', 1);
        $modelList = ContentModuleLogic::getAll();
        $list = $data['list'];
        if(! empty($list)){
            foreach($list as & $v){
                $v['model_name'] = $modelList[$v['model_id']] ?? '';
            }
        }
        $data = [
            'list' => $list,
            'pageBar' => $pageBar->show($data['total'], PageEnums::PAGE_SIZE, $page),
            'modelList' => $modelList,
            'input' => $input
        ];
        $this->view(__FUNCTION__, $data);
    }

    public function info(){
        $id = request()->getInt('id', 0);

        //下取拉框数据
        $info = $id ? InnerLinkLogic::getById($id) : [];
        $data = [
            'id' => $id,
            'info' => $info,
            'modelList' => ContentModuleLogic::getAll(),
        ];
        $this->view('info', $data);
    }

    public function save(){
        $data = \request()->all();
        $id = isset($data['id']) ? $data['id'] : 0;
        InnerLinkLogic::save($id, $data);
        $this->success([], 'success');
    }

    public function delete(){
        $id = I('get.id', 0);
        InnerLinkLogic::delete($id);
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