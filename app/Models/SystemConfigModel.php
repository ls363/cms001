<?php

namespace App\Models;

use App\Enums\PageEnums;
use App\Models\Base\SystemConfig;

class SystemConfigModel extends SystemConfig
{

    /**
     * 获取系统配置
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午3:53
     */
    public function get()
    {
        return $this->first();
    }

}