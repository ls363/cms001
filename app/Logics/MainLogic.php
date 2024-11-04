<?php
namespace App\Logics;

use App\Models\ContentModuleModel;
use Core\Session;
use core\Singleton;
use App\Models\AdminModel;
use App\Enums\ErrorEnums;
use Core\WebSession;
use App\Utils\PasswordUtils;
use Core\Validator;
use Core\Request;
use App\Facades\Db;

class MainLogic{

    use Singleton;

    /**
     * 获取模块的名称和信息数量
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/16 下午3:13
     */
    public function getModuleList(){
        $list = ContentModuleModel::where('type', 2)->get(['id', 'title', 'table']);
        foreach ($list as & $v){
            $v['num'] = Db::table($v['table'])->count();
        }
        return $list;
    }

    /**
     * 获取服务器信息
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午4:03
     */
    public function getServerInfo(){
        $server = Request::getInstance()->server();
        $data = [
            'soft' => $server['SERVER_SOFTWARE'], //服务器环境
            'ip' => $server['SERVER_ADDR'],//服务器IP地址
            'port' => $server['SERVER_PORT'],   //端口
            'root' => $server['DOCUMENT_ROOT'],
            'domain' => $server['HTTP_HOST'], //服务器域名
            'php_version' => phpversion(), //PHP版本
            'mysql_version' => Db::version(), //数据库信息
            'time' => date('Y-m-d H:i:s')//服务器当前时间
        ];
        return $data;
    }

    /**
     * 退出登录
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/29 下午8:18
     */
    public function logout(){
        Session::getInstance()->remove(config('admin_jwt_token_name'));
        redirect(url('login/index'));
    }

    /**
     * 获取当前用户的信息
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午1:49
     */
    public function getUserInfo(){
        $adminId = context('adminId');
        //以下两种方式都可以用
        return AdminModel::getInstance()->find($adminId);
        //return AdminModel::find($adminId);
    }

    /**
     * 保存当前用户信息
     *
     * @param  array  $data
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午3:07
     */
    public function saveUserInfo(array $data){
        $adminId = context('adminId');
        return AdminModel::where('id', $adminId)->update($data);
    }


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
        if($verifyCode != WebSession::getInstance()->get(config('session_auth_num_key'))){
            throw new \Exception(ErrorEnums::LOGIN_VERIFY_ERROR, ErrorEnums::DEFAULT_ERROR);
        }
        $userInfo = AdminModel::getInstance()->getByUserName($username);
        if(empty($userInfo)){
            throw new \Exception(ErrorEnums::ADMIN_NOT_FOUND, ErrorEnums::DEFAULT_ERROR);
        }
        //$data = PasswordUtils::generatePassword('lcg22580');
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
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/28 下午5:00
     */
    public function changePassword(string $originPassword, string $newPassword, string $confirmPassword){
        $userId = get_admin_user_id();
        $data = [
            'origin_password' => $originPassword,
            'new_password' => $newPassword,
            'confirm_password' => $confirmPassword
        ];
        $validator = Validator::getInstance()->make(
            $data,
            [
                'origin_password' => 'required|between:6,16',
                'new_password' => 'required|between:6,16',
                'confirm_password' => 'required|between:6,16|same:new_password'
            ],
            [
                'origin_password.required' => ErrorEnums::ORIGIN_PASSWORD_REQUIRED,
                'new_password.required' => ErrorEnums::NEW_PASSWORD_REQUIRED,
                'confirm_password.required' => ErrorEnums::CONFIRM_PASSWORD_REQUIRED,
                'origin_password.between' => ErrorEnums::ORIGIN_PASSWORD_LENGTH,
                'new_password.between' => ErrorEnums::NEW_PASSWORD_LENGTH,
                'confirm_password.between' => ErrorEnums::CONFIRM_PASSWORD_LENGTH,
                'confirm_password.same' => ErrorEnums::PASSWORD_NOT_MATCH,
            ]
        );
        $result = $validator->fails();
        if(! empty($result)){
            throw new \Exception($result, ErrorEnums::DEFAULT_ERROR);
        }
        //校验原密码
        $userInfo = AdminModel::find($userId,['password', 'salt']);
        if($userInfo['password'] != PasswordUtils::verifyPassword($originPassword, $userInfo['salt'])){
            throw new \Exception(ErrorEnums::ORIGIN_PASSWORD_ERROR, ErrorEnums::DEFAULT_ERROR);
        }
        //修改密码，生成新的盐值
        $data = PasswordUtils::generatePassword($newPassword);
        return AdminModel::getInstance()->where('id', $userId)->update($data);
    }

}