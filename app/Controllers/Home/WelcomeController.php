<?php

namespace App\Controllers\Home;

use App\Controllers\Controller;
use App\Utils\QRcode;
use App\Utils\RandNumImage;
use App\Utils\UploadFile;
use App\Logics\UploadsLogic;

class WelcomeController extends Controller
{

    public function index()
    {
        $this->display('/welcome');
    }

    /**
     * 显示二维码
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/14 下午2:07
     */
    public function qrcode(){
        $url = $this->request->input('url');
        QRcode::png(urldecode($url), false, 0, 5, 1);
    }

    //输出验证码图片
    public function randNum()
    {
        RandNumImage::show();
    }

}