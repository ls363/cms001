@section('title', '模型扩展字段列表')
@section('header')
    <div class="layui-inline">
    <button class="layui-btn layui-btn-sm layui-btn-normal addBtn" data-desc="添加模型扩展字段" data-url="{{url('info', ['model_id' => $model_id])}}"><i class="layui-icon">&#xe654;</i></button>
    <button class="layui-btn layui-btn-sm layui-btn-warm freshBtn"><i class="layui-icon">&#x1002;</i></button>
    <div class="layui-btn layui-btn-sm layui-btn-normal zkBtn" data-title="展开菜单"><i class="layui-icon">&#xe602;</i></div>
    </div>
@endsection
@section('table')
    <table class="layui-table" lay-skin="line">
        <colgroup>
            <col width="50">
            <col class="hidden-xs" width="50">
            <col class="hidden-xs" width="100">
            <col width="180">
            <col width="200">
            <col width="180">
            <col width="130">
        </colgroup>
        <thead>
        <tr>
            <th><input type="checkbox" name="" lay-skin="primary" lay-filter="allChoose"></th>
            <th class="hidden-xs">ID</th>
            <th class="hidden-xs">排序</th>
            <th>显示名称</th>
            <th>存储名称</th>
            <th>字段类型</th>
            <th>序号</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {foreach $list as $v}
            <tr>
                <td><input type="checkbox" name="" lay-skin="primary" data-id="{$v['id']}"></td>
                <td class="hidden-xs">{$v['id']}</td>
                <td class="hidden-xs"><input type="number" style="width: 50px;" name="sort" autocomplete="off" class="layui-input" value="{$v['sort']}" data-id="{$v['id']}" data-url="{{url('sort')}}" onchange="changeSort('menus',this)"></td>
                <td>{$v['field_name']}</td>
                <td>{$v['field_input']}</td>
                <td>{$v['field_type_name']}</td>
                <td>{$v['sort']}</td>
                <td>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-sm layui-btn-normal  edit-btn" data-id="{$v['id']}" data-desc="修改模型扩展字段" data-url="{{url('info', ['id' => $v['id'] ])}}"><i class="layui-icon">&#xe642;</i></button>
                        <button class="layui-btn layui-btn-sm layui-btn-danger del-btn" data-id="{$v['id']}" data-url="{{ url('delete',['id' => $v['id']]) }}"><i class="layui-icon">&#xe640;</i></button>
                    </div>
                </td>
            </tr>         
        {/foreach}
        </tbody>
    </table>
@endsection
@section('js')
    <script>
        layui.use(['jquery'], function() {
            var $=layui.jquery;
            //栏目展示隐藏
            $('.showSubBtn').on('click', function() {
                var _this = $(this);
                var id = _this.attr('data-id');
                var parent = _this.parents('.parent');
                var child = $('.child-node-' + id);

                var childAll = $('tr[parentid=' + id + ']');
                if(parent.hasClass('collapsed')) {
                    _this.html('-');
                    parent.addClass('expanded').removeClass('collapsed');
                    child.css('display', '');
                } else {
                    _this.html('+');
                    parent.addClass('collapsed').removeClass('expanded');
                    child.css('display', 'none');
                    childAll.addClass('collapsed').removeClass('expanded').css('display', 'none');
                    childAll.find('.showSubBtn').html('+');
                }
            });
            $('.zkBtn').click(function() {
                if($(this).attr('data-title')=='展开菜单'){
                    $(this).attr('data-title','收缩菜单');
                    $(this).html('<i class="layui-icon">&#xe61a;</i>');
                    $('.showSubBtn').html('-');
                    $('tr').css('display','');
                }else{
                    $(this).attr('data-title','展开菜单');
                    $(this).html('<i class="layui-icon">&#xe602;</i>');
                    $('.showSubBtn').html('+');
                    $("[parentid]").css('display','none');
                }
            }).mouseenter(function() {
                layer.tips($(this).attr('data-title'), $(this),{tips: [3, '#40455C']});
            })
        });
    </script>
@endsection
@extends('admin.common.list')
