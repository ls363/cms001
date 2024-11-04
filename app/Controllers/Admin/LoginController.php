<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Logics\AdminLogic;

class LoginController extends Controller {

    public function index(){
        //echo 'admin hello login index';
        $data = ['site_name' => $this->system['site_name']];
        $this->view(__FUNCTION__, $data);
    }

    /**
     * 登录
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/29 下午3:57
     */
    public function doLogin(){
        try {
            //始用注入的request对象
            $name = $this->request->input('username', '');
            $password = $this->request->input('password', '');
            $verifyCode = $this->request->input('verifyCode', '');
            $data = AdminLogic::getInstance()->doLogin($name, $password, $verifyCode);
            $this->success($data, '登录成功');
        }catch (\Exception $e){
            log_exception($e);
            $this->error($e->getMessage());
        }
    }

    public function checkToken(){
        $token = $this->request->input('admin_token');
        try {
            $data = jwt_decode($token);
            if(! isset($data->loginId) || $data->loginId == 0){
                return $this->error();
            }
            return $this->success();
        }catch (\Exception $e){
            return $this->error();
        }
    }

}
