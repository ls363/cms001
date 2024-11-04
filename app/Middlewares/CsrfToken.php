<?php
namespace App\Middlewares;


use Core\Request;

class CsrfToken
{

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

        //关闭csrf_token检查，直接返回
        if(! config('auto_check_csrf_token')){
            return $next($request);
        }
        /**
         * 检查POST的时候，有没有传csrf_token,即name="_token"
         */
        $method = Request::getInstance()->method();
        if($method == 'POST') {
            $token = Request::getInstance()->post("_token");
            //如果_token不一致，说明非法请求
            if (!check_csrf_token($token)) {
                $ajax = Request::getInstance()->postInt("_ajax", 0);
                if($ajax == 1){
                    $data = ['code' => 500, 'message' => 'CSRF_TOKEN校验失败'];
                    echo json_encode($data, JSON_UNESCAPED_UNICODE);
                    exit;
                }else {
                    $this->errorPage('error', 'CSRF_TOKEN为空');
                }
            }
        }

        $response = $next($request);
        //这里可以设置输出的参数
        return $response;
    }
}
