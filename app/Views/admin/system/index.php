@section('title', '编辑系统设置')
@section('id', $id)
@section('content')
<style type="text/css">
    .layui-form-label{width: 100px;}
    .layui-input-block{margin-left: 130px;}
</style>

<div class="layui-form-item">
    <label class="layui-form-label">整站HTML：</label>
    <div class="layui-input-block">
        <div class="layui-inline">
            {foreach $stateRange as $k=>$v}
            <input type="radio" name="make_html" value="{$k}" title="{$v}" {if ($info['make_html'] == $k)} checked{/if}>
            {/foreach}
        </div>
        <div class="layui-inline" style="color: #f60;">
            开启整站HTML, 能够节省服务器资源
        </div>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">URL规则：</label>
    <div class="layui-input-block">
        <div class="layui-inline" style="line-height:38px;color: #f60;">
            列表页：{url}/index.html， 内容页URL：{url}/{id}.html
        </div>
    </div>
</div>


<div class="layui-form-item">
    <label class="layui-form-label">URL重写：</label>
    <div class="layui-input-block">
        <div class="layui-inline">
            {foreach $stateRange as $k=>$v}
            <input type="radio" name="url_rewrite" value="{$k}" title="{$v}" {if ($info['url_rewrite'] == $k)} checked{/if}>
            {/foreach}
        </div>
        <div class="layui-inline" style="color: #f60;">
            主要是后台页面的缓存，开启后变更模板不会及时更新缓存
        </div>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">视图缓存：</label>
    <div class="layui-input-block">
        <div class="layui-inline">
            {foreach $stateRange as $k=>$v}
            <input type="radio" name="enable_views_cache" value="{$k}" title="{$v}" {if ($info['enable_views_cache'] == $k)} checked{/if}>
            {/foreach}
        </div>
        <div class="layui-inline" style="color: #f60;">
            主要是后台页面的缓存，开启后变更模板不会及时更新缓存
        </div>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">路由缓存：</label>
    <div class="layui-input-block">
        <div class="layui-inline">
            {foreach $stateRange as $k=>$v}
            <input type="radio" name="enable_route_cache" value="{$k}" title="{$v}" {if ($info['enable_route_cache'] == $k)} checked{/if}>
            {/foreach}
        </div>
        <div class="layui-inline" style="color: #f60;">
            路由配置的缓存，能够有效提升性能
        </div>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">前台模板缓存：</label>
    <div class="layui-input-block">
        <div class="layui-inline">
            {foreach $stateRange as $k=>$v}
            <input type="radio" name="enable_template_cache" value="{$k}" title="{$v}" {if ($info['enable_template_cache'] == $k)} checked{/if}>
            {/foreach}
        </div>
        <div class="layui-inline" style="color: #f60;">
            缓存前台模板编译结果，提升性能
        </div>
    </div>
</div>

@endsection


@section('js')
<script>

    layui.use(['form','jquery','laypage', 'layer', 'upload'], function() {
        var form = layui.form,
            upload = layui.upload,
            $ = layui.jquery;

        form.render();
        var layer = layui.layer;
        form.verify({
            //    title: [/[\u4e00-\u9fa5]{2,12}$/, '标题必须2到12位汉字'],
            //    intro: [/[\u4e00-\u9fa5]{2,30}$/, '权限介绍必须2到30位汉字'],
        });
        form.on('submit(formDemo)', function(data) {
            $.ajax({
                url:"{{ url('saveEnv') }}",
                data:$('form').serialize(),
                type:'post',
                dataType:'json',
                success:function(res){
                    if(res.code == 200){
                        layer.msg(res.message,{icon:6});
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