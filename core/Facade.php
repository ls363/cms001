<?php
namespace Core;

abstract class Facade{

    public static $instanceList = [];

    public static function __callStatic($method, $args)
    {
        $name = static::getFacadeAccessor();
        if(! isset(self::$instanceList[$name])){
            $clsName = config('facades.'.$name);
            self::$instanceList[$name]  = $clsName::getInstance();
        }
        return self::$instanceList[$name]->{$method}(...$args);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    abstract protected static function getFacadeAccessor();

}