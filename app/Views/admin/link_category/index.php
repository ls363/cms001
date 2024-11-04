@section('title', '管理员列表')
@section('header')
<div class="layui-inline">
    <button class="layui-btn layui-btn-sm layui-btn-normal addBtn" data-desc="添加链接分类" data-url="{{url('add')}}"><i class="layui-icon">&#xe654;</i></button>
    <button class="layui-btn layui-btn-sm layui-btn-warm freshBtn"><i class="layui-icon">&#x1002;</i></button>
</div>
@endsection
@section('table')
            <table class="layui-table" lay-even lay-skin="nob">
                <colgroup>
                    <col class="hidden-xs" width="50">
                    <col class="hidden-xs" width="100">
                    <col class="hidden-xs" width="150">
                    <col>
                    <col class="hidden-xs" width="200">
                    <col width="180">
                    <col width="130">
                </colgroup>
                <thead>

                <tr>
                    <th class="hidden-xs">ID</th>
                    <th class="hidden-xs">序号</th>
                    <th class="hidden-xs">标题</th>
                    <th>简介</th>
                    <th class="hidden-xs">状态</th>
                    <th class="hidden-xs">创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                {foreach $classifyList as $v}
                <tr>
                    <td class="hidden-xs">{$v.id}</td>
                    <td class="hidden-xs"><input type="number" name="number" autocomplete="off" class="layui-input" style="width: 50px; text-align: center; height: 30px; line-height:30px;" value="{$v['sort']}" data-id="{$v['id']}" data-url="{{url('sort')}}" onchange="changeSort('link_category',this)"></td>
                    <td class="hidden-xs">{$v.title}</td>
                    <td>{$v.intro}</td>
                    <th class="hidden-xs"><input type="checkbox" name="state" value="{$v['id']}" {$v['state'] == 1 ?'checked':''} lay-skin="switch" lay-filter="switchState" lay-text="上架|下架" title="开关"></th>
                    <td class="hidden-xs">{$v.created_at}</td>
                    <td>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-xs layui-btn-normal edit-btn" data-id="{$v.id}" data-desc="修改链接分类" data-url="{{ url('edit', ['id'=>$v['id']]) }}">修改</button>
                            <button class="layui-btn layui-btn-xs layui-btn-danger del-btn" data-id="{$v.id}" data-url="{{ url('delete', ['id'=> $v['id']]) }}">删除</button>
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

        form.on('switch(switchState)', function (obj) {
            var state = this.checked ? 1 : 2;
            $.get('{{url('setState')}}', {id: this.value, state: state}, function (data) {
                if (data.code == 200) {
                    layer.msg("状态修改成功");
                } else {
                    layer.msg("状态修改失败");
                }
            }, 'json');
        });

        form.render();
        form.on('submit(formDemo)', function(data) {
        });
    });
</script>
@endsection
@extends('admin.common.list')