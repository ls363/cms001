<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>@yield('title')</title>
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/layui/css/layui.css" />
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
</head>
<body>
<div class="wrap-container clearfix">
    <div class="column-content-detail">
        <form class="layui-form" method="get" action="">
            <div class="layui-form-item">
                <div class="layui-inline tool-btn">
                    @yield('header')
                </div>
                {{ csrf_token() }}
            </div>
        </form>
        <div class="layui-form" id="table-list">
            @yield('table')
        </div>
    </div>
</div>
</body>
@yield('js')
</html>