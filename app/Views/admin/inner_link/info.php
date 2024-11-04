@section('title', '内链编辑')
@section('id', $id)
@section('content')
<style type="text/css">
    .layui-form-label{width: 100px;}
    .layui-input-block{margin-left: 130px;}
</style>

<div class="layui-form-item">
    <label class="layui-form-label">内容模型：</label>
    <div class="layui-input-block">
        <select name="model_id" id="model_id" lay-verify="required">
            <option value=""></option>
            {foreach $modelList as $k=>$v}
            <option value="{$k}">{$v}</option>
            {/foreach}
        </select>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">名称：</label>
    <div class="layui-input-block">
        <input type="text" value="{$info['title'] ?? ''}" name="title" required lay-verify="title" placeholder="请输入名称" autocomplete="off" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">URL：</label>
    <div class="layui-input-block">
        <input type="text" value="{$info['url'] ?? ''}" name="url" required lay-verify="url" placeholder="请输入URL" autocomplete="off" class="layui-input">
    </div>
</div>


@endsection
@section('id',$id)
@section('js')
<script>


    layui.use(['form','jquery','laypage', 'layer','laydate'], function() {
        var form = layui.form,
            $ = layui.jquery;
        form.render();
        var layer = layui.layer;
        $('#model_id').val({$info['model_id'] ?? 0 })
        form.render('select');
        form.verify({
            title: function (value) {
                if (value == '' || value < 1) {
                    return '请填写标题';
                }
            },
            url: function (value) {
                if (value == '' || value < 1) {
                    return '请填写网址';
                }
            },

        });
        form.on('submit(formDemo)', function(data) {
            $.ajax({
                url:"{{url('save')}}",
                data:$('form').serialize(),
                type:'post',
                dataType:'json',
                success:function(res){
                    if(res.code == 200){
                        layer.msg(res.message,{icon:6});
                        var index = parent.layer.getFrameIndex(window.name);
                        setTimeout('parent.layer.close('+index+')',500);
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
@endsection
@extends('admin.common.edit')
