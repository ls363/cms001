<?php
namespace Core;

/**
 * Class BaseModel
 * 基本实现了Laravel DB类的功能
 *
 * @package Core
 * @author lichunguang 153102250@qq.com
 * @since 2022/8/18 下午8:56
 */
class BaseModel{

    protected $table = '';
    protected $db = null;
    protected $tableList = [];

    public function __construct(){
        $this->db = DbQueryBuilder::getInstance();
        //为防止意外，每次都重设表名
        $this->getTableName();
    }

    private static $_instance = [];

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
    public static function getInstance(...$args)
    {
        $className = static::class;
        if (! isset(self::$_instance[$className])) {
            self::$_instance[$className] = new static(...$args);
        }
        //为防止意外，每次都重设表名
        self::$_instance[$className]->getTableName();
        return self::$_instance[$className];
    }

    public function __call($name, $arguments){
        //$this->db->table($this->getTableName());
        if($name == 'with'){
            $relations = $arguments[0];
            $closure = $arguments[1] ?? null;
            return $this->dealWith($relations, $closure);
        }
        return $this->db->{$name}(...$arguments);
    }

    public static function __callStatic($name, $arguments){
        if($name == 'withStatic'){
            $relations = $arguments[0];
            $closure = $arguments[1] ?? null;
            return self::getInstance()->dealWith($relations, $closure);
        }
        return self::getInstance()->{$name}(...$arguments);
    }

    /**
     * 获取新的查询
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/18 下午8:02
     */
    public static function query(){
        return self::getInstance();
    }

    /**
     * 获取表名
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午10:17
     */
    public function getTableName(){
        if(! empty($this->table)){
            return $this->table;
        }
        $namespace = static::class;
        //以下两行，获取模型层的类名
        if(isset($this->tableList[$namespace])){
            $tableName = $this->tableList[$namespace];
        }else {
            $array = explode('\\', $namespace);
            $className = end($array);
            $className = str_replace('Model', '', $className);
            //表名是小写加下划线的，所以要转换
            $tableName = convertHump($className);
            $this->tableList[$namespace] = $tableName;
        }
        $this->db->table($tableName);
        return $tableName;
    }

    /**
     * 模型关联
     *
     * @param $namespace
     * @param $foreign_key
     * @param  string  $other_key
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/24 下午11:02
     */
    public function belongsTo($namespace, $foreign_key, $other_key='id'){
        $array = explode('\\', $namespace);
        $className = end($array);
        $className = str_replace('Model', '', $className);
        //表名是小写加下划线的，所以要转换
        $tableName = convertHump($className);
        return ['relation' => 'belongsTo', 'tableName' => $tableName, 'foreignKey' => $foreign_key, 'otherKey'=>$other_key];
    }

    /**
     * 模型关联
     *
     * @param $namespace
     * @param $foreign_key
     * @param  string  $other_key
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/24 下午11:02
     */
    public function hasOne($namespace, $foreign_key, $other_key='id'){
        $array = explode('\\', $namespace);
        $className = end($array);
        $className = str_replace('Model', '', $className);
        //表名是小写加下划线的，所以要转换
        $tableName = convertHump($className);
        return ['relation' => 'hasOne', 'tableName' => $tableName, 'foreignKey' => $foreign_key, 'otherKey'=>$other_key];
    }

    /**
     * 模型关联
     *
     * @param $namespace
     * @param $foreign_key
     * @param  string  $other_key
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/24 下午11:02
     */
    public function hasMany($namespace, $foreign_key, $other_key='id'){
        $array = explode('\\', $namespace);
        $className = end($array);
        $className = str_replace('Model', '', $className);
        //表名是小写加下划线的，所以要转换
        $tableName = convertHump($className);
        return ['relation' => 'hasMany', 'tableName' => $tableName, 'foreignKey' => $foreign_key, 'otherKey'=>$other_key];
    }

    /**
     * 模型关联 非静态调用
     *
     * @param $relations
     * @param  \Closure  $closure
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/24 下午11:32
     */
    public function dealWith($relations, \Closure $closure=null){
        //反射处理relations，并反回表名等相关参数
        $params = call_user_func([new static(), $relations]);
        $params['relations'] = $relations;
        $query = $this->db->with($params, $closure);
        return $query;
    }

    //清空表
    public function truncate(){
        $tableName = $this->getTableName();
        return $this->db->truncate($tableName);
    }

}