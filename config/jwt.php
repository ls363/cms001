<?php
/**
 * Created by PhpStorm.
 * User: zilong
 * Date: 2021/1/4
 * Time: 4:27 PM
 */

return [
    "key" => env('JWT_SECRET'),
    "iss" => "http://www.giikinn.com",
    "aud" => "http://www.giikinn.com",
    "ttl" => intval(env('JWT_TTL', 86400)), //有效期
    "refresh_token_period" => 86400 * 31, //有效期
];


return [
    'secret' => env("JWT_SECRET", 'szdj'),
    'ttl'    => env('JWT_TTL', 86400),
    'alg'    => env("JWT_ALG", 'HS256')
];
