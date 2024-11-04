@section('title', '链接编辑')
@section('id', $id)
@section('content')
<style type="text/css">
    .layui-form-label{width: 100px;}
    .layui-input-block{margin-left: 130px;}
</style>

<div class="layui-form-item">
    <label class="layui-form-label">链接类型：</label>
    <div class="layui-input-block">
        <select name="category_id" id="category_id" lay-verify="required">
            <option value=""></option>
            {foreach $categoryList as $k=>$v}
            <option value="{$k}">{$v}</option>
            {/foreach}
        </select>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">标题：</label>
    <div class="layui-input-block">
        <input type="text" value="{$info['title'] ?? ''}" name="title" required lay-verify="title" placeholder="标题不能为空" autocomplete="off" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">网址：</label>
    <div class="layui-input-block">
        <input type="text" value="{$info['url'] ?? ''}" name="url" required lay-verify="url" placeholder="网址不能为空" autocomplete="off" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">描述：</label>
    <div class="layui-input-block">
        <textarea name="intro" placeholder="请输入" class="layui-textarea" required lay-verify="intro">{$info['intro'] ?? ''}</textarea>
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
        $('#category_id').val({$info['category_id'] ?? 0 })
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
