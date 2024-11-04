@section('title', '菜单列表')
@section('header')
    <div class="layui-inline">
    <button class="layui-btn layui-btn-sm layui-btn-normal addBtn" data-desc="添加内容模型" data-url="{{url('info')}}"><i class="layui-icon">&#xe654;</i> 添加内容模型</button>
    </div>
@endsection
@section('table')
    <table class="layui-table" lay-skin="line">
        <colgroup>
            <col width="50">
            <col class="hidden-xs" width="50">
            <col class="hidden-xs" width="100">
            <col width="150">
            <col width="200">
            <col width="180">
            <col width="180">
            <col width="280">
        </colgroup>
        <thead>
        <tr>
            <th><input type="checkbox" name="" lay-skin="primary" lay-filter="allChoose"></th>
            <th class="hidden-xs">ID</th>
            <th class="hidden-xs">排序</th>
            <th>模型名称</th>
            <th>模型类型</th>
            <th>列表页模板</th>
            <th>内容页模板</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {foreach $list as $v}
            <tr>
                <td><input type="checkbox" name="" lay-skin="primary" data-id="{$v['id']}"></td>
                <td class="hidden-xs">{$v['id']}</td>
                <td class="hidden-xs"><input type="number" style="width: 50px;" name="sort" autocomplete="off" class="layui-input" value="{$v['sort']}" data-id="{$v['id']}" data-url="{{url('sort')}}" onchange="changeSort('menus',this)"></td>
                <td>{$v['title']}</td>
                <td>{$v['type_name']}</td>
                <td>{$v['list_template']}</td>
                <td>{$v['content_template']}</td>
                <td>
                    <div class="layui-inline">
                        <a class="layui-btn layui-btn-xs layui-btn-normal  edit-btn-right" data-id="{$v['id']}" data-desc="模型扩展字段" data-url="{{url('admin/content_module_extend/index', ['model_id' => $v['id'] ])}}">扩展字段</a>
                        <a class="layui-btn layui-btn-xs layui-btn-normal  edit-btn" data-id="{$v['id']}" data-desc="修改内容模型" data-url="{{url('info', ['id' => $v['id'] ])}}">修改</a>
                        <a class="layui-btn layui-btn-xs layui-btn-warm module-btn" data-id="{$v['id']}" data-url="{{ url('refresh',['id' => $v['id']]) }}">刷新</a>
                        <a class="layui-btn layui-btn-xs layui-btn-danger del-btn" data-id="{$v['id']}" data-url="{{ url('delete',['id' => $v['id']]) }}">删除</a>
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
            });

            $('a.module-btn').on('click', function() {
                var url=$(this).attr('data-url');
                console.log(url);
                $.get(url, {}, function (data) {
                        if (data.code == 200) {
                            layer.msg(data.message);
                        } else {
                            layer.msg(data.message);
                        }
                    }, 'json'
                );
            });

            //编辑栏目
            $('#table-list').on('click', '.edit-btn-right', function() {
                var id=$(this).attr('data-id');
                var url=$(this).attr('data-url');
                var desc=$(this).attr('data-desc');
                //处理来源窗口
                let frameId = parent.getRightFrameId();
                if(url.indexOf('?') == -1){
                    url += '?sourceFrameId='+frameId
                }else{
                    url += '&sourceFrameId='+frameId
                }
                //在保侧ifarme中打开,执行操作完成刷新
                parent.openRightFrame('content_'+id, desc, url);
                return false;
            })
        });
    </script>
@endsection
@extends('admin.common.list')
