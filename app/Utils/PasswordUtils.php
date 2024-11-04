<?php

namespace App\Utils;

class PasswordUtils
{
    /**
     * 生成带盐的密码
     * @param  string  $password
     * @return array
     */
    public static function generatePassword(string $password): array
    {
        $salt = substr(uniqid(rand()), -4);
        $password = md5($password . $salt);
        return ['password' => $password, 'salt' => $salt];
    }

    /**
     * 验证带盐的密码
     * @param  string  $password
     * @param  string  $salt
     * @return string
     */
    public static function verifyPassword(string $password, string $salt): string
    {
        return md5($password . $salt);
    }
}