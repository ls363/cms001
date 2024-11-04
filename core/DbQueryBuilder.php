<?php

namespace Core;

use App\Enums\PageEnums;


class DbQueryBuilder
{
    private $pdo = null;
    private $sql = '';
    private $fields = [];
    private $condition = [];
    private $tableName = '';
    private $groupBy = '';
    private $orderBy = [];
    private $limitValue = 0;
    private $having = '';
    private $keyBy = '';
    private $offsetValue = 0;
    private $withParams = [];
    //以下两个都是用作join的，有join table表必须有别名
    private $aliasParams = ''; //主查询表的别名
    private $joinParams = [];
    private $aliasList = [];    //别名和真实表的映射

    //缓存运行时的表结构
    private $tableFieldType = [];
    //处理with查询的时候
    private $queryWith = false;
    //处理分页的时候
    private $queryPager = false;

    private $debug = false;

    public function __construct(){
    //    $this->pdo = DbQueryBuilder::getInstance();
        $this->debug = config('database.debug');
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
        if($this->pdo == null) {
            //获取数据库连接
            $this->pdo = DbConnect::getInstance()->getConnect();
        }
        return $this;
    }

    public function __call($name, $arguments){
        //$this->db->table($this->getTableName());
        if($name == 'table'){
            return $this->parseTable($arguments[0]);
        }
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
        return (new static())->{$name}(...$arguments);
    }

    /**
     * 获取本实例
     *
     * @return null
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/23 下午11:18
     */
    public static function getInstance(...$args)
    {
        return new static(...$args);
        /*
        if (! isset(self::$_instance)) {
            self::$_instance = new static(...$args);
        }
        return self::$_instance;
        */
    }

    public function parseTable($table){
        $this->tableName = $table;
        return $this;
    }


    /**
     * 原始字符串，如hits = hits + 1这种，不需要做处理
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/25 上午10:58
     */
    public function raw($sql){
        return 'raw:'.$sql;
    }

    //跟在Model后面，或者 table()后面，table as
    public function alias($alias){
        $this->aliasParams = $alias;
        return $this;
    }

    /**
     * join参数, 表名不需要加前辍, 先暂存，用于多个join
     *
     * @param $table
     * @param $condition
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/28 下午8:03
     */
    public function join(string $table, string $alias, string $first_field, string $second_field, string $join_type='INNER'){
        $this->joinParams[] = [
            'table' => $table,
            'alias' => $alias,
            'first_field' => $first_field,
            'second_field' => $second_field,
            'join_type' => $join_type
        ];
        return $this;
    }

    /**
     * 获取join的SQL
     *
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/28 下午8:09
     */
    public function getJoinSql(){
        if(empty($this->joinParams)){
            return '';
        }
        $sql = '';
        $t1 = $this->aliasParams;
        foreach($this->joinParams as $v){
            $tableName = config('database.prefix') . $v['table'];
            $table =  $tableName. ' '. $v['alias'];
            //join表的映射，where条件处理的时候，要用
            $this->aliasList[$v['alias']] = $tableName;
            //处理表字段，用于查询的较验
            if(! isset($this->tableFieldType[$tableName])) {
                $this->tableFieldType[$tableName] = $this->getTableFieldType($v['table']);
            }
            $sql .= " {$v['join_type']} JOIN {$table} ON {$t1}.{$v['first_field']} = {$v['alias']}.{$v['second_field']} ";
        }
        return $sql;
    }

    /**
     * 该函数用于测试DB类
     *
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/24 下午8:13
     */
    public function getTable(){
        return $this->tableName;
    }

    /**
     * 清除当前的查询条件
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午5:10
     */
    public function clear($with = true)
    {
        $this->sql = '';
        $this->fields = [];
        $this->condition = [];
        if($this->queryPager === false){
            $this->tableName = '';
        }

        $this->groupBy = '';
        $this->orderBy = [];
        $this->limitValue = '';
        $this->having = '';
        $this->keyBy = '';
        $this->offsetValue = 0;
        $this->aliasParams = '';
        $this->aliasList = [];
        $this->joinParams = [];
        if($with) {//清除with
            $this->clearWith();
        }
    }

    public function clearWith(){
        if(! empty($this->withParams)){
            $this->withParams = [];
        }
    }


    //开始事务
    public function beginTransaction()
    {
        $this->connect();
        $this->pdo->beginTransaction();     // 启动一个事务
    }

    public function beginTran()
    {
        $this->connect();
        $this->pdo->beginTransaction();     // 启动一个事务
    }

    //提交事务
    public function commit()
    {
        $this->pdo->commit();     // 启动一个事务
    }

    //回滚操作
    public function rollBack()
    {
        $this->pdo->rollBack();     // 启动一个事务
    }

