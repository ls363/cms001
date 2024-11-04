<?php

namespace App\Utils;

use Core\WebSession;

class RandNumImage
{

    /**
     * 生成随机数
     *
     * @param  bool  $hasLetter
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/26 下午7:10
     */
    public static function getRandString(bool $hasLetter = true)
    {
        $hasLetter = true; //是否出现字母
        $num = 0 + mt_rand() / mt_getrandmax();
        if ($hasLetter) {
            $a = substr(md5($num * 10000000000000000), 0, 1);
            $b = substr(md5($num * 10000000000000000), 4, 1);
            $c = substr(md5($num * 10000000000000000), 8, 1);
            $d = substr(md5($num * 10000000000000000), 12, 1);
        } else {
            $a = substr(hexdec(md5($num * 10000000000000000)), 2, 1);
            $b = substr(hexdec(md5($num * 10000000000000000)), 3, 1);
            $c = substr(hexdec(md5($num * 10000000000000000)), 4, 1);
            $d = substr(hexdec(md5($num * 10000000000000000)), 5, 1);
        }
        //四位随机数转大写
        $randNum = strtoupper($a . $b . $c . $d);
        //统一保存session
        WebSession::getInstance()->set(config('session_auth_num_key'), $randNum);
        return $randNum;
    }

    public static function show(bool $hasLetter = true)
    {
        $randNum = self::getRandString();
        //生成验证码图片
        header("Content-type: image/PNG");
        $im = imagecreate(46, 20);
        //$im = imagecreate(92, 40);
        srand((double) microtime() * 1000000);
        $Red = rand(0, 200);
        $Green = rand(0, 200);
        $Blue = rand(0, 200);
        $Color = imagecolorallocate($im, $Red, $Green, $Blue);
        $BackGround = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $BackGround);

        //输出验证码到画布上
        imagestring($im, 100, 5, 2, $randNum, $Color);
        for ($i = 0; $i < 50; $i++)   //加入干扰象素
        {
            $randcolor = imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255));
            imagesetpixel($im, rand() % 46, rand() % 30, $randcolor);
        }
        imagepng($im);
        imagedestroy($im);
    }
}