<?php
namespace App\Logics;

use App\Enums\PageEnums;
use Core\Session;
use core\Singleton;
use App\Models\AdminModel;
use App\Enums\ErrorEnums;
use Core\WebSession;
use App\Utils\PasswordUtils;

class AdminLogic{

    use Singleton;

    /**
     * 登录
     *
     * @param  string  $username
     * @param  string  $password
     * @param  string  $verifyCode
     * @throws \Exception
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/29 下午4:08
     */
    public function doLogin(string $username, string $password, string $verifyCode){
        if(empty($username) || empty($password) || empty($verifyCode)){
            throw new \Exception(ErrorEnums::INVALID_ARGS, ErrorEnums::DEFAULT_ERROR);
        }
        $originCode = WebSession::getInstance()->get(config('session_auth_num_key'));
        if(strtolower($verifyCode) != strtolower($originCode)){
            throw new \Exception(ErrorEnums::LOGIN_VERIFY_ERROR, ErrorEnums::DEFAULT_ERROR);
        }
        $userInfo = AdminModel::getInstance()->getByUserName($username);
        if(empty($userInfo)){
            throw new \Exception(ErrorEnums::ADMIN_NOT_FOUND, ErrorEnums::DEFAULT_ERROR);
        }
        if(PasswordUtils::verifyPassword($password, $userInfo['salt']) != $userInfo['password']){
            throw new \Exception(ErrorEnums::ADMIN_PASSWORD_ERROR, ErrorEnums::DEFAULT_ERROR);
        }

        $data = [
            'loginId' => $userInfo['id']
        ];
        $token = jwt_encode($data,  config('admin_jwt_ttl'));
        Session::getInstance()->add(config('admin_jwt_token_name'), $token, config('admin_jwt_ttl'));
        return ['token' => $token, 'name' => $userInfo['real_name']];
    }

    /**
     * 获取文章详情
     *
     * @param  int  $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午2:18
     */
    public function getDetail(int $id){
        if($id == 0){
            return [];
        }
        return AdminModel::getInstance()->getDetail($id);
    }

    /**
     * 获取分页列表
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/31 下午8:18
     */
    public function getPageList(int $page, int $pageSize){
         return AdminModel::getInstance()->getPageList($page, $pageSize);
    }

    /**
     * 添加或者修改数据, 单条数据的修改
     * @param int $id
     * @param array $data
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午4:55
     */
    public function save(int $id, array $data){
        if($id > 0){
            return AdminModel::where('id', $id)->update($data);
        }else{
            return AdminModel::insert($data);
        }
    }

    /**
     * 单条记录删除
     *
     * @param  int  $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午5:00
     */
    public function delete(int $id){
        log_error('delete', $id);
        return AdminModel::getInstance()->where('id', $id)->delete();
    }

}