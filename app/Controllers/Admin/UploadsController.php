<?php

/**
 * 用户管理
 *
 * @author      fzs
 * @Time: 2017/07/14 15:57
 * @version     1.0 版本号
 */

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Utils\UploadFile;
use App\Logics\UploadsLogic;

class UploadsController extends Controller
{

    public function upload()
    {
        $instance = new UploadFile('file');
        $args = $instance->upFile();
        if (empty($args)) {
            throw new \Exception($instance->getErrorInfo(), 500);
        }
        //保存文件到数据库
        $data = [
            'admin_id' => 0,
            'name'    => isset($args['title']) ? $args['title'] : '',
            'folder'  => isset($args['folder']) ? $args['folder'] : '',
            'original' => isset($args['original']) ? $args['original'] : '',
            'size'     => isset($args['size']) ? $args['size'] : '',
            'extension'     => isset($args['type']) ? $args['type'] : '',
            'mime_type' => isset($args['mime_type']) ? $args['mime_type'] : '',
            'width' => $args['width'] ?? 0,
            'height' => $args['height'] ?? 0,
        ];
        $data['id'] = UploadsLogic::save($data);

        //拼成物理路径，并生成缩略图
        //$full_path = UPLOAD_PATH .'/'.$args['url'];
        //ImageUtils::makePreview($full_path);

        //用于前端渲染
        $data['src'] = UPLOAD_URL .'/'. $args['url'];
        $this->success($data);
    }

    //显示所有的图片列表
    public function index(){

    }

    public function onePic(){
        $this->view('onePic');
    }

    public function multiplePic(){
        $this->view('multiplePic');
    }

    public function multipleFile(){
        $this->view('multipleFile');
    }

}
