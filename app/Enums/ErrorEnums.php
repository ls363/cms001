<?php
namespace App\Enums;

class ErrorEnums{
    const DEFAULT_ERROR = 500;
    const ROUTE_NOT_FOUND = '路由不存在';
    const TEMPLATE_NOT_FOUND = '模板不存在';
    const INVALID_ARGS = '参数无效';
    const ADMIN_NOT_FOUND = '管理员不存在';
    const ADMIN_PASSWORD_ERROR = '管理员密码错误';
    const LOGIN_VERIFY_ERROR = '验证码错误';

    //密码长度错误
    public const PASSWORD_LENGTH = '密码长度错误';

    //修改密码相关
    //检查必填
    public const ORIGIN_PASSWORD_REQUIRED = '请输入原密码';
    public const NEW_PASSWORD_REQUIRED = '请输入新密码';
    public const CONFIRM_PASSWORD_REQUIRED = '请输入确认密码';
    //检查密码长度
    public const ORIGIN_PASSWORD_LENGTH = '原密码长度错误';
    public const NEW_PASSWORD_LENGTH = '新密码长度错误';
    public const CONFIRM_PASSWORD_LENGTH = '确认密码长度错语';
    //校验当前密码，错误提示“请输入正确的密码”
    public const ORIGIN_PASSWORD_ERROR = '原密码错误';
    //密码错误
    public const PASSWORD_ERROR = '新密码错误';
    //两次输入密码不一致
    public const PASSWORD_NOT_MATCH = '两次输入密码不一致';

}
//https://blog.csdn.net/qq_36025814/article/details/118733570