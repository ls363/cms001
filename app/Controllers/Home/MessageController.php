<?php

namespace App\Controllers\Home;

use App\Controllers\Controller;
use App\Logics\GuestbookLogic;

class MessageController extends Controller
{

    public function index()
    {
        $this->display('/welcome');
    }

    public function save(){
        $input = input();
        //处理安全过滤
        foreach ($input as $k=>$v){
            $input[$k] = remove_xss($v);
        }
        $bool = GuestbookLogic::saveFront($input);
        if($bool){
            return $this->success();
        }
        return $this->error('参数错误');
    }

}
