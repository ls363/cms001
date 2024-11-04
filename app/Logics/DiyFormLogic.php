<?php
namespace App\Logics;

use App\Enums\PageEnums;
use App\Facades\Db;
use App\Models\Base\DiyFormField;
use Core\Session;
use core\Singleton;
use App\Models\Base\DiyForm;
use App\Enums\ErrorEnums;
use Core\WebSession;
use App\Utils\PasswordUtils;

class DiyFormLogic{

    use Singleton;

    public function getPageList($input=[]){
        $query = DiyForm::query();
        $query->orderBy('id', 'desc');
        $result = $query->paginate(PageEnums::PAGE_SIZE, ['*'], isset($input['page']) ? $input['page'] : 1);
        $list = $result['list'];
        return [
            'list' => $list,
            'page' => $result['page'],
            'total' => $result['total'],
            'page_size' => $result['pageSize']
        ];
    }

    public function getDataList($input=[]){
        $formId = $input['form_id'] ?? 0;
        $formInfo = DiyForm::find($formId);
        $fields = $this->getFormFields($formId);
        $result = Db::table($formInfo['form_table'])->paginate(PageEnums::PAGE_SIZE, ['*'], isset($input['page']) ? $input['page'] : 1);
        $list = $result['list'];
        return [
            'fields' => $fields,
            'list' => $list,
            'page' => $result['page'],
            'total' => $result['total'],
            'page_size' => $result['pageSize']
        ];
    }

    //获取表单字段，按的sort,顺序排列
    public function getFormFields(int $formId){
        $list = DiyFormField::query()->where('form_id', $formId)->orderBy('sort', 'asc')->get();
        return $list;
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
        return DiyForm::getInstance()->where('id', $id)->delete();
    }

    /**
     * 自定义表单保存
     *
     * @author lichunguang
     * @since 2024/2/26 00:40
     * @return void
     */
    public function saveFront($args = []){
        $formId = $args['form_id'];
        $formInfo = DiyForm::find($formId);
        $list = $this->getFormFields($formId);
        $data = [] ;
        foreach ($list as $v){
            //必填项验证
            if($v['require'] == 1){
                if(empty($args[$v['field_input']]) && $args[$v['field_input']] !== 0){
                    throw new \Exception($v['field_name'].' 不能为空');
                }
            }
            if(isset($args[$v['field_input']])){
                $data[$v['field_input']] = $args[$v['field_input']];
            }
        }
        $id = Db::table($formInfo['form_table'])->insert($data);
        if($id < 0){
            throw new \Exception('数据保存失败');
        }
        return $formInfo['success_message'];
    }

}