<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Enums\PageEnums;
use App\Logics\MakeHtmlLogic;
use App\Logics\SystemLogic;
use App\Logics\TagLogic;
use App\Logics\UploadsLogic;
use App\Utils\PageBar;
use App\Logics\ProjectLogic;
use App\Logics\ClassifyLogic;
use Core\Request;

class ProjectController extends Controller {

    protected const MODEL_ID = 4;

    public function index(){
        $input = \request()->all();
        $data = ProjectLogic::getPageList($input);
        $pageBar = new PageBar();
        $page = \request()->input('page', 1);
        $data = [
            'system' => SystemLogic::getSystemCache(),
            'list' => $data['list'],
            'pageBar' => $pageBar->show($data['total'], PageEnums::PAGE_SIZE, $page),
            'classList' => ClassifyLogic::getSelectOption(self::MODEL_ID),
            'input' => $input
        ];

        $this->view(__FUNCTION__, $data);
    }

    public function info(){
        $id = request()->getInt('id', 0);
        $data = ['content' => '','class_id'=>0, 'state' => 1];
        if($id > 0){
            $data = ProjectLogic::getById($id);
            $data['slideList'] = UploadsLogic::convertPicAndRemark($data['slide'], $data['slide_remark']);
            $data['attachmentList'] = UploadsLogic::getUrlList($data['attachment']);
        }
        $this->view('info', ['id' => $id,'info' => $data,'classList' => ClassifyLogic::getSelectOption(self::MODEL_ID)]);
    }

    public function save(){
        $data = \request()->all();

        //保存图片备注
        if(array_key_exists('slide_remark', $data)){
            //批量保存图片备注
            if(empty($data['slide_remark'])){
                $data['slide_remark'] = "";
            }else {
                $data['slide_remark'] = implode("|", $data['slide_remark']);
            }
        }

        $id = isset($data['id']) ? $data['id'] : 0;
        $message = $id > 0 ? '修改成功' : '添加成功';
        $tmpId = ProjectLogic::save($id, $data);
        if($id == 0){
            $id = $tmpId;
        }
        TagLogic::saveTag($data['tags'] ?? '', self::MODEL_ID);
        //判断是否需要更新文章HTML
        $system = SystemLogic::getSystemCache();
        //如果开启静态页面生成，并且刷新方式为保存时生成
        if($system['make_html'] == 1){
            //生成内容页
            MakeHtmlLogic::makeContent($id, $data['class_id']);
            //生成列表页
            MakeHtmlLogic::makeList($data['class_id'], false);
        }
        $this->success([], $message);
    }

    public function delete(){
        $id = I('get.id', 0);
        ProjectLogic::delete($id);
        $this->success([], '删除成功');
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
        ProjectLogic::setState($id, $state);
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
        ProjectLogic::setField($id, $field, $value);
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
        ProjectLogic::move($ids, $class_id);
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
        ProjectLogic::batchDelete($ids);
        $this->success();
    }



}