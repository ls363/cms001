<?php
namespace App\Utils;

use App\Enums\HtmlEnums;

/**
 * Class MakeHtml
 * 生成静态页面
 *
 * @package App\Libraries\HtmlMaker
 * @author lichunguang 153102250@qq.com
 * @since 2022/4/22 下午4:23
 */
class MakeHtml{

    /**
     * 生成文件HTML, 会自动创建文件夹
     *
     * @param  string  $path
     * @param  string  $html
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/22 下午6:19
     */
    public static function makeFile(string $path, string $html){
        if(PUBLIC_URL == ''){
            $url = ROOT_PATH . '/public/' . $path;
        }else{
            $url = ROOT_PATH . HtmlEnums::HTML_PATH . $path;
        }

        $folder = substr($url, 0, strrpos($url,"/"));
        if(! is_dir($folder)) {
            mkdir($folder, 0777, true);
        }
        file_put_contents($url, $html);
    }

}