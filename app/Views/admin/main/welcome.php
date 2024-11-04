
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>网站后台管理系统</title>
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/layui/css/layui.css"/>
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/css/admin.css"/>
</head>
<body>
<div class="wrap-container welcome-container">
    <div class="row">
        <div class="welcome-left-container col-lg-9">
            <div class="data-show">
                <ul class="clearfix">
                    <li class="col-sm-12 col-md-4 col-xs-12">
                        <a href="javascript:;" class="clearfix">
                            <div class="icon-bg bg-org f-l">
                                <span class="iconfont">&#xe606;</span>
                            </div>
                            <div class="right-text-con">
                                <p class="name">会员数[未启用]</p>
                                <p><span class="color-org">0</span>数据<span class="iconfont">&#xe628;</span></p>
                            </div>
                        </a>
                    </li>
                    <li class="col-sm-12 col-md-4 col-xs-12">
                        <a href="javascript:;" class="clearfix">
                            <div class="icon-bg bg-green f-l">
                                <span class="iconfont">&#xe605;</span>
                            </div>
                            <div class="right-text-con">
                                <p class="name">评论数</p>
                                <p><span class="color-green">{$comment_num}</span>数据<span class="iconfont">&#xe60f;</span></p>
                            </div>
                        </a>
                    </li>
                    {foreach $module_list as $v}
                    <li class="col-sm-12 col-md-4 col-xs-12">
                        <a href="javascript:;" class="clearfix">
                            <div class="icon-bg bg-blue f-l">
                                <span class="iconfont">&#xe602;</span>
                            </div>
                            <div class="right-text-con">
                                <p class="name">{$v.title}数</p>
                                <p><span class="color-blue">{$v.num}</span>数据<span class="iconfont">&#xe628;</span></p>
                            </div>
                        </a>
                    </li>
                    {/foreach}

                </ul>
            </div>

            <!--服务器信息-->
            <div class="server-panel panel panel-default">
                <div class="panel-header">服务器信息</div>
                <div class="panel-body clearfix">
                    <div class="col-md-2">
                        <p class="title">服务器环境</p>
                        <span class="info">{$server.soft}{$server.ip}</span>
                    </div>
                    <div class="col-md-2">
                        <p class="title">服务器IP地址</p>
                        <span class="info">{$server.ip}</span>
                    </div>
                    <div class="col-md-2">
                        <p class="title">服务器域名</p>
                        <span class="info">{$server.domain}</span>
                    </div>
                    <div class="col-md-2">
                        <p class="title"> PHP版本</p>
                        <span class="info">{$server.php_version}</span>
                    </div>
                    <div class="col-md-2">
                        <p class="title">数据库信息</p>
                        <span class="info">{$server.mysql_version}</span>
                    </div>
                    <div class="col-md-2">
                        <p class="title">服务器当前时间</p>
                        <span class="info">{$server.time}</span>
                    </div>

                    <div class="col-md-2">
                        <p class="title">端口</p>
                        <span class="info">{$server.port}</span>
                    </div>
                    <div class="col-md-2">
                        <p class="title">程序根目录</p>
                        <span class="info">{$server.root}</span>
                    </div>

                </div>

            </div>
        </div>
        <div class="welcome-edge col-lg-3">
            <!--联系-->
            <div class="panel panel-default contact-panel">
                <div class="panel-header">联系我</div>
                <div class="panel-body">
                    <p>微信：13757193328</p>
                    <p>E-mail:153102250@qq.com</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{PUBLIC_URL}/static/admin/layui/layui.js" type="text/javascript" charset="utf-8"></script>
<script src="{PUBLIC_URL}/static/admin/lib/echarts/echarts.js"></script>
</body>
</html>