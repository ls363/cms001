
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>修改用户</title>
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/layui/css/layui.css"/>
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/css/admin.css"/>
</head>
<body>
<div class="layui-tab page-content-wrap">
    <ul class="layui-tab-title">
        <li class="layui-this">修改资料</li>
        <li>修改密码</li>
    </ul>
    <div class="layui-tab-content">
        <div class="layui-tab-item layui-show">
            <form class="layui-form"  style="width: 90%;padding-top: 20px;" id="info_form">
                {{ csrf_token() }} {{ ajax_hidden() }}
                <div class="layui-form-item">
                    <label class="layui-form-label">用户名：</label>
                    <div class="layui-input-block">
                        <input type="text" name="username" disabled autocomplete="off" class="layui-input layui-disabled" value="{$userInfo.username}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">姓名：</label>
                    <div class="layui-input-block">
                        <input type="text" name="real_name" autocomplete="off" class="layui-input" placeholder="请输入姓名" value="{$userInfo.real_name}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">邮箱：</label>
                    <div class="layui-input-block">
                        <input type="text" name="email" required  lay-verify="required|email" placeholder="请输入邮箱" autocomplete="off" class="layui-input" value="{$userInfo.email}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">手机号：</label>
                    <div class="layui-input-block">
                        <input type="text" name="phone" required  lay-verify="required|phone" placeholder="请输入手机号" autocomplete="off" class="layui-input" value="{$userInfo.phone}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">性别：</label>
                    <div class="layui-input-block">
                        <input type="radio" name="sex" value="1" title="男" {if $userInfo['sex'] == 1}checked{/if} >
                        <input type="radio" name="sex" value="2" title="女" {if $userInfo['sex'] == 2}checked{/if} >
                    </div>
                </div>
                <input name="id" type="hidden" value="1">
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="adminInfo">立即提交</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-tab-item">
            <form class="layui-form" style="width: 90%;padding-top: 20px;" id="pwd_form">
                {{ csrf_token() }} {{ ajax_hidden() }}
                <div class="layui-form-item">
                    <label class="layui-form-label">用户名：</label>
                    <div class="layui-input-block">
                        <input type="text" name="{$userInfo.username}" disabled autocomplete="off" class="layui-input layui-disabled" value="admin">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">旧密码：</label>
                    <div class="layui-input-block">
                        <input type="password" name="originPassword" required lay-verify="required|originPassword" placeholder="请输入密码" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">新密码：</label>
                    <div class="layui-input-block">
                        <input type="password" name="newPassword" required lay-verify="required|newPassword" placeholder="请输入密码" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">重复密码：</label>
                    <div class="layui-input-block">
                        <input type="password" name="confirmPassword" required lay-verify="required|confirmPassword" placeholder="请输入密码" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="adminPassword">立即提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{PUBLIC_URL}/static/admin/layui/layui.js" type="text/javascript" charset="utf-8"></script>
<script>
    layui.use(['form','jquery','element'], function(){
        var form = layui.form,
            $ = layui.jquery;
        form.render();
        form.verify({
            originPassword: [/(.+){6,12}$/, '密码必须6到12位'],//密码
            newPassword:function(value){
                if(value==$("input[name='originPassword']").val()){
                    return '新密码不能与旧密码一样';
                }
                if(value&&!/[a-zA-Z\d]{6,12}$/.test(value)){
                    return '新密码必须6到12位数字或字母';
                }
            },
            confirmPassword: function(value) {
                if(value && $("input[name='newPassword']").val() != value) {
                    return '两次输入密码不一致';
                }
            },
        });
        form.on('submit(adminInfo)', function(data){
            $.ajax({
                url:"{{ url('saveUserInfo') }}",
                data:$('#info_form').serialize(),
                type:'post',
                dataType:'json',
                success:function(res){
                    if(res.code == 200){
                        layer.msg(res.message,{icon:6});
                        var index = parent.layer.getFrameIndex(window.name);
                        setTimeout('parent.layer.close('+index+')',2000);
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
        form.on('submit(adminPassword)', function(data){
            $.ajax({
                url:"{{ url('changePassword') }}",
                data:$('#pwd_form').serialize(),
                type:'post',
                dataType:'json',
                success:function(res){
                    if(res.code == 200){
                        layer.msg(res.message,{icon:6});
                        var index = parent.layer.getFrameIndex(window.name);
                        setTimeout('parent.layer.close('+index+')',2000);
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