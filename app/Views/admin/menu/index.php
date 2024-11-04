@section('title', '菜单列表')
@section('header')
    <div class="layui-inline">
    <button class="layui-btn layui-btn-sm layui-btn-normal addBtn" data-desc="添加菜单" data-url="{{url('add')}}"><i class="layui-icon">&#xe654;</i></button>
    <button class="layui-btn layui-btn-sm layui-btn-warm freshBtn"><i class="layui-icon">&#x1002;</i></button>
    <div class="layui-btn layui-btn-sm layui-btn-normal zkBtn" data-title="展开菜单"><i class="layui-icon">&#xe602;</i></div>
    </div>
@endsection
@section('table')
    <table class="layui-table" lay-skin="line">
        <colgroup>
            <col width="50">
            <col class="hidden-xs" width="80">
            <col class="hidden-xs" width="100" >
            <col class="hidden-xs" width="180">
            <col width="300">
            <col>
            <col width="140">
        </colgroup>
        <thead>
        <tr>
            <th><input type="checkbox" name="" lay-skin="primary" lay-filter="allChoose"></th>
            <th class="hidden-xs">ID</th>
            <th class="hidden-xs">排序</th>
            <th class="hidden-xs">图标</th>
            <th>菜单名称</th>
            <th>URL</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {foreach $menus as $branch}
            <tr id='node-{$branch['id']}' class="parent collapsed">
                <td><input type="checkbox" name="" lay-skin="primary" data-id="{$branch['id']}"></td>
                <td class="hidden-xs">{$branch['id']}</td>
                <td class="hidden-xs"><input type="number" style="width: 50px;" name="title" autocomplete="off" class="layui-input" value="{$branch['sort']}" data-id="{$branch['id']}" data-url="{{url('/sort')}}" onchange="changeSort('menus',this)"></td>
        <td class="hidden-xs"><i class="layui-icon layui-btn-sm">{$branch['icon']}</i></td>
                <td>{$branch['title']}
                    <a class="layui-btn layui-btn-xs layui-btn-normal showSubBtn" data-id='{$branch['id']}'>-</a>
                </td>
        <td>{$branch['uri']}</td>
                <td>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-xs layui-btn-normal  edit-btn" data-id="{$branch['id']}" data-desc="修改菜单" data-url="{{url('edit', ['id' => $branch['id'] ])}}">修改</button>
                        <button class="layui-btn layui-btn-xs layui-btn-danger del-btn" data-id="{$branch['id']}" data-url="{{ url('delete',['id' => $branch['id']]) }}">删除</button>
                    </div>
                </td>
            </tr>
            {if isset($branch['children'])}
                {foreach $branch['children'] as $child_branch}
                    <tr id='node-{$branch['id']}' class="child-node-{$branch['id']} parent collapsed" parentid="{$branch['id']}">
                        <td><input type="checkbox" name="" lay-skin="primary" data-id="{$child_branch['id']}"></td>
                        <td class="hidden-xs">{$child_branch['id']}</td>
                        <td class="hidden-xs"><input type="text" name="title" style="width: 50px;" autocomplete="off" class="layui-input" value="{$child_branch['sort']}" data-id="{$child_branch['id']}" data-url="{url('/sort')}" onchange="changeSort('menus',this)"></td>
        <td class="hidden-xs"><i class="layui-icon layui-btn-sm">{$child_branch['icon']}</i></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─{$child_branch['title']}</td>
        <td>{$child_branch['uri']}</td>
                        <td>
                            <div class="layui-inline">
                                <button class="layui-btn layui-btn-xs layui-btn-normal edit-btn" data-id="{$child_branch['id']}"  data-desc="修改菜单" data-url="{{ url('edit', ['id' => $child_branch['id']]) }}">修改</button>
                                <button class="layui-btn layui-btn-xs layui-btn-danger del-btn" data-id="{$child_branch['id']}" data-url="{{ url('delete', ['id' => $child_branch['id']]) }}">删除</button>
                            </div>
                        </td>
                    </tr>
                {/foreach}
            {/if}
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
