<?php

/**
 * Banner管理
 *
 * @author      fzs
 * @Time: 2017/07/14 15:57
 * @version     1.0 版本号
 */

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Logics\BannerLogic;
use App\Enums\BannerEnums;
use App\Logics\UploadsLogic;

class BannerController extends Controller
{


    public function getList()
    {
        //获取小程序端的百叶窗
        $data = BannerLogic::getList(1, 1);
        return ['code' => 0, 'message' => 'success', 'data' => $data];
    }

    /**
     * 列表
     */
    public function index()
    {
        $list = BannerLogic::getAll();
        $this->view('list', ['list' => $list]);
    }

    /**
     *编辑页面
     */
    public function info()
    {
        $id = $this->request->input('id', 0);
        if($id == 0){
            $info = [
                'state' => 1,
                'type' => 1
            ];
        }else{
            $info = BannerLogic::getById($id);
            if($info['file_id'] > 0){
                $file = UploadsLogic::getUrlById($info['file_id']);
                $info['picurl'] = $file['url'];
            }
        }
        $this->view('info',
            ['id'   => $id,
             'typeRange' => BannerEnums::$typeList,
             'shelfRange' => BannerEnums::$shelfRange,
             'info' => $info
            ]
        );
    }

    /**
     * 增加保存
     */
    public function setState()
    {
        $id = \request()->input('id', 0);
        $state = \request()->input('state', 0);
        BannerLogic::setState($id, $state);
        return ['code' => 0, 'message' => 'success'];
    }

    /**
     * 增加保存
     */
    public function save()
    {
        $data = request()->all();
        BannerLogic::save($data);
        return api_success();
    }

    /**
     *删除
     */
    public function delete()
    {
        $id = $this->request->input('id', 0);
        BannerLogic::delete($id);
        return api_success([],'删除成功');
    }


}
