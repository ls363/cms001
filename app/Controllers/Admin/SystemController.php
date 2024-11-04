<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Enums\BannerEnums;
use App\Logics\SystemLogic;
use App\Logics\UploadsLogic;
use App\Utils\FileUtils;
use Core\Env;

class SystemController extends Controller {

    /**
     * 显示页面
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 下午2:44
     */
    public function index(){
        $system = SystemLogic::getSystemCache();
        //获取编辑页的数据
        $info = [
            'enable_template_cache'=> config('enable_template_cache'),
            'enable_views_cache'  => config('enable_views_cache'),
            'enable_route_cache' => config('enable_route_cache'),
            'url_rewrite' => config('route.url_rewrite'),
            'make_html' => $system['make_html'],
        ];
        $id = $info['id'] ?? 0;
        $this->view('index', ['info' => $info, 'id' => $id,'contentMakeRange' => BannerEnums::$contentMakeRange, 'stateRange' => BannerEnums::$stateRange]);
    }

    //保存到.env
    public function saveEnv(){
        $system_data = [
            'id' => 0,
            'make_html' => input('make_html', 0)
        ];
        SystemLogic::save($system_data);
        $data = Env::get();
        $data['ENABLE_ROUTE_CACHE'] = input('enable_route_cache') == 1 ? "true" : "false";
        $data['ENABLE_VIEWS_CACHE'] = input('enable_views_cache') == 1 ? "true" : "false";
        $data['ENABLE_TEMPLATE_CACHE'] = input('enable_template_cache') == 1 ? "true" : "false";
        $data['URL_REWRITE'] = input('url_rewrite') == 1 ? "true" : "false";
        $str = '';
        foreach ($data as $k=>$v){
            if($str != ''){
                $str .= PHP_EOL;
            }
            $str .= "{$k} = {$v}";
        }
        //保存.env
        file_put_contents(ROOT_PATH .'/public/.env', $str);
        return api_success([],'系统设置保存成功, 请退出重新登录');
    }

    /**
     * 显示页面
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 下午2:44
     */
    public function info(){
        //获取编辑页的数据
        $info = SystemLogic::getInfoForEdit();
        $id = $info['id'] ?? 0;
        $dir = TEMPLATE_PATH;
        $files = scandir($dir);
        $skinList = [];
        if(! empty($files)){
            foreach ($files as $file) {
                if(strpos($file, '.') === false){
                    $skinList[] = $file;
                }
            }
        }
        $this->view('info', ['info' => $info, 'skinList' => $skinList, 'id' => $id]);
    }

    /**
     * 保存数据
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 下午2:45
     */
    public function save(){
        $data = \request()->all();
        try {
            SystemLogic::save($data);
            //清除模板缓存
            $path = RUNTIMES_PATH . '/template_cache';
            FileUtils::deldir($path);
            $this->success([], '站点信息保存成功');
        }catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * 缓存页面
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/23 下午2:44
     */
    public function cache(){
        //获取编辑页的数据
        $id = $info['id'] ?? 0;
        $this->view('cache', ['id' => $id]);
    }

    //清理所有缓存
    public function clearAll(){
        $path = RUNTIMES_PATH;
        FileUtils::deldir($path);
        return $this->success();
    }

    /**
     * 清除路由缓存
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/16 上午12:30
     */
    public function clearRoute(){
        $path = RUNTIMES_PATH . '/route';
        FileUtils::deldir($path);
        return $this->success();
    }

    /**
     * 清除路由缓存
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/16 上午12:30
     */
    public function clearView(){
        $path = RUNTIMES_PATH . '/views_cache';
        FileUtils::deldir($path);
        return $this->success();
    }


    /**
     * 清除路由缓存
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/16 上午12:30
     */
    public function clearTemplate(){
        $path = RUNTIMES_PATH . '/template_cache';
        FileUtils::deldir($path);
        return $this->success();
    }

    /**
     * 清除路由缓存
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/16 上午12:30
     */
    public function clearData(){
        $path = RUNTIMES_PATH . '/cache';
        FileUtils::deldir($path);
        return $this->success();
    }

    /**
     * 清除路由缓存
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/16 上午12:30
     */
    public function clearTable(){
        $path = RUNTIMES_PATH . '/table';
        FileUtils::deldir($path);
        return $this->success();
    }





}