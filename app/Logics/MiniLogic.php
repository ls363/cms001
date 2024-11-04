<?php


namespace App\Logics;

use App\Cache\TokenCache;
use App\Utils\WXBizDataCrypt;
use App\Models\Base\MiniUsers;
use App\Utils\WXGetPhone;

class MiniLogic
{

    /**
     * 生成token
     *
     * @param  int  $userId
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/23 下午6:26
     */
    private function generateToken(int $userId)
    {
        //生成Token
        $token = jwt_encode(['loginId' => $userId]);
        //保存到Redis，用于登录判断
        TokenCache::add($token, $userId);
        MiniUsers::query()->where('id', $userId)->update(['api_token' => $token]);
        return $token;
    }


    /**
     * 添加或者更新用户信息
     *
     * @param  string  $openid
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/23 下午5:56
     */
    public function saveUserInfo(string $openid, array $data)
    {
        $result = MiniUsers::query()->where('openid', $openid)->first();
        if (empty($result)) {
            $result = MiniUsers::query()->newModelInstance();
            $result['openid'] = $openid;
        }
        $result->nickname = $data['nickname'];
        $result->avatar = $data['avatar'];
        if (isset($data['phone']) && !empty($data['phone'])) {
            $result->phone = $data['phone'];
        } else {
            $userInfo = UserLogic::getById($result['user_id']);
            $result->phone = $userInfo['mobile'] ?? '';
        }
        $result->save();
        return $result;
    }

    /**
     * 关联用户，生成新的Token
     *
     * @param  string  $openid
     * @param  int  $userId
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/6/1 上午10:16
     */
    public function bindUser(string $openid, int $userId, string $phone = '')
    {
        $data = ['user_id' => $userId];
        //关联用户
        $result = MiniUsers::query()->where('openid', $openid)->first();
        $enable_subscribe = 0;
        if (empty($result)) {
            $mini = new MiniUsers();
            $mini->user_id = $userId;
            $mini->openid = $openid;
            $mini->phone = $phone;
            $mini->save();
        } else {
            $enable_subscribe = empty($result->enable_subscribe) ? 0 : intval($result->enable_subscribe);
            MiniUsers::query()->where('openid', $openid)->update($data);
        }
        $token = $this->generateToken($userId);
        $result = RoleUser::query()->where('user_id', $userId)->first();
        $roleId = empty($result) ? 5 : $result->role_id;
        return [
            'code'    => 0,
            'message' => 'success',
            'data'    => [
                'is_bind'          => 1,
                'role_id'          => $roleId,
                'token'            => $token,
                'phone'            => $phone,
                'enable_subscribe' => $enable_subscribe
            ]
        ];
    }

    /**
     * 关联用户，生成新的Token
     *
     * @param  string  $openid
     * @param  int  $userId
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/6/1 上午10:16
     */
    public function bindCreateUser(string $openid, string $phone = '')
    {
        $data = [
            'real_name' => $phone,
            'user_role' => [5],
            'mobile'    => $phone,
            'sex'       => 0,
            'pwd'       => '123456',
            'dept_id'   => 3
        ];
        $result = UserLogic::store($data);
        $userId = $result['data'];
        //$data = ['user_id' => $userId];
        //关联用户
        $mini = MiniUsers::query()->where('openid', $openid)->first();
        if (empty($mini)) {
            $model = new MiniUsers();
            $model->openid = $openid;
            $model->user_id = $userId;
            $model->save();
        }
        $token = $this->generateToken($userId);
        $result = RoleUser::query()->where('user_id', $userId)->first();
        $roleId = empty($result) ? 5 : $result->role_id;
        return ['code' => 0, 'message' => 'success',
                'data' => ['is_bind' => 1, 'role_id' => $roleId, 'token' => $token, 'phone' => $phone]];
    }

    /**
     * 新的账号密码登录
     *
     * @param $code
     * @param $userName
     * @param $password
     * @return array|string
     * @author lichunguang 153102250@qq.com
     * @since 2022/7/28 下午2:49
     */
    public function accountLogin($code, $userName, $password)
    {
        $result = User::query()->where('mobile', $userName)
            ->first();
        if (empty($result)) {
            return ['code' => ErrorEnum::DEFAULT_ERROR, 'message' => ErrorEnum::LOGIN_ERROR];
        }

        //实例化小程序解密类
        $pc = new WXBizDataCrypt(config('mini.app_id'), config('mini.app_secret'));
        //获取session_key及openid
        $session = $pc->codeToSession($code);
        $openid = $session['openid'];
        return $this->bindUser($openid, $result->id, $result['mobile']);
    }

    /**
     * 新的手机号登录
     *
     * @param  string  $openid
     * @param  string  $code
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/23 下午6:05
     */
    public function wechatLogin(string $codeOpenid, string $codeMobile)
    {
        //实例化小程序解密类
        $pc = new WXBizDataCrypt(config('mini.app_id'), config('mini.app_secret'));
        //获取session_key及openid
        $session = $pc->codeToSession($codeOpenid);
        $openid = $session['openid'];
        //获取手机号
        $phoneInfo = WXGetPhone::getPhone($codeMobile);
        $phone = $phoneInfo['purePhoneNumber'];
        $miniUser =  MiniUsers::query()->where('openid', $openid)->first();
        $userId = $miniUser['id'];
        $token = $this->generateToken($userId);
        //更新手机号
        MiniUsers::query()->where('openid', $openid)->update(['phone' => $phone]);
        return $token;
    }

    /**
     * 绍电问卷登录，用户表 mini_users
     *
     * @param  string  $openid
     * @param  string  $code
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/23 下午6:05
     */
    public function login(array $input)
    {
        //实例化小程序解密类
        $pc = new WXBizDataCrypt(config('mini.app_id'), config('mini.app_secret'));
        //获取session_key及openid
        $session = $pc->codeToSession($input['code']);
        if(! isset($session['openid'])){
            log_error('session', var_export($session, true));
            return api_error('API获取openid异常');
        }
        $openid = $session['openid'];

        //更新手机号
        $userData = [
            'avatar' => $input['avatar'],
            'nickname' => $input['nickname'],
            'dept'=> $input['dept'],
            'employee' => $input['employee']
        ];
        $miniUser =  MiniUsers::query()->where('openid', $openid)->first();
        if(empty($miniUser)){
            $userData['openid'] = $openid;
            $userId = MiniUsers::query()->insert($userData);
        }else{
            MiniUsers::query()->where('openid', $openid)->update($userData);
            $userId = $miniUser['id'];
        }
        $token = $this->generateToken($userId);
        return $token;
    }
}
