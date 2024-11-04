@section('title', '编辑链接分类')
@section('content')
        <div class="layui-form-item">
            <label class="layui-form-label">分类名称：</label>
            <div class="layui-input-block">
                <input type="text" value="{$data.title ?? ''}" name="title" required lay-verify="permission_remark" placeholder="请输入2-12位字母" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">分类简介：</label>
            <div class="layui-input-block">
                <textarea name="intro" placeholder="请输入2-30位汉字" class="layui-textarea" required lay-verify="permission_desc">{$data.intro ?? ''}</textarea>
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
                url:"{{ url('save') }}",
                data:$('form').serialize(),
                type:'post',
                dataType:'json',
                success:function(res){
                    if(res.code == 200){
                        layer.msg(res.message,{icon:6});
                        var index = parent.layer.getFrameIndex(window.name);
                        setTimeout('parent.layer.close('+index+')',2000);
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