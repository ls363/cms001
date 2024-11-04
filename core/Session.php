<?php

namespace Core;

class Session
{
    use Singleton;

    /**
     * 判断是否过期
     *
     * @param string $name
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午11:54
     */
    public function expired(string $name)
    {
        if (!isset($_COOKIE[$name])) {  //cookie是否存在
            return true;
        } else {
            return false;
        }
    }

    /**
     * 增加会话
     *
     * @param  string  $name
     * @param  string  $value
     * @param  int  $ttl
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午11:51
     */
    public function add(string $name, string $value, int $ttl)
    {
        $domain = ConfigCls::getInstance()->get('cookie_domain');
        return setcookie($name, $value, time() + $ttl, '/', $domain);
    }

    /**
     * 删除会话
     *
     * @param string $name
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午11:53
     */
    public function remove(string $name)
    {
        $domain = ConfigCls::getInstance()->get('cookie_domain');
        //设置 cookie 过期时间为过去1小时
        return setcookie($name, "", time() - 3600, '/', $domain);
    }

    /**
     * 判断会话
     *
     * @param  string  $name
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午11:56
     */
    public function get(string $name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
    }

}