
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>{$system.site_name}后台首页</title>
    <meta name="csrf-token" content="{{ csrf_token(false) }}">
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/layui/css/layui.css?stime={{time()}}"/>
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/css/admin.css?stime={{time()}}"/>
    <link data-n-head="ssr" rel="icon" type="image/x-icon" href="{PUBLIC_URL}/static/favicon.ico">
</head>
<body>
<div class="main-layout" id='main-layout'>
    <!--侧边栏-->
    <div class="main-layout-side">
        <div class="m-logo">
            <img src="{PUBLIC_URL}/static/admin_logo.png" alt="{$system.site_name}" title="{$system.site_name}" style="height: 50px;" />
        </div>
        <ul class="layui-nav layui-nav-tree" lay-filter="leftNav">
            {if ! empty($menus) }
            {foreach $menus as $v}
            <li class="layui-nav-item">
                <a href="javascript:;"><i class="layui-icon">&#xe632;</i>&nbsp;&nbsp;&nbsp;{$v['title']}</a>
                <dl class="layui-nav-child ">
                    {if ! empty($v['children'])}
                    {foreach $v['children'] as $v2}
                    {if $v2['status'] == 1}
                    <dd><a href="#{$question_mark}{$v2['uri']}" data-url="{$question_mark}{$v2['uri']}" data-id="{$v2['id']}" data-text="{$v2['title']}"><i class="layui-icon layui-btn-sm">{$v2['icon']}</i>  {$v2['title']}</a></dd>
                    {/if}
                    {/foreach}
                    {/if}
                </dl>
            </li>
            {/foreach}
            {/if}
        </ul>
    </div>
    <!--右侧内容-->
    <div class="main-layout-container">
        <!--头部-->
        <div class="main-layout-header">
            <div class="menu-btn" id="hideBtn">
                <a href="javascript:;">
                    <span class="iconfont">&#xe60e;</span>
                </a>
            </div>
            <ul class="layui-nav" lay-filter="rightNav">
                {if $system['make_html'] == 1}
                <li class="layui-nav-item"><a style="color:#f60;" href="javascript:void(0);" id="btn_make_index">更新首页</a></li>
                {/if}
                <li class="layui-nav-item">
                    <div class="addBtn hidden-xs" style="color:#00b5f9;" data-desc="管理员信息" data-url="{{ url('main/userinfo') }}">&nbsp;<i class="layui-icon">&#xe612;</i>&nbsp;{$userInfo.real_name}&nbsp;</div>
                </li>
                <li class="layui-nav-item"><a style="color:#666;" href="{{ url('main/logout') }}">退出</a></li>
            </ul>
        </div>        <!--主体内容-->
        <div class="main-layout-body">
            <!--tab 切换-->
            <div class="layui-tab layui-tab-brief main-layout-tab" lay-filter="tab" lay-allowClose="true">
                <ul class="layui-tab-title">
                    <li class="layui-this welcome">后台主页</li>
                </ul>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show" style="background: #f5f5f5;">
                        <!--1-->
                        <iframe src="{{ url('main/welcome') }}" width="100%" height="100%" id="iframe" name="iframe" scrolling="auto" class="iframe" framborder="0"></iframe>
                        <!--1end-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--遮罩-->
    <div class="main-mask">

    </div>
</div>
<script type="text/javascript">
    var scope={
        link:"http://local.company/welcome"
    }
    <?php if (PUBLIC_URL == ""){?>
    var layuiModulePath = '../../static/admin/js/module/';
    <?php }else{?>
    var layuiModulePath = '../../public/static/admin/js/module/';
    <?php }?>
</script>
<script src="{PUBLIC_URL}/static/admin/layui/layui.js?sss={{time()}}" type="text/javascript" charset="utf-8"></script>
<script src="{PUBLIC_URL}/static/admin/js/common.js?sss={{time()}}" type="text/javascript" charset="utf-8"></script>
<script src="{PUBLIC_URL}/static/admin/js/main.js?sss={{time()}}" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">


    layui.use(['element','form', 'jquery','laydate', 'layer'], function() {
        var form = layui.form,
            $ = layui.jquery,
            laydate = layui.laydate,
            layer = layui.layer,
            element = layui.element;
        ;
        $('#btn_make_index').on('click', function() {
            $.get('{{ url('admin/make_html/makeIndex') }}', {}, function (data) {
                    if (data.code == 200) {
                        layer.msg("首页HTML更新成功");
                    } else {
                        layer.msg("首页HTML更新失败");
                    }
                }, 'json'
            );
        });

        $(document).ready(function(){
            window.onpopstate = function(event) {
                var url = $('.main-layout-tab .layui-tab-title li.layui-this').attr('lay-id');
                var new_url = window.location.hash;
                new_url = new_url.replace('#','_');
                console.log('current_tab', url,  window.location.hash);
                if(new_url != url){
                    element.tabChange('tab', new_url);
                }
            };
        });
    });
</script>
</body>
</html>