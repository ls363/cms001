@section('title', '栏目列表')
@section('header')
<div class="layui-inline">
    <button class="layui-btn layui-btn-sm layui-btn-normal addBtn" data-desc="添加栏目" data-url="{{url('info')}}"><i class="layui-icon">&#xe654;</i></button>
    <button class="layui-btn layui-btn-sm layui-btn-warm freshBtn"><i class="layui-icon">&#x1002;</i></button>
</div>
<div class="layui-btn layui-btn-sm layui-btn-normal zkBtn" data-title="收缩类型"><i class="layui-icon">&#xe61a;</i></div>

@endsection
@section('table')
<table class="layui-table" lay-even >
    <colgroup>
        <col class="hidden-xs" width="50">
        <col class="hidden-xs" width="100">
        <col class="hidden-xs" width="150">
        <col class="hidden-xs" width="150">
        <col class="hidden-xs" width="150">
        <col width="180">
        <col width="180">
        <col class="hidden-xs" width="200">
        <col width="180">
    </colgroup>
    <thead>

    <tr>
        <th class="hidden-xs">ID</th>
        <th class="hidden-xs">序号</th>
        <th class="hidden-xs">内容模型</th>
        <th class="hidden-xs">栏目名称</th>
        <th class="hidden-xs">栏目URL</th>
        <th class="hidden-xs">状态</th>
        <th>列表页模板</th>
        <th>内容页模板</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    {if !empty($list)}
    {foreach $list as $v}
    <tr data-path="{$v['path']}-{$v['id']}" id='node-{$v['id']}' data-id="{$v['id']}" class="parent expanded" {if($v['parent_id'] > 0)}parentid="{$v['parent_id']}"{/if}>
        <td class="hidden-xs">{$v.id}</td>
        <td class="hidden-xs"><input type="number" name="number" autocomplete="off" class="layui-input" style="width: 50px; text-align: center; height: 30px; line-height:30px;" value="{$v['sort']}" data-id="{$v['id']}" data-url="{{url('sort')}}" onchange="changeSort('link_category',this)"></td>
        <td class="hidden-xs">{$v.model_name}</td>
        <td class="hidden-xs">
            {$v.html}
            {if($v['child'] > 0)}<a class="layui-btn layui-btn-xs layui-btn-normal showSubBtn" data-id='{$v['id']}' data-path="{$v['path']}">-</a>{endif}

        </td>
        <td class="hidden-xs">{$v.url}</td>
        <th class="hidden-xs"><input type="checkbox" name="state" value="{$v['id']}" {$v['state'] == 1 ?'checked':''} lay-skin="switch" lay-filter="switchState" lay-text="上架|下架" title="开关"></th>
        <td>{$v['list_template']}</td>
        <td>{$v['content_template']}</td>
        <td>
            <div class="layui-inline">
            <button class="layui-btn layui-btn-xs layui-btn-normal edit-btn" data-id="{$v.id}" data-desc="添加子类" data-url="{{ url('info', ['parent_id'=>$v['id']]) }}">添加</button>
            {if $system['make_html']==1}
            <button class="layui-btn layui-btn-xs layui-btn-normal make-btn" data-id="{$v.id}" data-desc="更新" data-url="{{ url('admin/make_html/makeClassify', ['class_id'=>$v['id']]) }}">更新</button>
            {/if}
            <button class="layui-btn layui-btn-xs layui-btn-normal edit-btn" data-id="{$v.id}" data-desc="修改栏目" data-url="{{ url('info', ['id'=>$v['id']]) }}">修改</button>
            <button class="layui-btn layui-btn-xs layui-btn-danger del-btn" data-id="{$v.id}" data-url="{{ url('delete', ['id'=> $v['id']]) }}">删除</button>
            </div>
        </td>
    </tr>
    {/foreach}
    {/if}
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

        $('button.make-btn').on('click', function() {
            var url=$(this).attr('data-url');
            $.get(url, {}, function (data) {
                if (data.code == 200) {
                    layer.msg("栏目HTML更新成功");
                } else {
                    layer.msg("栏目HTML更新失败");
                }
            }, 'json'
            );
        });


        //栏目展示隐藏
        $('.showSubBtn').on('click', function() {
            var _this = $(this);
            var id = _this.attr('data-id');
            console.log(_this.html());
            var data_path = _this.attr('data-path');
            var childAll2 = $('tr[data-path^=' + data_path +'-'+id+ '-]');
            if(_this.html() == '-'){
                _this.html('+');
                childAll2.hide();
            }else{
                _this.html('-');
                childAll2.show();
            }
        });
        $('.zkBtn').click(function() {
            if($(this).attr('data-title')=='展开类型'){
                $(this).attr('data-title','收缩类型');
                $(this).html('<i class="layui-icon">&#xe61a;</i>');
                $('.showSubBtn').html('-');
                $('tr').css('display','');
            }else{
                $(this).attr('data-title','展开类型');
                $(this).html('<i class="layui-icon">&#xe602;</i>');
                $('.showSubBtn').html('+');
                $("[parentid]").css('display','none');
            }
        }).mouseenter(function() {
            layer.tips($(this).attr('data-title'), $(this),{tips: [3, '#40455C']});
        })


        form.on('switch(switchState)', function (obj) {
            var state = this.checked ? 1 : 2;
            $.get('{{ url('setState') }}', {id: this.value, state: state}, function (data) {
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