    //事务操作, 测试中，未启用
    public function trans()
    {
        $this->connect();
        try {
            $this->pdo->beginTransaction();     // 启动一个事务
            $this->pdo->exec("insert into order_info (order_no) values ('1234');");
            $this->pdo->exec("insert into order_detail(order_id, order_amount) values (1, 123);");
            $this->pdo->commit();               // 提交事务
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            echo "事务失败,自动回滚: " . $e->getMessage() . "<br>";
        }
    }


    /**
     * 查看表信息
     *
     * @param  string  $tableName
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午9:18
     */
    public function desc(string $tableName=''){
        $sql = "desc ". config('database.prefix') . $tableName;
        if($this->debug){
            log_error('sql', $sql);
        }
        $this->connect();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $allRow = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $allRow;
    }

    public function truncate(string $tableName=''){
        $sql = "truncate ". config('database.prefix') . $tableName;
        return $this->exec($sql);
    }

    /**
     * 获取表的字段类型
     *
     * @param  string  $tableName
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午9:28
     */
    public function getTableFieldType(string $tableName='', $isRefresh=false){
        $file = RUNTIMES_PATH . '/table' . '/'. $tableName . '.php';
        //从文件读取数据
        $data = file_to_array($file);
        if(empty($data) || $isRefresh) {
            $fieldList = $this->desc($tableName);
            $stringFieldList = ['varchar', 'timestamp', 'text', 'longtext', 'datetime', 'date'];
            $list = [];
            foreach ($fieldList as $v) {
                $iPos = strpos($v['Type'], '(');
                $type = $iPos > 0 ? substr($v['Type'], 0, $iPos) : $v['Type'];
                $quote = in_array($type, $stringFieldList) ? true : false;
                $list[$v['Field']] = ['type' => $type, 'quote' => $quote];
            }
            //将数组写到缓存文件
            array_to_file($list, $file);
            return $list;
        }
        return $data;
    }

