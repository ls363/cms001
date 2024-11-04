<?php
namespace App\Controllers\Home;

use App\Controllers\Controller;
use App\Logics\GuestbookLogic;
use App\Logics\PreviewLogic;
use App\Logics\SystemLogic;

class GuestbookController extends Controller{

    /**
     * 保存留言
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 下午8:02
     */
    public function save(){
        if(! checkReferer()){
            $this->error('非法提交');
        }
        try {
            //获取参数
            $args = $this->request->all();
            $args = save_filter_array($args);
            GuestbookLogic::saveFront($args);
            return $this->success([], '提交成功');
        }catch (\Exception $e){
            log_exception($e);
            return $this->error($e->getMessage());
        }
    }

    public function getList(){
        //加载系统配置
        $config = SystemLogic::getSystemCache();
        //读取内容页
        $args = context('app_current_route_args', []) ?? [];
        context_set('request_args', $args);
        //评论列表的模板是定死的
        $templateUrl = 'common/guestbook_list_ajax.shtml';
        header('Content-Type:text/html; charset=utf-8');
        echo PreviewLogic::getContentHtml($templateUrl, $config, $args);
    }
}