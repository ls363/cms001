<?php


namespace Core;

/**
 * Session类
 * （1）关闭浏览器不会使一个Session结束，但会使这个Session永远无法访问。因为当用户打开新的浏览器窗口又会产生一个新的Session。
 * （2）Session对象不是一直有效，默认有效期为24分钟。
 * （3）增加Session的有效期会导致Web服务器保存用户Session的信息的时间增长，如果访问的用户很多，会加重服务器负担。
 * （4）不能单独对某个用户的Session设置有效期。
 *
 * Date: 2021/01/08
 */
class WebSession
{

    const SESSION_STARTED = true;
    const SESSION_NOT_STARTED = false;

    // The state of the session
    private $sessionState = self::SESSION_NOT_STARTED;

    // THE only instance of the class
    private static $instance;


    private function __construct()
    {
    }

    /**
     *    Returns THE instance of 'Session'.
     *    The session is automatically initialized if it wasn't.
     *
     * @return    object
     **/

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        self::$instance->startSession();
        return self::$instance;
    }

    /**
     * session_start处理
     *
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/26 下午6:14
     */
    public function startSession()
    {
        if ($this->sessionState == self::SESSION_NOT_STARTED) {
            if (!headers_sent() && session_id() == ''){
                ini_set('session.auto_start', 0);
                ini_set('session_save_path', '/tmp/');//设置保存路径
                ini_set('session.gc_maxlifetime', 60);//保存1分钟
                $this->sessionState = session_start();
            }
        }

        return $this->sessionState;
    }

    public function destroy()
    {
        if ( $this->sessionState == self::SESSION_STARTED )
        {
            $this->sessionState = !session_destroy();
            unset( $_SESSION );
            return !$this->sessionState;
        }

        return FALSE;
    }

    public function __get( $name )
    {
        if ( isset($_SESSION[$name]))
        {
            return $_SESSION[$name];
        }
    }

    public function __set( $name , $value )
    {
        $_SESSION[$name] = $value;
    }

    public function __isset( $name )
    {
        return isset($_SESSION[$name]);
    }


    public function __unset( $name )
    {
        unset( $_SESSION[$name] );
    }


    /**
     * 设置Session
     *
     * @param string $name Session名称
     * @param $value 值
     */
    public function set(string $name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * 获取Session值
     * @param string $name Session名称
     */
    public function get(string $name)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : '';
    }

    /**
     * 设置Session Domain
     * @param $sessionDomain 域
     * @return string
     */
    public function setDomain($sessionDomain = null)
    {
        $return = ini_get('session.cookie_domain');
        if (!empty($sessionDomain)) {
            ini_set('session.cookie_domain', $sessionDomain);   //跨域访问Session
        }
        return $return;
    }

    /**
     * 删除指定Session值
     * @param $name Session名称
     */
    public function clear($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * 获取或设置Session id
     */
    public function sessionid($id = null)
    {
        return session_id($id);
    }

}

/**
$data = Webession::getInstance();
$data->nickname = 'Someone';
$data->age = 18;

var_dump( isset( $data->nickname ));

// We destroy the session
$data->destroy();
 */

