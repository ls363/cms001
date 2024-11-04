@section('title', '模型字段编辑')
@section('id', $id)
@section('content')

<style type="text/css">
    .layui-form-label{width: 100px;}
    .layui-input-block{margin-left: 130px;}
    .info_tip{margin-bottom:10px; margin-left: 20px; border: solid 2px #f60; border-radius:5px; background: #fff;  padding: 10px; color: #f60;}
</style>

<div class="info_tip">注意：新增字段完成后，模型表会自动增加该字段，并且生成内容编辑页面的输入框。如果修改了字段，变更了字段类型，则会删除模型表中的字段(录入的内容会丢失)，重新创建，否则只更新内容编辑的页面。此功能最好在录入内容之前完成。</div>

<input type="hidden" name="model_id" value="{$model_id}">
<div class="layui-form-item">
    <label class="layui-form-label">内容模型：</label>
    <div class="layui-input-block" style="line-height: 38px; color: #f60;">
    {$modelName}
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">字段显示名称：</label>
    <div class="layui-input-block">
        <div class="layui-inline">
        <input type="text" style="width: 200px;" value="{$info['field_name'] ?? ''}" name="field_name" required lay-verify="field_name" placeholder="请输入字段名称" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-inline" style="color: #f60;">
            显示的字段描述
        </div>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">字段存储名称：</label>
    <div class="layui-input-block">
        <div class="layui-inline">
            <input type="text" style="width: 200px;" value="{$info['field_input'] ?? ''}" name="field_input" required lay-verify="field_input" placeholder="请输入字段标识" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-inline" style="color: #f60;">
            输入框及数据表的字段名称
        </div>    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">字段类型：</label>
    <div class="layui-input-block">
        <select name="field_type" id="field_type" required lay-verify="field_type" lay-filter="field_type">
            <option value="0" selected>请选择</option>
            {foreach $typeRange as $k => $v}
            <option value="{$k}" {if(isset($info['field_type']) && $info['field_type'] == $k)}selected{/if}>{$v}</option>
            {/foreach}
        </select>
    </div>
</div>

<div class="layui-form-item" id="row_field_option" style="display: none;">
    <label class="layui-form-label">选项的值：</label>
    <div class="layui-input-block">
        <textarea name="field_option" placeholder="请输入选项的值，请以 “,” 隔开" class="layui-textarea">{$info.field_option ?? ''}</textarea>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">序号：</label>
    <div class="layui-input-block">
        <div class="layui-inline">
        <input type="number" style="width: 50px;" value="{$info['sort'] ?? ''}" name="sort" required  placeholder="请输入数字" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-inline" style="color: #f60;">
            扩展信息TAB中的显示顺序，充号小的在前面
        </div>
    </div>
</div>



@endsection
@section('id',$id)
@section('js')
<script>

    layui.use(['form','laypage', 'layer', 'jquery'], function() {
        var $ = layui.jquery;
        var form = layui.form;
        form.render();
        var layer = layui.layer;

        form.verify({
            field_name: function (value){
                if(value == '' || value <1){
                    return '请输入字段显示名称';
                }
            },
            field_input: function (value){
                if(value == '' || value <1){
                    return '请输入字段存储名称';
                }
            },
            field_type: function (value){
                if(value == '' || value <1){
                    return '请选择字段类型';
                }
            }
        });

        form.on('select(field_type)', function(data){
            console.log('field_type',data);
            if(data.value == 'select' || data.value == 'radio' || data.value == 'checkbox'){
                if($('#row_field_option').is(":hidden")){
                    $('#row_field_option').show();
                }
            }else{
                console.log($('#row_field_option').attr('display'));
                if($('#row_field_option').is(":visible")){
                    $('#row_field_option').hide();
                }
            }
        });
        form.render("select")


        form.on('submit(formDemo)', function(data) {
            $.ajax({
                url:"{{url('save')}}",
                data:$('form').serialize(),
                type:'post',
                dataType:'json',
                success:function(res){
                    if(res.code == 200){
                        layer.msg(res.message,{icon:6});
                        var index = parent.layer.getFrameIndex(window.name);
                        setTimeout('parent.layer.close('+index+')',2000);
                        //parent.layer.close(index);
                    }else{
                        layer.msg(res.message,{shift: 6,icon:5});
                    }
                },
                error : function(XMLHttpRequest, textStatus, errorThrown) {
                    layer.msg('网络失败', {time: 1000});
                }
            });
            return false;
        });
    });
</script>
@endsection
@extends('admin.common.edit')
