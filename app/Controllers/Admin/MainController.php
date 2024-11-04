<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Logics\CommentLogic;
use App\Logics\MainLogic;
use App\Logics\MenuLogic;
use App\Logics\SystemLogic;
use Core\Request;

class MainController extends Controller {

    private $logic;

    public function __construct(){
        $this->logic = MainLogic::getInstance();
    }

    public function index(){
        $menuLogic = new MenuLogic();
        $userInfo = $this->logic->getUserInfo();
        $data = [
            'system' => SystemLogic::getSystemCache(),
            'userInfo' => $userInfo,
            'question_mark' => config('route.url_rewrite') ? '' : '?',
            'menus' => $menuLogic->getTreeList()
        ];
        $this->view(__FUNCTION__, $data);
    }

    public function welcome(){
        $data = [
            'server' => $this->logic->getServerInfo(),
            'module_list' => $this->logic->getModuleList(),
            'comment_num' => CommentLogic::getAllNum()
        ];
        $this->view(__FUNCTION__, $data);
    }

    public function userinfo(){
        $userInfo = $this->logic->getUserInfo();
        $data = ['userInfo' => $userInfo];
        $this->view(__FUNCTION__, $data);
    }

    /**
     * 保存当前用户信息
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午3:14
     */
    public function saveUserInfo(){
        $data = [
            'real_name' => I('post.real_name'),
            'phone' => I('post.phone'),
            'email' => I('post.email'),
            'sex' => I('post.sex', 0),
        ];
        $this->logic->saveUserInfo($data);
        $this->success();
    }

    /**
     * 修改密码
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午3:09
     */
    public function changePassword(){
        $originPassword = I('post.originPassword');
        $newPassword = I('post.newPassword');
        $confirmPassword = I('post.confirmPassword');
        $this->logic->changePassword($originPassword, $newPassword, $confirmPassword);
        $this->success([], '密码修改成功');
    }

    /**
     * 退出登录
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/29 下午8:18
     */
    public function logout(){
        $this->logic->logout();
    }

}
