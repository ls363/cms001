<?php
use App\Middlewares\AdminAuth;
use App\Middlewares\MemberAuth;
use App\Middlewares\CsrfToken;
use App\Middlewares\ApiToken;


return [
    'admin_auth' => AdminAuth::class,
    'csrf_token' => CsrfToken::class,
    'member_auth' => MemberAuth::class,
    'api_token' => ApiToken::class
];