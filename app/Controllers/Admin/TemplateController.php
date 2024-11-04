<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Logics\ClassifyLogic;
use App\Logics\ContentModuleLogic;
use App\Logics\Maker\TemplateLogic;
use App\Logics\UploadsLogic;
use Core\Request;

class TemplateController extends Controller {

    public function tree(){
        $list = TemplateLogic::getDir();
        $data = [
            'treeData' => json_encode($list, JSON_UNESCAPED_UNICODE)
        ];
        $this->view('/admin/template/tree', $data);
    }

}