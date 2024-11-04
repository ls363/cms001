<?php

namespace App\Models;

use App\Enums\PageEnums;
use App\Models\Base\DiyForm;

class DiyFormModel extends DiyForm
{

    /**
     * 根据ID查询
     *
     * @param  int  $id
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午3:53
     */
    public function getById(int $id)
    {
        return $this->where('id', $id)->first();
    }

}