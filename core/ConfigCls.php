<?php
namespace Core;

class ConfigCls {

    use Singleton;

    private $config = [];

    /**
     * 加载配置文件
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/23 下午7:24
     */
    public function load(string $configName){
        $path = ROOT_PATH ."/config/{$configName}.php";
        if(file_exists($path)) {
            $data = include $path;
            if ($configName == 'app') {
                $this->config = array_merge($this->config, $data);
            } else {
                $this->config[$configName] = $data;
            }
        }
    }

    /**
     * 获取配置
     *
     * @param  string  $configName
     * @param  string  $default
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/23 下午7:28
     */
    public function get(string $configName, string $default=''){
        if(strpos($configName, '.')){
            $keyArray = explode('.', $configName);
            //检测配置是否已加载，未加载则注入
            if(! isset($this->config[$keyArray[0]])){
                self::load($keyArray[0]);
            }
            $config = [];
            foreach ($keyArray as $i=>$key){
                if($i == 0){
                    if(isset($this->config[$key])){
                        $config = $this->config[$key];
                        continue;
                    }
                    return '';
                }else{
                    if(isset($config[$key])){
                        $config = $config[$key];
                        continue;
                    }
                    return '';
                }
            }
            return $config;
        }else {
            //检测配置是否已加载，未加载则注入
            if(! isset($this->config[$configName])){
                self::load($configName);
            }
            //如果是APP返回默认配置
            if($configName=='app'){
                return  $this->config;
            }
            return isset($this->config[$configName]) ? $this->config[$configName] : $default;
        }
    }

    /**
     * 动态设置参数
     *
     * @param $configName
     * @param  string  $default
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/26 下午3:24
     */
    public function set(string $configName, $value=''){
        if(strpos($configName, '.')){
            $config = & $this->config;
            $keyArray = explode('.', $configName);
            $len = count($keyArray);
            foreach ($keyArray as $i=>$key){
                if($i == $len-1){
                    $config[$key] = $value;
                }else {
                    //转成树型结构
                    if (!isset($config[$key])) {
                        $config[$key] = [];
                    }
                    $config = &$config[$key];
                }
            }
        }else {
            $this->config[$configName] = $value;
        }
    }

    /**
     * 获取全部配置
     *
     * @return array
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 上午8:24
     */
    public function all(){
        return $this->config;
    }

}