    /**
     * 获取全部
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/26 上午9:26
     */
    public function fetchAll(){
        $sql = $this->getSql();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 输出提示
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午6:29
     */
    public function tip()
    {
        $dbh = $this->pdo;
        // 调用getAttribute()可以获得所有属性名称对应的值.
        echo "是否关闭自动提交: " . $dbh->getAttribute(PDO::ATTR_AUTOCOMMIT) . "<br />";
        echo "PDO错误处理模式: " . $dbh->getAttribute(PDO::ATTR_ERRMODE) . "<br />";
        echo "表字段字符的大小写转换: " . $dbh->getAttribute(PDO::ATTR_CASE) . "<br />";
        echo "连接状态相关的信息: " . $dbh->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "<br />";
        echo "空字符串转换SQL的NULL: " . $dbh->getAttribute(PDO::ATTR_ORACLE_NULLS) . "<br />";
        echo "应用程序提前获取数据大小: " . $dbh->getAttribute(PDO::ATTR_PERSISTENT) . "<br />";
    }

    /**
     * 设置分组
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/25 上午11:27
     */
    public function groupBy($fields){
        $this->groupBy = $fields;
        return $this;
    }

    /**
     * 设置游标
     *
     * @param $tableName
     * @return $this
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午6:19
     */
    public function offset($offset)
    {
        $this->offsetValue = $offset;
        return $this;
    }

    /**
     * 设置游标
     *
     * @param $rgs
     * @return $this
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午6:19
     */
    public function limit(...$args)
    {
        $num = count($args);
        if($num == 1){
            $this->limitValue = $args[0];
        }else {
            $this->offsetValue = $args[0];
            $this->limitValue = $args[1];
        }
        return $this;
    }


    /**
     * 支持多种方式的参数，如果两个, 将where条件转为一维数组
     *
     * @param  mixed  ...$args
     * @return $this
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午6:27
     */
    public function where(...$args)
    {
        if(empty($args)){
            return $this;
        }
        $num = count($args);
        if($num == 1){
            $value = $args[0];
            if (is_string($value)) {
                $this->condition[] = $value;
            } else {//数组的情况
                foreach ($value as $v){
                    if(is_string($v)) {
                        $this->condition[] = $v;
                    }else{
                        $operator = $num === 2 ? '=' : $v[1];
                        $this->condition[] = [$v[0], $operator, end($v)];
                    }
                }
            }
        }else{
            $operator = $num === 2 ? '=' : $args[1];
            $this->condition[] = [$args[0], $operator, end($args)];
        }
        return $this;
    }

    /**
     * in查询
     *
     * @param  string  $field
     * @param  array  $params
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/26 上午8:56
     */
    public function whereIn(string $field, array $params){
        $this->condition[] = [$field, 'in', $params];
        return $this;
    }

    /**
     * between 构造
     *
     * @param  string  $field
     * @param  array  $params
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/26 上午8:56
     */
    public function whereBetween(string $field, array $params){
        $this->condition[] = [$field, 'between', $params];
        return $this;
    }


    /**
     * 设置排序
     *
     * @param  $sortField
     * @param  string  $sortType
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/26 上午10:31
     */
    public function orderBy($sortField, string $sortType=''){
        if(is_array($sortField)){
            $this->orderBy = array_merge($this->orderBy, $sortField);
        }else {
            $this->orderBy[] = [$sortField, $sortType];
        }
        return $this;
    }

    /**
     * 设置字段
     *
     * @param  string|[]  $fields
     * @return $this
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午8:10
     */
    public function select($fields = ['*'])
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * 设置按哪个字段分组，返回关联数组，暂时用于多字段返回
     *
     * @param  string  $field
     * @return $this
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午8:10
     */
    public function keyBy(string $field)
    {
        $this->keyBy = $field;
        return $this;
    }

    /**
     * 获取单行的值
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午8:12
     */
    public function first($fields=['*'])
    {
        $this->limitValue = 1;
        if(empty($this->fields)){
            $this->fields = $fields;
        }
        return $this->row($this->fields);
    }

    /**
     * 快速根据ID查询
     * @param int $id
     * @param  string[]  $fields
     * @return $this
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午8:10
     */
    public function find(int $id=0, $fields = ['*'])
    {
        if($id > 0){
            $this->where('id', $id);
        }
        return $this->first($fields);
    }

    /**
     * 获取单个字段的值
     *
     * @param  string  $field
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午8:33
     */
    public function value(string $field)
    {
        $this->limitValue = 1;
        $this->fields = $field;
        return $this->row($this->fields);
    }

    public function column(string $columnName){
        return $this->pluck($columnName);
    }

    /**
     * 获取单列的值
     *
     * @param  string $columnName
     * @param string $keyName
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午8:33
     */
    public function pluck(string $columnName, string $keyName='')
    {
        if(empty($columnName)){
            $columnName = "*";
        }
        $columnArr = [];
        if(strpos($columnName, ',')){
            $columnArr = explode(',', $columnName);
            $field = $columnArr;
        }else{
            $field = [$columnName];
        }
        if(! empty($keyName)){
            $field[] = $keyName;
        }
        $this->fields = $field;
        $sql = $this->getSql();
        if(empty($this->pdo)){
            $this->connect();
        }
        $res = $this->pdo->prepare($sql);//准备查询语句
        $res->execute();
        $list = [];
        while ($result = $res->fetch(\PDO::FETCH_ASSOC)) {
            if(empty($keyName)){
                $list[] = $result[$columnName];
            }else{
                if(empty($columnName) || $columnName == '*'){
                    $list[$result[$keyName]] = $result;
                }else {
                    if(empty($columnArr)){
                        $list[$result[$keyName]] = $result[$columnName];
                    }else{
                        foreach ($columnArr as $c){
                            $list[$result[$keyName]][$c] = $result[$c];
                        }
                    }
                }
            }
        }
        $this->clear();
        return $list;
    }


    /**
     * 获取以key分组的列表
     *
     * @param  string $columnName
     * @param string $keyName
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午8:33
     */
    public function keyList(string $keyName, array $fields=['*'])
    {
        //先使用select
        if(empty($this->fields)){
            $this->fields = $fields;
        }
        $sql = $this->getSql();
        if(empty($this->pdo)){
            $this->connect();
        }
        $res = $this->pdo->prepare($sql);//准备查询语句
        $res->execute();
        $list = [];
        while ($result = $res->fetch(\PDO::FETCH_ASSOC)) {
            $list[$result[$keyName]][] = $result;
        }
        $this->clear();
        return $list;
    }


    /**
     * 统计数量
     *
     * @param  string  $fields
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午8:43
     */
    public function count(string $fields = '*')
    {
        $this->fields = "COUNT({$fields})";
        return $this->row($this->fields);
    }

    /**
     * 最大值
     *
     * @param  string  $fields
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午8:43
     */
    public function max(string $fields)
    {
        $this->fields = "MAX({$fields})";
        return $this->row($this->fields);
    }

    /**
     * 最小值
     *
     * @param  string  $fields
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午8:43
     */
    public function min(string $fields)
    {
        $this->fields = "MIN({$fields})";
        return $this->row($this->fields);
    }

    /**
     * 统计数量
     *
     * @param  string  $fields
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午8:43
     */
    public function sum(string $fields)
    {
        $this->fields = "SUM({$fields})";
        return $this->row($this->fields);
    }

    /**
     * 平均值
     *
     * @param  string  $fields
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午8:43
     */
    public function average(string $fields)
    {
        $this->fields = "AVERAGE({$fields})";
        return $this->row($this->fields);
    }


    /**
     * 获取查询的结果
     *
     * @param  string[]  $fields
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午8:42
     */
    public function get($fields = ['*'])
    {
        //select优化，select没有才使用get
        if(empty($this->fields)) {
            $this->fields = $fields;
        }
        $key = $this->keyBy;
        $sql = $this->getSql();
        //记录SQL日志
        //echo $sql .PHP_EOL;
        //exit;
        if(empty($this->pdo)){
            $this->connect();
        }
        $res = $this->pdo->prepare($sql);//准备查询语句
        $res->execute();
        $list = [];
        while ($result = $res->fetch(\PDO::FETCH_ASSOC)) {
            if(empty($key)) {
                $list[] = $result;
            }else{
                $list[$result[$key]] = $result;
            }
        }
        //正在处理with语句
        if($this->queryWith){
            $this->clear(false);
            return $list;
        }
        //查询结果为空，直接返回
        if(empty($list)){
            $this->clear(false);
            return [];
        }
        //没with查询
        if(empty($this->withParams)){
            $this->clear(false);
            return $list;
        }

        //先清除一次
        $this->clear(false);

        //处理with查询
        $this->dealWith($list, 'list');
        $this->queryWith = false;
        $this->clearWith();
        return $list;
    }

    /**
     * 处理with查询
     *
     *
     * @param $result 结果集
     * @param  string  $resultType 数组 还是 对象， 列表还是单个
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/25 上午12:57
     */
    function dealWith(& $result, $resultType='list'){
        $this->queryWith = true;
        foreach ($this->withParams as $item){
            $p = $item['params'];
            $table = $p['tableName'];
            $relations = $p['relations'];
            $fk = $p['foreignKey'];
            //print_r($p);
            $this->queryWith = true;
            if($resultType == 'list') {
                $ids = array_column($result, $fk);
                $ids = array_unique($ids);
            }else{
                $ids = [$result[$fk]];
            }

            $closure = $item['closure'];
            //预留字段，处理查询关系
            $relation = $p['relation'];
            $query = $this->table($table)->whereIn('id', $ids);
            if(! empty($closure)) {
                $query = $closure($query);
            }
            switch ($relation){
                case 'hasOne':
                case 'belongsTo':
                    $list = $query->keyBy('id')->get();
                    break;
                case 'hasMany':
                    $list = $query->keyList('id')->get();
                    break;
            }

            //多条记录
            if($resultType == 'list') {
                foreach ($result as & $item) {
                    $item[$relations] = $list[$item[$fk]] ?? [];
                }
            }else{//单条记录
                $result[$relations] = $list[$result[$fk]] ?? [];
            }
            $this->clear(false);
        }
        $this->clearWith();
    }

    /**
     * 查询库中所有的表, 表名， 注释，数据量
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 上午9:45
     */
    public function getAllTable(){
        $dbName = config('database.database');
        $sql = "SELECT 
        TABLE_NAME 表名,TABLE_COMMENT 表注释,TABLE_ROWS 数据量
        FROM information_schema.tables
        WHERE TABLE_SCHEMA = '{$dbName}' 
        ORDER BY TABLE_NAME";
        return $this->query($sql);
    }


    /**
     * 表删除, true建表成功，false失败  可以回滚撤消
     *
     * @param  string  $fromTable
     * @param  string  $toTable
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 上午9:44
     */
    public function deleteTable(string $tableName){
        if(empty($tableName)){
            return false;
        }
        try {
            $sql = "DROP TABLE `". config('database.prefix') ."$tableName`";
            return $this->exec($sql);
        }catch (\Exception $e){
            log_exception($e);
            return false;
        }
    }

    /**
     * 表清空  truncate, true建表成功，false失败  不可以回滚撤消
     *
     * @param  string  $fromTable
     * @param  string  $toTable
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 上午9:44
     */
    public function clearTable(string $tableName){
        if(empty($tableName)){
            return false;
        }
        try {
            $sql = "TRUNCATE TABLE ". config('database.prefix') ."$tableName";
            return $this->exec($sql);
        }catch (\Exception $e){
            log_exception($e);
            return false;
        }
    }

    /**
     * 给表添加字段
     *
     * @param  string  $tableName
     * @param  array  $columns ['name' => must,'type' => must 'default'=>'optional', 'comment_optional']
     * @return false
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 上午10:07
     */
    public function addTableColumn(string $tableName, array $columns){
        if(empty($tableName) || empty($columns)){
            return false;
        }
        try {
            $sql = "ALTER TABLE `". config('database.prefix') ."$tableName` ";
            $fields = [];
            foreach ($columns as $v){
                //字段默认值
                $default_sql = '';
                $default = $v['default'] ?? '';
                if($default === 0 || $default > 0){
                    $default_sql = "DEFAULT {$default}";
                }else{
                    $default_sql = "DEFAULT '{$default}'";
                }
                //字段备注
                $comment_sql = '';
                if(isset($v['comment'])){
                    $comment_sql = "COMMENT '{$v['comment']}'";
                }
                //允许为null
                $null_sql = 'NOT NULL';
                if(isset($v['allowNull']) && $v['allowNull']){
                    $null_sql = 'NULL';
                }
                $fields[] = "ADD {$v['name']} {$v['type']} {$null_sql} {$default_sql} {$comment_sql} ";
            }
            if(empty($fields)){
                return false;
            }
            $sql .= implode(' ,', $fields);
            return $this->exec($sql);
        }catch (\Exception $e){
            log_exception($e);
            return false;
        }
    }

    /**
     * 删除表字段
     *
     * @param $tableName
     * @param  array  $columns
     * @return false
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 上午10:20
     */
    public function deleteTableColumn($tableName, array $columns){
        if(empty($tableName) || empty($columns)){
            return false;
        }
        try {
            $sql = "ALTER TABLE `". config('database.prefix') ."$tableName` ";
            $fields = [];
            foreach ($columns as $v) {
                $fields[] = "DROP COLUMN  $v";
            }
            if(empty($fields)){
                return false;
            }
            $sql .= implode(' ,', $fields);
            return $this->exec($sql);
        }catch (\Exception $e){
            log_exception($e);
            return false;
        }
    }

    /**
     * CREATE TABLE `survey_result_1` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT
     ,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
     */
    public function createTable(string $tableName, $columns=[], $comment=''){
        $sql = "CREATE TABLE `". config('database.prefix') ."$tableName` ";
        $sql .= "(`id` int(11) unsigned NOT NULL AUTO_INCREMENT";
        $fields = [];
        if(! empty($columns)) {
            foreach ($columns as $v) {
                //字段默认值
                $default = $v['default'] ?? '';
                if ($default === 0 || $default > 0) {
                    $default_sql = "DEFAULT {$default}";
                } else {
                    $default_sql = "DEFAULT '{$default}'";
                }
                //字段备注
                $comment_sql = '';
                if (isset($v['comment'])) {
                    $comment_sql = "COMMENT '{$v['comment']}'";
                }
                //允许为null
                $null_sql = 'NOT NULL';
                if (isset($v['allowNull']) && $v['allowNull']) {
                    $null_sql = 'NULL';
                }
                $fields[] = "ADD {$v['name']} {$v['type']} {$null_sql} {$default_sql} {$comment_sql} ";
            }
        }
        if(! empty($fields)) {
            $sql .= implode(' ,', $fields);
        }
        $sql .= ",PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        if(! empty($comment)){
            $sql .= " COMMENT='{$comment}' ";
        }
        return $this->exec($sql);
    }

