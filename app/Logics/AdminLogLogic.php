<?php
namespace App\Logics;

use Core\Session;
use core\Singleton;
use App\Models\AdminLogModel;
use App\Enums\ErrorEnums;
use Core\WebSession;
use App\Utils\PasswordUtils;

class AdminLogLogic{

    use Singleton;

    public function getPageList(){
        return AdminLogModel::getInstance()->getPageList();
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
        return AdminLogModel::getInstance()->where('id', $id)->delete();
    }

}