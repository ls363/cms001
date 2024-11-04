<?php


namespace App\Middlewares;


use Core\Context;
use Core\Session;

class ApiToken
{
    /**
     * 会员登录验证
     *
     * @param $request
     * @param  \Closure  $next
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/22 下午3:27
     */
    public function handle($request, \Closure $next){
        //获取token
        $token = $request->getToken();
        if(empty($token)){
            return api_error('您还没有登录');
        }
        if(strpos($token, 'Bearer ') === false){
            return api_error('Token格式错误');
        }
        //替换前辍
        $token = str_replace('Bearer ', '', $token);
        //校验jwt token的有效性，先跳过
        try{
            $data = jwt_decode($token);
            if(! isset($data->loginId) || $data->loginId == 0){
                return api_error('Token可能已经过期');
            }
            //Request对象存储
            $request->memberId = $data->loginId;
            //上下文存储
            Context::set('memberId', $data->loginId);
            return $next($request);
        }catch (\Exception $e){
            return api_error('Token异常');
        }
    }


}