    /**
     * 表复制, true建表成功，false失败
     *
     * @param  string  $fromTable
     * @param  string  $toTable
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/30 上午9:44
     */
    public function copyTable(string $fromTable, string $toTable){
        try {
            $sql = "CREATE TABLE `". config('database.prefix') ."{$toTable}` LIKE `". config('database.prefix') ."{$fromTable}`";
            return $this->exec($sql);
        }catch (\Exception $e){
            log_exception($e);
            return false;
        }
    }

    /**
     * 执行原生SQL查询
     *
     * @param string $sql
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午11:07
     */
    public function exec(string $sql)
    {
        if($this->debug){
            log_error('sql', $sql);
        }
        if(empty($this->pdo)){
            $this->connect();
        }
        return $this->pdo->exec($sql);
    }

    /**
     * 递增
     *
     * @param $field
     * @param  int  $step
     * @param  array  $where
     * @return false
     * @throws \Exception
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/19 下午4:12
     */
    public function incr($field, $step=1,$where=[]){
        if(! empty($where)){
            $this->where($where);
        }
        $data = [
            $field => $this->raw("$field + $step")
        ];
        return $this->update($data);
    }

    /**
     * 递减
     *
     * @param $field
     * @param  int  $step
     * @param  array  $where
     * @return false
     * @throws \Exception
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/19 下午4:12
     */
    public function decr($field, $step=1,$where=[]){
        if(! empty($where)){
            $this->where($where);
        }
        $data = [
            $field => $this->raw("$field + $step")
        ];
        return $this->update($data);
    }

