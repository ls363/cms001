<?php
namespace App\Models\Base;

use App\Models\Base\Uploads;

class Banner extends Model {

    function file(){
        return $this->hasOne(Uploads::class, 'file_id');
    }

}