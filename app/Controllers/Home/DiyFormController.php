<?php
namespace App\Controllers\Home;

use App\Controllers\Controller;
use App\Enums\ErrorEnums;
use App\Logics\DiyFormLogic;
use Core\WebSession;

class DiyFormController extends Controller{

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
        //获取参数
        $args = $this->request->all();
        $args = save_filter_array($args);
        $formId = intval($args['form_id'] ?? 0);
        if($formId == 0){
            $this->error('非法提交');
        }
        try{
            $verifyCode = $args['verify_code'] ?? '';
            $originCode = WebSession::getInstance()->get(config('session_auth_num_key'));
            if(strtolower($verifyCode) != strtolower($originCode)){
                throw new \Exception("验证码错误");
            }
            $message = DiyFormLogic::getInstance()->saveFront($args);
            $this->success([], $message);
        }catch (\Exception $e){
            $this->error($e->getMessage());
            return;
        }

    }
}