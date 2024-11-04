@section('title', '管理员列表')
@section('header')
<div class="layui-inline">
    <button class="layui-btn layui-btn-sm layui-btn-normal addBtn" data-desc="添加链接" data-url="{{url('info')}}"><i class="layui-icon">&#xe654;</i></button>
    <button class="layui-btn layui-btn-sm layui-btn-warm freshBtn"><i class="layui-icon">&#x1002;</i></button>
</div>
<div class="layui-inline">
    <select id="model_id" name="model_id" class="layui-select-sm" lay-ignore>
        <option value="0">内容模型</option>
        {foreach $modelList as $k=>$v}
        <option value="{$k}">{$v}</option>
        {/foreach}
    </select>
</div>
<div class="layui-inline">
    <select id="search_type" name="search_type" class="layui-select-sm" lay-ignore>
        <option value="">搜索方式</option>
        <option value="title">名称</option>
        <option value="url">URL</option>
    </select>
</div>
<div class="layui-inline">
    <input type="text" name="search_text" value="{$input['search_text'] ?? ''}" id="search_text" class=" layui-input layui-input-sm" style="width: 120px;" />
</div>
<div class="layui-inline">
    <button class="layui-btn layui-btn-sm layui-btn-normal" value="搜索">搜索</button>
    <button type="button"  class="layui-btn layui-btn-sm layui-btn-normal" onclick="resetForm()" value="重置">重置</button>
</div>

@endsection
@section('table')
    <style type="text/css">
        .layui-textarea{ min-height: 60px;}
    </style>
    {if(empty($list))}
    <div class="no_data">
        没有符合条件的记录
    </div>
    {else}
    <table class="layui-table" lay-even lay-skin="nob" style="margin-bottom: 15px;">
        <colgroup>
            <col width="50">
            <col class="hidden-xs" width="50">
            <col class="hidden-xs" width="120">
            <col class="hidden-xs" width="300">
            <col class="hidden-xs" width="300">
            <col class="hidden-xs" width="130">
        </colgroup>
        <thead>
        <tr>
            <th><input type="checkbox" name="" lay-skin="primary" lay-filter="allChoose"></th>
            <th class="hidden-xs">ID</th>
            <th class="hidden-xs">内容模型</th>
            <th class="hidden-xs">名称</th>
            <th>URL</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {foreach $list as $info}
        <tr>
            <td><input type="checkbox" name="ids[]" value="{$info['id']}" lay-skin="primary" /></td>
            <td class="hidden-xs">{$info['id']}</td>
            <td class="hidden-xs">{$info['model_name']}</td>
            <td class="hidden-xs"><a href="{$info['url']}" target="_blank">{$info['title']}</a></td>
            <td class="hidden-xs">{$info['url']}</td>
            <td>
                <div class="layui-inline">
                    <button class="layui-btn layui-btn-xs layui-btn-normal edit-btn" data-id="{$info['id']}" data-h="500" data-desc="修改链接" data-url="{{url('info', ['id'=> $info['id']])}}">修改</button>
                    <button class="layui-btn layui-btn-xs layui-btn-danger del-btn" data-id="{$info['id']}" data-url="{{url('delete', ['id' => $info['id']])}}">删除</button>
                </div>
            </td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    {/if}
    {if(! empty($list))}
    <div style="margin-bottom: 15px;">
        <div class="layui-inline">
            <button class="layui-btn layui-btn-sm layui-btn-danger" onclick="batchDelete()">删除所选</button>
            <button class="layui-btn layui-btn-sm layui-btn-normal" onclick="move()" >将所选记录移动到</button>
        </div>
        <div class="layui-inline">
            <select id="move_category_id" name="move_category_id" class="layui-select-sm" style="height: 30px; line-height: 30px;" lay-ignore>
                <option value="0">链接类型</option>
                {foreach $categoryList as $k=>$v}
                <option value="{$k}">{$v}</option>
                {/foreach}
            </select>
        </div>
    </div>
    {/if}

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

        $('#search_type').val("{$input['search_type'] ?? ''}");
        $('#category_id').val("{$input['category_id'] ?? 0}");


        form.render();
        form.on('submit(formDemo)', function(data) {
        });
    });

    function move(){
        layui.use(['form', 'jquery', 'layer'], function() {
            var $ = layui.jquery;
            var layer = layui.layer;

            var token = $('input[name="_token"]').val();

            var chk_value =[];
            $('input[name="ids[]"]:checked').each(function(){
                chk_value.push($(this).val());
            });
            if(chk_value.length==0){
                layer.msg('请选择要移动的记录',{shift: 6,icon:5});
                return false;
            }
            var category_id = $('#move_category_id').val();
            if(category_id < 1){
                layer.msg('请选择链接类型',{shift: 6,icon:5});
                return false;
            }
            layer.confirm('确定要移动所选记录？', function (index){
                $.post('/link/move', {ids:chk_value, category_id:category_id, _token:token}, function (data){
                    console.log(data);
                    if(data.status == 1){
                        layer.msg('操作成功');
                        window.location.reload();
                    }
                },'json');
                layer.close(index);
            });

        });
    }

    function batchDelete(){
        layui.use(['form', 'jquery', 'layer'], function() {
            var $ = layui.jquery;
            var layer = layui.layer;

            var token = $('input[name="_token"]').val();

            var chk_value =[];
            $('input[name="ids[]"]:checked').each(function(){
                chk_value.push($(this).val());
            });
            if(chk_value.length==0){
                layer.msg('请选择要删除的记录',{shift: 6,icon:5});
                return false;
            }

            layer.confirm('确定要删除所选记录？', function (index){
                $.post('/link/batchDelete', {ids:chk_value, _token:token}, function (data){
                    console.log(data);
                    if(data.status == 1){
                        layer.msg('操作成功');
                        window.location.reload();
                    }
                },'json');
                layer.close(index);
            });

        });
    }

</script>
@endsection
@extends('admin.common.list')