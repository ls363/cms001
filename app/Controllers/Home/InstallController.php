<?php

namespace App\Controllers\Home;

use App\Controllers\Controller;
use App\Facades\Db;
use App\Logics\SystemLogic;
use App\Utils\FileUtils;
use App\Utils\PasswordUtils;
use Core\Env;

class InstallController extends Controller
{

    public function index()
    {
        $data = ['system_name' => env('APP_NAME', 'CMS001'), 'srand_time' => '?stime='.time()];
        $this->view('/home/install/index', $data);
    }

    /**
     * 确认必备组件
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/8 下午4:32
     */
    public function config(){
        $data = [
            'http' => $_SERVER['REQUEST_SCHEME'] ?? 'http',
            'system_name' => env('APP_NAME', 'CMS001'),
            'site_name' => 'CMS001',
            'site_domain' => $_SERVER['HTTP_HOST'],
            'db_host'   => '127.0.0.1',
            'db_name' => 'cms001',
            'db_port' => '3306',
            'db_username' => 'root',
            'db_password' => '123456',
            'db_prefix' => 'gs_',
            'db_charset' => 'utf8mb4',
            'admin_username' => 'admin',
            'admin_password' => '123456',
            'admin_dir' => getRandomString(7),
            'srand_time' => '?stime='.time()
        ];
        $this->view('/home/install/config', $data);
    }

    /**
     * 保存账号配置
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/8 下午4:32
     */
    public function save()
    {
        $input = input();
        //全部安装参数
        //默认的.env
        $data = Env::get();
        //覆盖配置参数
        $data['APP_NAME'] = $input['site_name'];
        $data['APP_URL'] = $input['http'] .'://'. $input['site_domain'];
        //覆盖数据库参数
        $data['DB_HOST'] = $input['db_host'];
        $data['DB_PORT'] = $input['db_port'];
        $data['DB_DATABASE'] = $input['db_name'];
        $data['DB_PREFIX'] = $input['db_prefix'];
        $data['DB_CHARSET'] = $input['db_charset'];
        $data['DB_USERNAME'] = $input['db_username'];
        $data['DB_PASSWORD'] = $input['db_password'];
        $data['DB_DEBUG'] = 'false';
        $data['COOKIE_DOMAIN'] = $_SERVER['HTTP_HOST'];
        $data['JWT_SECRET'] = getRandomString(64, false);
        $data['JWT_TTL'] = 86400;
        $data['JWT_ALG'] = 'HS256';
        $data['ENABLE_ROUTE_CACHE'] = 'false';
        $data['ENABLE_VIEWS_CACHE'] = 'false';
        $data['ENABLE_TEMPLATE_CACHE'] = 'false';
        $data['URL_REWRITE'] = 'false'; //默认先关闭URL重写
        $data['ADMIN_DIR'] = $input['admin_dir'] ?? '';

        //检查数据库连接
        $config = [
            'host'     => $input['db_host'],
            'database' => $input['db_name'],
            'port'     => $input['db_name'],
            'username' => $input['db_username'],
            'password' => $input['db_password'],
            'charset'  => $input['db_charset']
        ];
        $this->checkDb($config);

        $str = '';
        foreach ($data as $k => $v) {
            if ($str != '') {
                $str .= PHP_EOL;
            }
            $str .= "{$k} = {$v}";
        }

        //处理编辑器的上传图片配置
        $tpl = ROOT_PATH . '/public/static/admin/lib/ueditor/php/config_tpl.json';
        $dst = str_replace('config_tpl.json', 'config.json', $tpl);
        $content = file_get_contents($tpl);
        if(PUBLIC_URL == ''){//public为主目录的情况
            $content = str_replace('"/public/static/upload/', '"/upload/', $content);
        }else{
            $content = str_replace('"/public/static/upload/', '"/public/upload/', $content);
        }
        file_put_contents($dst, $content);

        //保存.env
        file_put_contents(ROOT_PATH . '/public/.env', $str);
        return api_success([],'配置保存成功');
    }

    //初始化数据库，这一步在save()之后执行，save很快，新建数据库可能会比较慢
    public function initDb(){
        $input = input();
        //获取不带前辍的表结构
        $sql = file_get_contents(ROOT_PATH . '/database/56_cms001.sql');
        $db_prefix = $input['db_prefix'];
        //给sql语句增加前辍, 数据库中的前辍用gs_
        $sql = str_replace("`gs_", "`{$db_prefix}", $sql);
        if(PUBLIC_URL == ''){//以public为主目录的情况
            $sql = str_replace("/public/upload", "/upload", $sql);
        }

        //处理数据库表创建
        Db::exec($sql);

        //清理表缓存
        $path = RUNTIMES_PATH . '/table';
        FileUtils::deldir($path);
        //更新系统配置
        $systemData = [
           'site_name' => $input['site_name'],
           'site_domain' => 'http://'. $input['site_domain'],
            'skin' => 'default'
        ];
        SystemLogic::save($systemData);
        //处理管理员的添加
        $sql = 'TRUNCATE '. $db_prefix .'admin';
        Db::exec($sql);
        $password = PasswordUtils::generatePassword($input['admin_password']);
        $sql ="INSERT INTO `{$db_prefix}admin` (`username`, `password`, `salt`, `real_name`, `status`, `created_at`, `updated_at`)
VALUES
	('admin', '{$password['password']}', '{$password['salt']}', '管理员', 1, '". now() ."', '". now() ."');";
        Db::exec($sql);
        //隐藏内容管理菜单
        $sql = "UPDATE {$db_prefix}menu SET status=0 WHERE id=21";
        Db::exec($sql);
        //保存安装标识
        file_put_contents(ROOT_PATH .'/public/install.lock', date('Y-m-d H:i:s'));
        return api_success([],'系统安装成功');
    }

    /**
     * 检查数据库连接
     *
     * @param $config
     * @throws \Exception
     * @author lichunguang 153102250@qq.com
     * @since 2022/10/9 上午11:44
     */
    private function checkDb($config){
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            $this->pdo = new \PDO($dsn, $config['username'], $config['password']);
        } catch (\PDOException $e) {
            throw new \Exception('数据库连接失败，请检查配置', 500);
        }
    }

}
