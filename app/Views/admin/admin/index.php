@section('title', '管理员列表')
@section('header')
<div class="layui-inline">
    <button class="layui-btn layui-btn-sm layui-btn-normal addBtn" data-desc="添加管理员" data-url="{{url('info')}}"><i class="layui-icon">&#xe654;</i></button>
    <button class="layui-btn layui-btn-sm layui-btn-warm freshBtn"><i class="layui-icon">&#x1002;</i></button>
</div>
@endsection
@section('table')
    <table class="layui-table" lay-even lay-skin="nob">
        <colgroup>
            <col class="hidden-xs" width="50">
            <col class="hidden-xs" width="150">
            <col width="150">
            <col width="100">
            <col width="100">
            <col width="100">
            <col class="hidden-xs" width="200">
            <col width="200">
        </colgroup>
        <thead>

        <tr>
            <th class="hidden-xs">ID</th>
            <th class="hidden-xs">用户名</th>
            <th>姓名</th>
            <th>邮箱</th>
            <th>手机号</th>
            <th>性别</th>
            <th class="hidden-xs">创建时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {foreach $list as $v}
        <tr>
            <td class="hidden-xs">{$v.id}</td>
            <td class="hidden-xs">{$v.username}</td>
            <td>{$v.real_name}</td>
            <td>{$v.email}</td>
            <td>{$v.phone}</td>
            <td>{$v.sex_text}</td>
            <td class="hidden-xs">{$v.created_at}</td>
            <td>
                <div class="layui-inline">
                    <button class="layui-btn layui-btn-xs layui-btn-normal edit-btn" data-id="{$v.id}" data-desc="修改" data-url="{{ url('info', ['id'=>$v['id']]) }}">修改</button>
                    <button class="layui-btn layui-btn-xs layui-btn-danger del-btn" data-id="{$v.id}" data-url="{{ url('delete', ['id'=> $v['id']]) }}">删除</button>
                </div>
            </td>
        </tr>
        {/foreach}
        </tbody>
    </table>

    {$pageBar}

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
        form.on('submit(formDemo)', function(data) {
        });
    });
</script>
@endsection
@extends('admin.common.list')