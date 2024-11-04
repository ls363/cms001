@section('title', '编辑管理员')
@section('id', $id)
@section('content')

        <div class="layui-form-item">
            <label class="layui-form-label">用户名：</label>
            <div class="layui-input-block">
                <input type="text" value="{$info.username ?? ''}" name="username" required  placeholder="请输入用户名" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">姓名：</label>
            <div class="layui-input-block">
                <textarea name="real_name" placeholder="请输入姓名" class="layui-textarea" required >{$info.real_name ?? ''}</textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">邮箱：</label>
            <div class="layui-input-block">
                <input type="text" name="email" required  lay-verify="required|email" placeholder="请输入Email" autocomplete="off" class="layui-input" value="{$info.email ?? ''}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">手机号：</label>
            <div class="layui-input-block">
                <input type="text" name="phone" required  lay-verify="required|phone" placeholder="请输入手机号" autocomplete="off" class="layui-input" value="{$info.phone ?? ''}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">性别：</label>
            <div class="layui-input-block">
                <input type="radio" name="sex" value="1" title="男" {if isset($info['sex']) && $info['sex'] == 1}checked{/if} >
                <input type="radio" name="sex" value="2" title="女" {if isset($info['sex']) && $info['sex'] == 2}checked{/if} >
            </div>
        </div>
@endsection


@section('js')
<script>
    layui.use(['form','jquery','laypage', 'layer'], function() {
        var form = layui.form,
            $ = layui.jquery;
        form.render();
        var layer = layui.layer;
        form.verify({
        //    title: [/[\u4e00-\u9fa5]{2,12}$/, '标题必须2到12位汉字'],
        //    intro: [/[\u4e00-\u9fa5]{2,30}$/, '权限介绍必须2到30位汉字'],
        });
        form.on('submit(formDemo)', function(data) {
            $.ajax({
                url:" {{url('save')}}",
                data:$('form').serialize(),
                type:'post',
                dataType:'json',
                success:function(res){
                    if(res.code == 200){
                        layer.msg(res.message,{icon:6});
                        var index = parent.layer.getFrameIndex(window.name);
                        setTimeout('parent.layer.close('+index+')',1000);
                    }else{
                        layer.msg(res.msg,{shift: 6,icon:5});
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
@endsection
@extends('admin.common.edit')