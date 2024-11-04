@section('title', '上传文件列表')
@section('header')
<div class="layui-inline">
    <button class="layui-btn layui-btn-sm layui-btn-normal addBtn" data-desc="上传文件" data-url="{{url('info')}}"><i class="layui-icon">&#xe654;</i></button>
    <button class="layui-btn layui-btn-sm layui-btn-warm freshBtn"><i class="layui-icon">&#x1002;</i></button>
</div>
@endsection
@section('table')
<input id="callback" value="" class="layui-input"/>
<table class="layui-table" lay-even lay-skin="nob">
    <colgroup>
        <col class="hidden-xs" width="50">
        <col class="hidden-xs" width="150">
        <col width="150">
        <col width="100">
        <col width="100">
        <col width="100">
        <col width="200">
    </colgroup>
    <thead>

    <tr>
        <th class="hidden-xs">ID</th>
        <th class="hidden-xs">预览</th>
        <th>原始文件名</th>
        <th>宽度</th>
        <th>高度</th>
        <th>大小</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    {foreach $list as $v}
    <tr>
        <td class="hidden-xs">{$v.id}</td>
        <td><img src="{$v.url}" width="100" /></td>
        <td>{$v.original}</td>
        <td>{$v.width}</td>
        <td>{$v.height}</td>
        <td>{$v.size}</td>
        <td>
            <div class="layui-inline">
                <button class="layui-btn layui-btn-xs layui-btn-danger choose-file" data-id="{$v.id}" data-url="{{ url('delete', ['id'=> $v['id']]) }}">选择</button>
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
    var callbackInput  = "";
    //设置回调参数
    function setCallbackInput(inputName){
        callbackInput = inputName;
        layui.use(['jquery'], function(){
            var $ = layui.jquery;
            $('#callback').val(inputName);
        });
    }

    layui.use(['form', 'jquery','laydate', 'layer'], function() {
        var form = layui.form,
            $ = layui.jquery,
            laydate = layui.laydate,
            layer = layui.layer
        ;
        form.render();
        form.on('submit(formDemo)', function(data) {
        });

        $('button.choose-file').on('click', function() {
            var url=$(this).attr('data-url');
            parent.saveTemplate(callback, url);
        });
    });



    function closePopup(){
        var index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
    }
</script>
@endsection
@extends('admin.common.list')