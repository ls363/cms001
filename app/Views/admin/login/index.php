
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>CMS001-后台登录</title>
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/layui/css/layui.css" />
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/css/login.css" />
    <link data-n-head="ssr" rel="icon" type="image/x-icon" href="{PUBLIC_URL}/static/favicon.ico">
</head>

<body>
<div class="m-login-bg">
    <div class="m-login">
        <h3>CMS001内容管理系统</h3>
        <div class="m-login-warp">
            <form class="layui-form" method="post">
                {{ csrf_token() }}
                {{ ajax_hidden() }}
                <div class="layui-form-item">
                    <input type="text" value="" name="username" required lay-verify="username" placeholder="用户名" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-item">
                    <input type="password" value="" name="password" required lay-verify="password" placeholder="密码" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <input type="text" name="verifyCode" lay-verify="verifyCode" placeholder="验证码" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <img class="verifyImg" onclick="refreshRandom(this)" src="{{ url('home/welcome/randNum') }}" />
                    </div>

                </div>
                <div class="layui-form-item m-login-btn">
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo">登录</button>
                    </div>
                    <div class="layui-inline">
                        <button type="reset" class="layui-btn layui-btn-primary">取消</button>
                    </div>
                </div>
            </form>
        </div>
        <p class="copyright">Copyright 2022-2099 by lichunguang</p>
    </div>
</div>
<script src="{PUBLIC_URL}/static/admin/layui/layui.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">

    //刷新验证码
    function refreshRandom(obj){
        var url = obj.src;
        var pos = url.indexOf('c=');
        if(pos == -1){
            url += (url.indexOf('?') == -1 ? '?' : '&');
            url+= 'c='+Math.random();
        }else{
            url = url.substr(0, pos)+'c='+Math.random();
        }
        obj.src = url;
    }

    /*
    layui.use(['form'], function() {
        var form = layui.form,
            layer = layui.layer;
        form.verify({
            username: [/(.+){2,12}$/, '用户名必须2到12位'],
            password: [/(.+){6,12}$/, '密码必须6到12位'],
            verifyCode: [/(.+){4}$/, '验证码必须是4位'],
        });
    });*/

    layui.use(['form','jquery','laypage', 'layer'], function() {
        var form = layui.form,
            $ = layui.jquery;
        form.render();
        var layer = layui.layer;
        form.verify({
            username: [/(.+){2,12}$/, '用户名必须2到12位'],
            password: [/(.+){6,12}$/, '密码必须6到12位'],
            verifyCode: [/(.+){4}$/, '验证码必须是4位'],
        });
        form.on('submit(formDemo)', function(data) {
            $.ajax({
                url:"{{ url('doLogin') }}",
                data:$('form').serialize(),
                type:'post',
                dataType:'json',
                success:function(res){
                    if(res.code == 200){
                        layer.msg(res.message,{icon:6});
                        window.location.href = '{{ url('main/index') }}';
                        //var index = parent.layer.getFrameIndex(window.name);
                        //setTimeout('parent.layer.close('+index+')',2000);
                    }else{
                        layer.msg(res.message,{shift: 6,icon:5});
                    }
                },
                error : function(XMLHttpRequest, textStatus, errorThrown) {
                    layer.msg('网络失败', {time: 1000});
                }
            });
            return false;
        });
    });
</script>


</body>

</html>