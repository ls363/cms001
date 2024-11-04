<?php
namespace App\Logics;

use App\Models\TagModel;

class TagLogic{

    public static function saveTag($tagStr, $modelId){
        if(empty($tagStr)){
            return;
        }
        $tagList = explode("|", $tagStr);
        $existList = TagModel::where('model_id', $modelId)->whereIn("title", $tagList)->pluck('title');
        $list = array_diff($tagList, $existList);
        log_error('tag', $list);
        if(empty($list)){
            return;
        }
        foreach ($list as $tag){
            $data = [
                'title' => trim($tag),
                'model_id' => $modelId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            TagModel::insert($data);
        }
    }
}