<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>@yield('title')</title>
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/layui/css/layui.css?sss={{time()}}" />
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/css/admin.css?sss={{time()}}" />
    <script src="{PUBLIC_URL}/static/admin/layui/layui.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript">
    <?php if (PUBLIC_URL == ""){?>
    var layuiModulePath = '../../static/admin/js/module/';
    <?php }else{?>
    var layuiModulePath = '../../public/static/admin/js/module/';
    <?php }?>
    </script>
    <script src="{PUBLIC_URL}/static/admin/js/common.js?sss={{time()}}" type="text/javascript" charset="utf-8"></script>
    <script src="{PUBLIC_URL}/static/admin/js/jquery.min.js"></script>

</head>
<body>
<div class="wrap-container">
    <form class="layui-form" style="width: 90%;padding-top: 20px;">
        {{ csrf_token() }}
        @yield('content')
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
        <input name="id" type="hidden" value="@yield('id')">
    </form>
</div>
@yield('js')
</body>
</html>