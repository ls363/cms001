<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Logics\AdminLogLogic;
use Core\Request;
use Core\Route;

class AdminLogController extends Controller {

    private $logic;

    public function __construct(){
        $this->logic = AdminLogLogic::getInstance();
    }

    public function index(){
        $list = $this->logic->getPageList();
        $data = [
            'list' => $list
        ];
        $this->view(__FUNCTION__, $data);
    }

}