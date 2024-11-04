<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Logics\CompanyLogic;

class CompanyController extends Controller {

    /**
     * 显示页面
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 下午2:44
     */
    public function info(){
        $info = CompanyLogic::getInfoForEdit();
        $this->view('info', ['info' => $info, 'id' => 0]);
    }

    /**
     * 保存数据
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 下午2:45
     */
    public function save(){
        $data = \request()->all();
        CompanyLogic::save($data);
        $this->success([], 'success');
    }

}