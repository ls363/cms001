<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>CMS001-错误提示页面</title>
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/layui/css/layui.css?sss={{time()}}" />
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/system/css/system.css?sss={{time()}}" />
    <script src="{PUBLIC_URL}/static/admin/layui/layui.js" type="text/javascript" charset="utf-8"></script>
    <script src="{PUBLIC_URL}/static/admin/js/common.js?sss={{time()}}" type="text/javascript" charset="utf-8"></script>
    <script src="{PUBLIC_URL}/static/admin/js/jquery.min.js"></script>

</head>
<body>
<div class="wrap-container">
    <div class="error_box">
        <div class="error_header">
            <div class="logo">
                错误提示
            </div>
        </div>
        <div class="error_body">
            <h3>{$message}</h3>
            {if (isset($line) && $line > 0)}
            <p>错误文件：{$file}, 第 {$line} 行</p>
            <p>追踪信息：</p>
            {/if}
        </div>
        <div class="error_footer">
            <a href="/">返回首页</a>
        </div>
    </div>
</div>
</body>
</html>