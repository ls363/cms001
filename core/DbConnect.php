<?php
namespace Core;

class DbConnect
{
    private $pdo = null;

    private static $_instance = null;

    /**
     * 私有化默认构造方法，保证外界无法直接实例化
     */
    private function __construct()
    {

    }

    //覆盖__clone()方法，禁止克隆
    private function __clone() {

    }

    /**
     * 获取单例
     *
     * @return null
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/23 下午11:18
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new static();
            self::$_instance->connect();
        }

        return self::$_instance;
    }

    /**
     * 获取连接
     *
     * @return null
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/29 下午4:00
     */
    public function getConnect(){
        if($this->pdo == null){
            $this->connect();
        }
        return $this->pdo;
    }

    /**
     * 连接数据库
     *
     * @return $this
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午5:10
     */
    public function connect()
    {
        if ($this->pdo) {
            return $this;
        }
        $config = ConfigCls::getInstance()->get('database');
        try {
            $options = [
                \PDO::ATTR_TIMEOUT => 10,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']};";
            //exit;
            $this->pdo = new \PDO($dsn, $config['username'], $config['password'], $options);
        } catch (\PDOException $e) {
            if($e->getCode() == 1049){
                throw new \Exception('数据库不存在，请先创建', 500);
            }
            throw new \Exception($e->getMessage(), 500);
            //exit('连接数据库失败：' . $e->getMessage());
        }
        //数据库有错误，抛出异常的设置
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $this;
    }

    /**
     * 关闭数据库连接
     **
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/17 上午11:51
     */
    public function close(){
        $this->pdo = null;
    }

}