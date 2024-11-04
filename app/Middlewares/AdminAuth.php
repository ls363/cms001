<?php
namespace App\Middlewares;

use Core\Context;
use Core\Session;

class AdminAuth{

    /**
     * 中间件的处理逻辑，接收request, 返回response对象
     *
     * @param $request 原始对象，不做处理
     * @param  \Closure  $next
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/22 上午10:05
     */
    public function handle($request, \Closure $next){
        $token = Session::getInstance()->get('admin_token');
        $loginUrl = url(config('route.admin_login_url'));
        if(empty($token)){
            redirect($loginUrl);
        }
        //校验jwt token的有效性，先跳过
        try{
            $data = jwt_decode($token);
            if(! isset($data->loginId) || $data->loginId == 0){
                redirect($loginUrl);
            }
            //$this->adminId = $data->loginId;
            Context::set('adminId', $data->loginId);
            return $next($request);
        }catch (\Exception $e){
            redirect($loginUrl);
        }
    }

}