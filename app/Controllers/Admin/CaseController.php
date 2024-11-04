<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Enums\PageEnums;
use App\Logics\UploadsLogic;
use App\Utils\PageBar;
use App\Logics\CaseLogic;
use App\Logics\ClassifyLogic;
use Core\Request;

class CaseController extends Controller {

    protected const MODEL_ID = 4;

    public function index(){
        $input = \request()->all();
        $data = CaseLogic::getPageList($input);
        $pageBar = new PageBar();
        $page = \request()->input('page', 1);
        $data = [
            'list' => $data['list'],
            'pageBar' => $pageBar->show($data['total'], PageEnums::PAGE_SIZE, $page),
            'classList' => ClassifyLogic::getSelectOption(self::MODEL_ID),
            'input' => $input
        ];

        $this->view(__FUNCTION__, $data);
    }

    public function info(){
        $id = request()->getInt('id', 0);
        $data = ['content' => '','class_id'=>0];
        if($id > 0){
            $data = CaseLogic::getById($id);
            //封面图转换，只有一张
            $coverInfo = UploadsLogic::getUrlById($data['cover'] ?? 0);
            $data['coverPic'] = $coverInfo['url_pre'];
            $data['coverPicBig'] = $coverInfo['url'];
            $data['slideList'] = UploadsLogic::getUrlList($data['slide']);
            $data['attachmentList'] = UploadsLogic::getUrlList($data['attachment']);
        }
        $this->view('info', ['id' => $id,'info' => $data,'classList' => ClassifyLogic::getSelectOption(self::MODEL_ID)]);
    }

    public function save(){
        $data = \request()->all();
        //print_r($data);
        //print_r($_REQUEST);exit;

        //保存图片备注
        if(array_key_exists('slide_remark', $data)){
            //批量保存图片备注
            UploadsLogic::batchsaveRemark($data['slide'], $data['slide_remark']);
            unset($data['slide_remark']);
        }

        $id = isset($data['id']) ? $data['id'] : 0;
        CaseLogic::save($id, $data);
        $message = $id > 0 ? '修改成功' : '添加成功';
        $this->success([], $message);
    }

    public function delete(){
        $id = I('get.id', 0);
        CaseLogic::delete($id);
        $this->success();
    }

    /**
     * 设置上下架状态
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午2:25
     */
    public function setStatus(){
        $id = I('id', 0);
        $state = I('state', 0);
        CaseLogic::setState($id, $state);
        $this->success();
    }

    /**
     * 设置上下架状态
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午2:25
     */
    public function setField(){
        $id = I('id', 0);
        $field = I('field');
        $value = I('value', 0);
        CaseLogic::setField($id, $field, $value);
        $this->success();
    }

    /**
     * 移动
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午2:25
     */
    public function move(){
        $ids = I('ids');
        $class_id = I('class_id', 0);
        if(empty($ids)){
            $this->error('请选择记录');
        }
        if($class_id == 0){
            $this->error('请选择分类');
        }
        CaseLogic::move($ids, $class_id);
        $this->success();
    }

    /**
     * 批量删除
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/27 下午2:25
     */
    public function batchDelete(){
        $ids = I('ids');
        if(empty($ids)){
            $this->error('请选择记录');
        }
        CaseLogic::batchDelete($ids);
        $this->success();
    }



}