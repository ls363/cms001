<?php
namespace App\Controllers;

use App\Logics\SystemLogic;
use core\ConfigCls;
use Core\Request;
use Core\View;

class Controller {

    protected $system = null;
    protected $request = null;
    protected $commonViewData = [];


    function __construct(){
        if(HAS_INSTALL) {
            $this->system = SystemLogic::getSystemCache();
        }
    }

    public function assign(...$args){
        if(count($args) == 2){
            $this->commonViewData[$args[0]] = $args[1];
        }else{
            $this->commonViewData = array_merge($this->commonViewData, $args);
        }
    }

    public function setRequest(Request $requst){
        $this->request = $requst;
    }

    /**
     * 显示视图层页面, 先编译, 暂时实时读取
     *
     * @param  string  $viewPath
     * @param  array  $data
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 上午11:23
     */
    public function view(string $viewPath, array $data = []){
        //取根目录的模板, 原始路径，不做转换
        if(substr($viewPath, 0, 1) == '/'){
            $viewPath = ltrim($viewPath, '/');
        }else {
            $className = static::class;
            $className = str_replace(ConfigCls::getInstance()->get('controller_namespace'), '', $className);
            $array = explode('\\', $className);
            $folder = ''. convertHump($array[0]) . '/' . convertHump(str_replace('Controller', '', $array[1])) .'/';
            $viewPath = $folder .$viewPath;
        }
        View::getInstance()->display($viewPath, $data);
        $data = null;
    }

    /**
     * 显示视图层页面, 纯PHP模板使用，前台测试用的
     *
     * @param  string  $viewPath
     * @param  array  $data
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 上午11:23
     */
    public function display(string $viewPath, array $data = []){
        //取根目录的模板
        if(substr($viewPath, 0, 1) == '/'){
            $folder = '';
        }else {
            $className = static::class;
            $className = str_replace(ConfigCls::getInstance()->get('controller_namespace'), '', $className);
            $array = explode('\\', $className);
            $folder = '/'. convertHump($array[0]) . '/' . convertHump(str_replace('Controller', '', $array[1])) .'/';
        }
        if(! empty($data)){
            extract($data);
        }
        //$path = ROOT_PATH ."/resource/views{$folder}{$viewPath}.php";
        //View::getInstance()->debugTag($path);
        include APP_PATH ."/Views{$folder}{$viewPath}.php";
    }

    /**
     * 成功的返回
     *
     * @param  int  $code
     * @param  array  $data
     * @param  string  $message
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午12:21
     */
    public function success(array $data = [], $message='success', $code=200){
        header('Content-Type:application/json; charset=utf-8');
        $json = [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
        echo json_encode($json);
    }

    /**
     * 失败的返回
     *
     * @param  string  $message
     * @param  int  $code
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午12:21
     */
    public function error($message='failed', $code=500){
        header('Content-Type:application/json; charset=utf-8');
        $json = [
            'code' => $code,
            'message' => $message,
        ];
        echo json_encode($json);
    }

    /**
     * JSON输出
     *
     * @param  array  $data
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午12:18
     */
    public function json(array $data){
        echo json_encode($data);
    }

}
