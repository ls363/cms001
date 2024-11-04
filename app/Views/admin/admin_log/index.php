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
                    <col>
                    <col>
                    <col>
                    <col class="hidden-xs" width="200">
                    <col width="200">
                </colgroup>
                <thead>

                <tr>
                    <th class="hidden-xs">ID</th>
                    <th class="hidden-xs">用户名</th>
                    <th>真实姓名</th>
                    <th>操作信息</th>
                    <th>URL</th>
                    <th class="hidden-xs">操作时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                {foreach $list as $v}
                <tr>
                    <td class="hidden-xs">{$v.id}</td>
                    <td class="hidden-xs">{$v.admin_id}</td>
                    <td>{$v.admin_id}</td>
                    <td>{$v.log_info}</td>
                    <td>{$v.log_uri}</td>
                    <td class="hidden-xs">{$v.created_at}</td>
                    <td>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-sm layui-btn-normal edit-btn" data-id="{$v.id}" data-desc="修改" data-url="{{ url('edit', ['id'=>$v['id']]) }}"><i class="layui-icon">&#xe642;</i></button>
                            <button class="layui-btn layui-btn-sm layui-btn-danger del-btn" data-id="{$v.id}" data-url="{{ url('delete', ['id'=> $v['id']]) }}"><i class="layui-icon">&#xe640;</i></button>
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