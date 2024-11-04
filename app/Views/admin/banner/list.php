@section('title', 'Banner列表')
@section('header')
<div class="layui-inline">
    <button class="layui-btn layui-btn-sm layui-btn-normal addBtn" data-desc="添加Banner" data-url="{{url('info')}}"><i class="layui-icon">&#xe654;</i></button>
    <button class="layui-btn layui-btn-sm layui-btn-warm freshBtn"><i class="layui-icon">&#x1002;</i></button>
</div>
@endsection
@section('table')
<style type="text/css">
    .bannerList img{max-height: 150px;}
</style>
<table class="layui-table bannerList" lay-even lay-skin="nob">
    <colgroup>
        <col class="hidden-xs" width="50">
        <col class="hidden-xs" width="200">
        <col class="hidden-xs" width="150">
        <col width="150">
        <col class="hidden-xs" width="150">
        <col class="hidden-xs" width="80">
        <col class="hidden-xs" width="250">
        <col width="200">
    </colgroup>
    <thead>
    <tr>
        <th class="hidden-xs">ID</th>
        <th class="hidden-xs">图片</th>
        <th class="hidden-xs">类型</th>
        <th class="hidden-xs">描述</th>
        <th class="hidden-xs">状态</th>
        <th>序号</th>
        <th class="hidden-xs">创建时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    {foreach $list as $info}
    <tr>
        <td class="hidden-xs">{$info['id']}</td>
        <td class="hidden-xs">{if(! empty($info['cover']))}<a href="{$info['cover'] ?? ''}" target="_blank"><img src="{$info['cover'] ?? ''}" /></a>{/if}</td>
        <th class="hidden-xs">{$info['type'] == 1 ?'轮播图':'其它'} </th>
        <td class="hidden-xs">{$info['title']}</td>
        <th class="hidden-xs"><input type="checkbox" name="state" value="{$info['id']}" {$info['state'] == 1 ?'checked':''} lay-skin="switch" lay-filter="switchState" lay-text="上架|下架" title="开关"></th>
        <td>{$info['sort']}</td>
        <td class="hidden-xs">{$info['created_at']}</td>
        <td>
            <div class="layui-inline">
                <button class="layui-btn layui-btn-xs layui-btn-normal edit-btn" data-id="{$info['id']}" data-desc="修改Banner" data-url="{{url('info', ['id' => $info['id']])}}">修改</button>
                <button class="layui-btn layui-btn-xs layui-btn-danger del-btn" data-id="{$info['id']}" data-url="{{url('delete', ['id' => $info['id']])}}">删除</button>
            </div>
        </td>
    </tr>
    {/foreach}
    </tbody>
</table>
@endsection
@section('js')
<script>
        layui.use(['form', 'jquery','laydate', 'layer'], function() {
            var form = layui.form,
                $ = layui.jquery,
                laydate = layui.laydate,
                layer = layui.layer
            ;

            form.render();

            form.on('switch(switchState)', function (obj) {
                var state = this.checked ? 1 : 2;
                $.get('{{url('setState')}}', {id: this.value, state: state}, function (data) {
                    if (data.code == 0) {
                        layer.msg("状态修改成功");
                    } else {
                        layer.msg("状态修改失败");
                    }
                });
            });

            form.on('submit(formDemo)', function(data) {
                console.log(data);
            });
        });
    </script>
@endsection
@extends('admin.common.list')