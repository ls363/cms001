@section('title', '留言列表')
@section('header')
<div class="layui-inline">
    <button class="layui-btn layui-btn-sm layui-btn-warm freshBtn"><i class="layui-icon">&#x1002;</i></button>
</div>

<div class="layui-inline">
    <select id="search_type" name="search_type" class="layui-select-sm" lay-ignore>
        <option value="">搜索方式</option>
        <option value="linkman">联系人</option>
        <option value="mobile">手机</option>
        <option value="content">留言内容</option>
        <option value="reply">回复内容</option>
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
            <col class="hidden-xs" width="120">
            <col class="hidden-xs" width="300">
            <col class="hidden-xs" width="300">
            <col class="hidden-xs" width="150">
        </colgroup>
        <thead>
        <tr>
            <th><input type="checkbox" name="" lay-skin="primary" lay-filter="allChoose"></th>
            <th class="hidden-xs">ID</th>
            <th class="hidden-xs">联系人</th>
            <th class="hidden-xs">手机</th>
            <th>留言内容</th>
            <th>回复内容</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {foreach $list as $info}
        <tr>
            <td><input type="checkbox" name="ids[]" value="{$info['id']}" lay-skin="primary" /></td>
            <td class="hidden-xs">{$info['id']}</td>
            <td class="hidden-xs">{$info['linkman']}</td>
            <td class="hidden-xs">{$info['mobile']}</td>
            <td class="hidden-xs">{$info['content']}</td>
            <td class="hidden-xs">{$info['reply']}</td>
            <td>
                <div class="layui-inline">
                    <button class="layui-btn layui-btn-xs layui-btn-normal edit-btn" data-id="{$info['id']}" data-h="500" data-desc="回复留言" data-url="{{url('info', ['id'=> $info['id']])}}">回复</button>
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
                $.post('{{url('batchDelete')}}', {ids:chk_value, _token:token}, function (data){
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