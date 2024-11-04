@section('title', '内容模型编辑')
@section('id', $id)
@section('content')

<style type="text/css">
    .layui-form-label{width: 100px;}
    .layui-input-block{margin-left: 130px;}
</style>


<div class="layui-form-item">
    <label class="layui-form-label">模型名称：</label>
    <div class="layui-input-block">
        <input type="text" value="{$info['title'] ?? ''}" name="title" required lay-verify="title" placeholder="请输入模型名称" autocomplete="off" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">模型简介：</label>
    <div class="layui-input-block">
        <textarea name="intro" placeholder="请输入模型简介" class="layui-textarea">{$data.intro ?? ''}</textarea>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">模型的表：</label>
    <div class="layui-input-block">
        <div class="layui-inline">
        <input type="text" value="{$info['table'] ?? ''}" style="width: 160px;" name="table" id="table" required lay-verify="table" placeholder="请输入模型的数据库表" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-inline" style="color: #FF6600">
            模型数据存储的表名，不需要跟表前辍，如: article
        </div>
    </div>
</div>


<div class="layui-form-item">
    <label class="layui-form-label">模型类型：</label>
    <div class="layui-input-block">
        <select name="type" id="type" lay-verify="required">
            <option value="0" selected>请选择</option>
            {foreach $typeRange as $k => $v}
            <option value="{$k}" {if(isset($info['type']) && $info['type'] == $k)}selected{/if}>{$v}</option>
            {/foreach}
        </select>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">序号：</label>
    <div class="layui-input-block">
        <input type="number" value="{$info['sort'] ?? ''}" name="sort" required  placeholder="请输入数字" autocomplete="off" class="layui-input">
    </div>
</div>

{if $id == 0}
<div class="layui-form-item">
    <label class="layui-form-label"></label>
    <div class="layui-input-block">
        <input type="button" value="根据模型名称初始化模板" class="layui-btn layui-btn-sm layui-btn-danger" onclick="initTemplateFile()" >
    </div>
</div>
{/if}

<div class="layui-form-item">
    <label class="layui-form-label">列表页模板：</label>
    <div class="layui-input-block">
        <div class="layui-inline">
        <input style="width: 300px;" readonly type="text" value="{$info['list_template'] ?? ''}" name="list_template" id="list_template"  placeholder="请选择列表页模板" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-inline">
            <input type="button" value="选择" class="layui-btn layui-btn-sm layui-btn-normal" onclick="openTemplateChoose('list_template')" >
        </div>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">内容页模板：</label>
    <div class="layui-input-block">
        <div class="layui-inline">
        <input type="text" style="width: 300px;" readonly value="{$info['content_template'] ?? ''}" name="content_template" id="content_template" placeholder="请选择内容页模板" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-inline">
        <input type="button" value="选择" class="layui-btn layui-btn-sm layui-btn-normal" onclick="openTemplateChoose('content_template')" >
        </div>
    </div>
</div>


@endsection
@section('id',$id)
@section('js')
<script>

    function initTemplateFile(){
        layui.use(['form', 'layer', 'jquery'], function() {
            var $ = layui.jquery;
            var table = $('#table').val();
            if(table == ''){
                layer.msg('请填写表名', {time: 1000});
                return false;
            }

            layer.confirm('确认初始化模板?' ,
                function(index, layero){
                    $.ajax({
                        url:"{{url('initTemplate')}}",
                        data:{"table":table},
                        type:'post',
                        dataType:'json',
                        success:function(res){
                            if(res.code == 200){
                                layer.msg(res.message,{icon:6});
                                $('#list_template').val(table + '/list.shtml');
                                $('#content_template').val(table + '/content.shtml');
                            }else{
                                layer.msg(res.message,{shift: 6,icon:5});
                            }
                        },
                        error : function(XMLHttpRequest, textStatus, errorThrown) {
                            layer.msg('网络失败', {time: 1000});
                        }
                    });
                }
            );
            return;
        });
    }

    //打开模板选择
    function openTemplateChoose(inputName) {
        var index = layer.open({
            type: 2,
            title: "选择模板",
            area: ["500px", "500px"],
            fixed: false, //不固定
            content: "{{url('admin/template/tree')}}",
            success: function (layer0, index) {
                var iframeWin = window[layer0.find('iframe')[0]['name']]; //得到iframe页的窗口对象
                iframeWin.setCallbackInput(inputName);
            }
        });
    }

    //保存模板
    function saveTemplate(inputName, path){
        layui.use(['form', 'layer', 'jquery'], function() {
            var $ = layui.jquery;
            $("input[name='"+inputName+"']").val(path);
        });
    }

    layui.use(['form', 'layer', 'jquery'], function() {
        var $ = layui.jquery;
        var form = layui.form;
        form.render();
        var layer = layui.layer;

        $('#type').val({$info.type ?? ''});

        form.verify({
            title: function (value){
                if(value == '' || value <1){
                    return '请输入模型名称';
                }
            },
            table: function (value){
                if(value == '' || value <1){
                    return '请输入模型的表';
                }
            },
            type:function (value){
                if(value == '' || value <1){
                    return '请选择模型类型';
                }
            }
        });
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
