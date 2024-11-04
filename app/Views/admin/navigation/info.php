@section('title', '编辑链接分类')
@section('id', $id)
@section('content')
        <div class="layui-form-item">
            <label class="layui-form-label">导航名称：</label>
            <div class="layui-input-block">
                <input type="text" value="{$info.title ?? ''}" name="title" required lay-verify="name" placeholder="请输入导航名称" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">导航URL：</label>
            <div class="layui-input-block">
                <input type="text" value="{$info.url ?? ''}" name="url" placeholder="请输入导航URL" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">导航简介：</label>
            <div class="layui-input-block">
                <textarea name="intro" placeholder="请输入导航简介" class="layui-textarea" required >{$info.intro ?? ''}</textarea>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">状态：</label>
            <div class="layui-input-block">
                {foreach $showRange as $k=>$v}
                <input type="radio" name="state" value="{$k}" title="{$v}" {if ($info['state'] == $k)} checked{/if}>
                {/foreach}

            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">序号：</label>
            <div class="layui-input-block">
                <input type="number"  value="{$info['sort'] ?? ''}" name="sort" id="sort" required placeholder="" autocomplete="off" class="layui-input" style="width: 100px;">
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