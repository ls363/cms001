<?php
namespace App\Middlewares;

use Core\Context;
use Core\Session;

class MemberAuth
{

    /**
     * 会员登录验证
     *
     * @param $request
     * @param  Closure  $next
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/22 下午3:27
     */
    public function handle($request, Closure $next){
        $token = Session::getInstance()->get(config('member_jwt_token_name'));
        $loginUrl = url(config('route.member_login_url'));
        if(empty($token)){
            redirect($loginUrl);
        }
        //校验jwt token的有效性，先跳过
        try{
            $data = jwt_decode($token);
            if(! isset($data->loginId) || $data->loginId == 0){
                redirect($loginUrl);
            }
            Context::set('loginId', $data->loginId);
        }catch (\Exception $e){
            redirect($loginUrl);
        }
    }


}