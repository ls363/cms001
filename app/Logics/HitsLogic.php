<?php
namespace App\Logics;

use App\Models\Base\ContentModule;

class HitsLogic{

    public static function addHits($id, $modelId){
        $moduleInfo = ContentModule::find($modelId);

    }

}