    /**
     * 执行原生SQL查询
     *
     * @param string $sql
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午11:07
     */
    public function query(string $sql){
        if(empty($this->pdo)){
            $this->connect();
        }
        // 使用 query() 完成数据查询
        try {
            $pdo_proc = $this->pdo->query($sql);
            //echo "总共查询到: {$pdo_proc->rowCount()} 条记录 <br>";
            //返回全部可用
            //return $pdo_proc->fetchAll(\PDO::FETCH_ASSOC);
            $list = [];
            //返回key+index数字
            /*
            foreach ($pdo_proc as $row) {
                $list[] = $row;
            }*/
            //只返回key,
            while($row = $pdo_proc->fetch(\PDO::FETCH_ASSOC)){
                $list[] = $row;
            }
            $pdo_proc = null;
            return $list;
        } catch (\PDOException $e) {
            // 两种方式都可以完成异常捕获
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 获取数据库的版本
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午4:31
     */
    public function version(){
        $data = $this->query('SELECT VERSION() as version');
        if(empty($data)){
            return '';
        }
        return isset($data[0]['version']) ? $data[0]['version'] : '';
    }

    /**
     * 链式查询删除数据
     *
     * @return mixed
     * @throws \Exception
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/26 上午12:19
     */
    public function delete()
    {
        // 第二种绑定参数的方式
        $sql = "DELETE FROM ". $this->getTableSql() . $this->getWhereSql();
        $res =  $this->exec($sql);
        $this->clear();
        return $res;
    }

    /**
     * 链接查询，修改数据
     *
     * @param  array  $data
     * @return false
     * @throws \Exception
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/26 上午12:19
     */
    public function update(array $data)
    {
        $tableName = $this->getTableSql();
        $fieldType = $this->tableFieldType[$tableName];
        $fieldSql = [];
        foreach ($data as $key => $value) {
            //如果字段不存在，不参与更新
            if(! isset($fieldType[$key])){
                continue;
            }
            //原生SQL处理
            $iPos = strpos($value, 'raw:');
            if( $iPos !== false){
                $iPos = strpos($value, 'raw:') + 4;
                $fieldSql[] = "`{$key}` = " . substr($value, $iPos);
                continue;
            }

            //需要加引号的
            if ($fieldType[$key]['quote']) {
                $fieldSql[] = "`{$key}` = '" . addslashes($value) . "'";
            } else {
                $fieldSql[] = "`{$key}` = {$value}";
            }
        }
        if(empty($fieldSql)){
            return false;
        }
        //没有updated_at字段，但是表中存在, 自动更新为当前时间
        if(! isset($data['updated_at']) && isset($fieldType['updated_at'])){
            $fieldSql[] = "`updated_at` = '". date('Y-m-d H:i:s') ."'";
        }
        //return false;
        $sql = "UPDATE ". $tableName .' SET '. implode(',', $fieldSql) . $this->getWhereSql();
        $result = $this->exec($sql);
        $this->clear();
        return $result;
        //暂不使用预编译
        //$stmt = $this->pdo->prepare($query);
        //$res = $stmt->execute(array(":name" => '紫龙', ":id" => 2));
        //echo ($stmt->rowCount() > 0) ? '成功修改了' . $stmt->rowCount() . '条记录' : '没有记录修改';
    }


    /**
     * 链式查询，添加数据
     *
     * @param  array  $data
     * @return false
     * @throws \Exception
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/26 上午12:19
     */
    public function insert(array $data)
    {
        $fieldList = $valueList = $dataList = $vList = [];
        $tableName = $this->getTableSql();
        $fieldType = $this->tableFieldType[$tableName];

        foreach ($data as $k=>$v){
            //如果字段不存在，不参与更新
            if(! isset($fieldType[$k])){
                continue;
            }
            $fieldList[] = "`$k`";

            if($fieldType[$k]['quote']){
                $valueList[] = "'". addslashes($v) ."'";
            }else{
                if($v === null || $v === ""){
                    $v = 0;
                }
                $valueList[] = $v;
            }


        }
        if(empty($fieldList)){
            return false;
        }
        //补上created_at与updated_at，为兼容mysql5.6
        $currentTime = date('Y-m-d H:i:s');
        if(! isset($data['created_at']) && isset($fieldType['created_at'])){
            $fieldList[] = 'created_at';
            $valueList[] = "'{$currentTime}'";
        }
        //补上updated_at
        if(! isset($data['updated_at']) && isset($fieldType['updated_at'])){
            $fieldList[] = 'updated_at';
            $valueList[] = "'{$currentTime}'";
        }
        $sql = "INSERT INTO {$tableName} (". implode(',', $fieldList) .") VALUES (". implode(',', $valueList) .");";
        $this->exec($sql);

        return $this->pdo->lastInsertId();

        /*
        $sql = "INSERT INTO  {$tableName} (". implode(',', $fieldList) .") VALUES (". implode(',', $valueList) .")";
        echo $sql;
        //exit;
        // 第二种绑定参数的方式
        $stmt = $this->pdo->prepare($sql);
        //var_dump($stmt);exit;
        $stmt->execute($dataList);
        //$this->pdo->insert();
        return $this->pdo->lastInsertId();
        */
    }

    /**
     * 批量插入
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/26 上午8:40
     */
    public function batchInsert($dataList)
    {
        if(empty($dataList)){
            return false;
        }
        $fieldList = $valueList = [];
        $tableName = $this->getTableSql();
        $fieldType = $this->tableFieldType[$tableName];

        $dataField = $dataList[0];
        foreach ($dataField as $k=>$v){
            //如果字段不存在，不参与更新
            if(! isset($fieldType[$k])){
                continue;
            }
            $fieldList[] = "`$k`";
            $valueList[] = ':'.$k;
        }
        if(empty($fieldList)){
            return false;
        }
        $sql = "INSERT INTRO  {$tableName} (". implode(',', $fieldList) .") VALUES (". implode(',', $valueList) .")";
        //使用绑定参数的方式添加数据，多条转成单条循环添加
        $stmt = $this->pdo->prepare($sql);
        foreach ($dataList as $data) {
            $sqlDataList = [];
            foreach($data as $dk => $dv){
                $sqlDataList[':'.$dk] = $dv;
            }
            $stmt->execute($sqlDataList);
        }
        return true;
    }

    /**
     * 分页，返回记录以及总的记录数
     *
     * @param  int  $pageSize
     * @param  string[]  $fields
     * @param int $page
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/17 下午1:47
     */
    public function paginate(int $pageSize=PageEnums::PAGE_SIZE, array $fields=['*'], int $page=1){
        //检查数据库连接
        if(empty($this->pdo)){
            $this->connect();
        }

        if(empty($this->fields)) {
            $this->fields = $fields;
        }
        $key = $this->keyBy;

        $this->queryPager = true;
        //统计数量
        $countSql = $sql = 'SELECT COUNT(*) as num FROM '
            . $this->getTableSql()
            . $this->getWhereSql();
        if($this->debug){
            log_error('sql', $countSql);
        }

        $stmt = $this->pdo->query($countSql);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $totalRecords = $result['num'];
        $totalPages = ceil($totalRecords / $pageSize);

        $this->offset($pageSize * ($page-1));
        $this->limit($pageSize);

        $sql = 'SELECT ' . $this->getFields() . ' FROM '
            . $this->getTableSql()
            . $this->getWhereSql()
            . $this->getGroupSql()
            . $this->getOrderBySql()
            . $this->getLimitSql()
            . $this->getHavingSql();

        if($this->debug){
            log_error('sql', $sql);
        }

        //记录SQL日志
        //echo $sql .PHP_EOL;
        $res = $this->pdo->prepare($sql);//准备查询语句
        $res->execute();
        $list = [];
        while ($result = $res->fetch(\PDO::FETCH_ASSOC)) {
            if(empty($key)) {
                $list[] = $result;
            }else{
                $list[$result[$key]] = $result;
            }
        }
        $this->queryPager = false;
        $this->clear();
        return [
            'list' => $list,
            'page'  => $page,
            'total' => $totalRecords,
            'totalPages' => $totalPages,
            'pageSize' => $pageSize
        ];
    }

    /**
     * 获取表名
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午6:11
     */
    private function getTableSql()
    {
        if (empty($this->tableName)) {
            throw new \Exception('数据查询，缺少表名', 500);
        }
        //表名拼上前辍
        $tableName = config('database.prefix') . $this->tableName;
        //处理表字段，用于查询的较验
        if(! isset($this->tableFieldType[$tableName])) {
            $this->tableFieldType[$tableName] = $this->getTableFieldType($this->tableName);
        }
        //主表的映射
        $this->aliasList[$this->aliasParams] = $tableName;
        return $tableName;
    }

    /**
     * 拼接sql语句的时候调用
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午5:58
     */
    private function getFields()
    {
        if (empty($this->fields)) {
            return '*';
        }
        //有联表的情况, 不加mysql符号``， 出去性能考虑
        if(! empty($this->joinParams)){
            if($this->fields == ['*']){
                return $this->aliasParams.'.*';
            }
            return implode(', ', $this->fields);
        }
        //没有联表的情况
        if(is_array($this->fields)){
            //默认返回所有字段
            if($this->fields == ['*'] || $this->fields == ["*"]){
                return '*';
            }
            $str = '';
            foreach ($this->fields as $v){
                if($v == '*'){
                    $field = '*';
                }else {
                    //原生SQL处理
                    $iPos = strpos($v, 'raw:');
                    if ($iPos !== false) {
                        $iPos = strpos($v, 'raw:') + 4;
                        $field = substr($v, $iPos);
                    } else {
                        $field = "`{$v}`";
                    }
                }
                //字符串逗号连接
                if($str != ''){
                    $str .= ', ';
                }

                $str .= $field;
            }
            return $str;
        }
        return $this->fields;
    }

    /**
     * 获取where条件语句
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午6:13
     */
    private function getWhereSql()
    {
        $where = [];
        if (!empty($this->condition)) {
            foreach ($this->condition as $v){
                if(is_string($v)){
                    $where[] = $v;
                }else{
                    $oneWhere = $this->getOneWhereRow($v);
                    if(! empty($oneWhere)){
                        $where[] = $oneWhere;
                    }
                }
            }
            return ' WHERE ' . implode(' AND ', $where);
        }
        return '';
    }

    /**
     * 将一维数据转成sql语句 where[] 中的一项, 需要判断传入的类型与字段类型不一致的情况
     * ['name', '春光'] ['age', '>', 25]
     *
     * @param  array  $row
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午10:11
     */
    private function getOneWhereRow(array $row){
        $value = $row[2];
        $valueType = gettype($value);
        $fieldPrefix = ''; //字段前辍
        $name = $row[0];
        //联表查询的sql处理
        if(! empty($this->joinParams)){
            $name_list = explode('.', $name);
            $fieldPrefix = $name_list[0];
            $tableName = $this->aliasList[$fieldPrefix];
            //拼接字符串要用
            $fieldPrefix .= '.';
            $name = $name_list[1];
        }else{
            $tableName = $this->getTableSql();
        }

        //echo $name;exit;
        //判断字段的实际类型, 是否需要加 ''，如果是字符串，要加
        $needQuote = $this->tableFieldType[$tableName][$name]['quote'];
        if($valueType == 'NULL'){
            return '';
        }

        $operator = $row[1];
        switch ($valueType){
            case 'array' :
                if($needQuote){//每个值，会自动加引号
                    $valueSql = '('. stringImplode(',', $value) .')';
                }else{
                    $valueSql = '('. implode(',', $value) .')';
                }
                break;
            case 'string':
                if($operator == 'in'){//IN查询字符串，原样输出
                    $valueSql = "({$value})";
                    break;
                }
                //通过真实类型判断，是否要加上引号，优化性能
                if($needQuote){
                    $valueSql = "'". addslashes($value). "'";
                }else{
                    $valueSql = $value;
                }
                break;
            default:
                $valueSql = $value;
        }
        return "{$fieldPrefix}`{$name}` {$operator} {$valueSql}";
    }

    /**
     * 获取group语句
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午6:13
     */
    private function getGroupSql()
    {
        if (!empty($this->groupBy)) {
            if(is_array($this->groupBy)){
                if(empty($this->joinParams)) {
                    $sql = '';
                    foreach ($this->groupBy as $v) {
                        if ($sql != '') {
                            $sql .= ',';
                        }
                        $sql .= "`" . $v . "`";
                    }
                }else{
                    $sql = implode(",", $this->groupBy);
                }
                return ' GROUP BY ' . $sql;
            }else {
                return ' GROUP BY ' .$this->groupBy;
            }
        }
        return '';
    }

    /**
     * 获取having语句
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午6:13
     */
    private function getHavingSql()
    {
        if (!empty($this->having)) {
            return ' HAVING ' . $this->having;
        }
        return '';
    }

    /**
     * 获取排序语句
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午6:04
     */
    private function getOrderBySql()
    {
        if (empty($this->orderBy)) {
            return '';
        }
        $sql = '';
        if(empty($this->joinParams)) {
            foreach ($this->orderBy as $v) {
                if ($sql != '') {
                    $sql .= ',';
                }
                $sql .= " `{$v[0]}`  {$v[1]}";
            }
        }else{//有join的时候
            foreach ($this->orderBy as $v) {
                if ($sql != '') {
                    $sql .= ',';
                }
                $sql .= " {$v[0]}  {$v[1]}";
            }
        }
        return ' ORDER BY ' . $sql;
    }

    /**
     * 获取limit语句
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午6:08
     */
    private function getLimitSql()
    {
        if ($this->limitValue > 0) {
            if($this->offsetValue > 0){
                return ' LIMIT '. $this->offsetValue .', '. $this->limitValue;
            }
            return ' LIMIT ' . $this->limitValue;
        }
        return '';
    }

    /**
     * 组装查询的SQL语句
     *
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午6:03
     */
    private function getSql()
    {
        $sql = 'SELECT ' . $this->getFields() . ' FROM '
            . $this->getTableSql()
            . ' '. $this->aliasParams
            . $this->getJoinSql()
            . $this->getWhereSql()
            . $this->getGroupSql()
            . $this->getOrderBySql()
            . $this->getLimitSql()
            . $this->getHavingSql();
        if($this->debug){
            log_error('sql', $sql);
        }
        return $sql;
    }

    /**
     * 获取一行的数据
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/25 下午10:43
     */
    private function row($fieldName)
    {
        $sql = $this->getSql();
        if(empty($this->pdo)){
            $this->connect();
        }
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->clear(false);
        if(is_array($fieldName)){
            if(empty($result)){
                return [];
            }
            //清除当前的sql相关缓存
            $this->clear(false);
            //处理with查询
            $this->dealWith($result, 'one');
            $this->queryWith = false;
            $this->clearWith();
            return $result;
        }else{
            return empty($result) ? '' : (isset($result[$fieldName]) ? $result[$fieldName] : '');
        }
    }

    /**
     * with查询处理
     *
     * @param  array  $params
     * @param  \Closure|null  $closure
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/25 上午12:48
     */
    public function with(array $params, \Closure $closure = null){
        $this->withParams[] = ['params' => $params, 'closure' => $closure];
        return $this;
    }

    /**
     * 关闭数据库连接
     **
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/17 上午11:51
     */
    public function close(){
        $this->clear();
        $this->pdo = null;
    }

}