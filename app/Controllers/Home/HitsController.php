<?php
namespace App\Controllers\Home;

use App\Controllers\Controller;
use App\Logics\PreviewLogic;

class HitsController extends Controller {

    function addHits(){
        $args = input();
        if(isset($args['id'], $args['model_id']) === false){
            return $this->error('参数错误');
        }
        $id = intval($args['id']);
        $modelId = intval($args['model_id']);
        if($id == 0 || $modelId == 0){
            return $this->error('参数错误');
        }
        $hits = PreviewLogic::addHits($id, $modelId);
        $ajax = $args['ajax'] ?? 0;
        if($ajax == 1) {
            return $this->success([]);
        }
        $html = 'document.write("'. $hits .'");';
        echo $html;
    }

}