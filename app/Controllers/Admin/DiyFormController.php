<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Enums\PageEnums;
use App\Logics\ClassifyLogic;
use App\Logics\DiyFormLogic;
use App\Logics\JobLogic;
use App\Logics\SystemLogic;
use App\Utils\PageBar;
use Core\Request;
use Core\Route;

class DiyFormController extends Controller {

    private $logic;

    public function __construct(){
        $this->logic = DiyFormLogic::getInstance();
    }

    public function index(){
        $input = \request()->all();
        $data = $this->logic->getPageList($input);
        $pageBar = new PageBar();
        $page = \request()->input('page', 1);
        $data = [
            'system' => SystemLogic::getSystemCache(),
            'list' => $data['list'],
            'pageBar' => $pageBar->show($data['total'], PageEnums::PAGE_SIZE, $page),
            'input' => $input
        ];
        $this->view(__FUNCTION__, $data);
    }

    /**
     * 表单数据列表
     * @author lichunguang
     * @since 2024/2/26 00:13
     * @return void
     */
    public function dataList(){
        $input = \request()->all();
        $data = $this->logic->getDataList($input);
        //print_r($data);exit;
        $pageBar = new PageBar();
        $page = \request()->input('page', 1);
        $data = [
            'fields' => $data['fields'],
            'system' => SystemLogic::getSystemCache(),
            'list' => $data['list'],
            'pageBar' => $pageBar->show($data['total'], PageEnums::PAGE_SIZE, $page),
            'input' => $input
        ];
        $this->view(__FUNCTION__, $data);
    }

}