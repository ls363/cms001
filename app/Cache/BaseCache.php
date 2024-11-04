<?php

namespace App\Cache;

use App\Utils\RedisUtils;

class BaseCache
{

    public static $instance = null;

    public static function getRedis()
    {
        $redis_config = config('redis');
        $redis = RedisUtils::getInstance($redis_config);
        return $redis;
    }
}
