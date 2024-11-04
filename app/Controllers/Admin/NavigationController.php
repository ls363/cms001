<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Enums\BannerEnums;
use App\Logics\NavigationLogic;

class NavigationController extends Controller {

    public function index(){
        $list = NavigationLogic::getPageList();
        $data = [
            'list' => $list
        ];
        $this->view(__FUNCTION__, $data);
    }

    public function info(){
        $id = request()->input('id', 0);
        if($id == 0){
            $data = ['state' => 1, 'sort' => 0];
        }else{
            $data = NavigationLogic::getDetail($id);
        }
        $this->view('info', ['id' => $id, 'info' => $data, 'showRange' => BannerEnums::$showRange]);
    }

    public function save(){
        $data = \request()->all();
        $id = $data['id'] ?? 0;
        NavigationLogic::save($id, $data);
        $this->success([], 'success');
    }

    public function delete(){
        $id = I('get.id', 0);
        NavigationLogic::delete($id);
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
        NavigationLogic::setState($id, $state);
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