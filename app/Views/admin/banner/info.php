@section('title', 'Banner编辑')
@section('id',$id)
@section('content')
    <style type="text/css">
        .layui-form-label{width: 100px;}
        .layui-input-block{margin-left: 130px;}
        </style>

    <div class="layui-form-item">
        <label class="layui-form-label">类型：</label>
        <div class="layui-input-block">
            {foreach $typeRange as $k => $v}
                <input type="radio" name="type" value="{$k}" title="{$v}" {if(isset($info['type']) && $info['type'] == $k)}checked{/if}>
            {/foreach}
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">图片：</label>
        <div class="layui-input-block">
            <div class="layui-inline">
                <input type="text" value="{$info['cover'] ?? ''}" placeholder="请输入图片地址" required lay-verify="cover" name="cover" id="cover" class="layui-input" style="width:300px;" />
            </div>
            <div class="layui-inline">
            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnChoose">选择文件</button>
            <button type="button" class="layui-btn layui-btn-sm layui-btn-warm" id="btnUpload" style="display: none;">开始上传</button>
            </div>
            <p id="boxImgPreview" {if(! isset($info['cover']) || empty($info['cover']))}style="display: none;"{/if}><img src="{$info['cover'] ?? ''}" onclick="window.open(this.src);"  id="imgPreview" style="margin-top: 15px; max-height: 150px; cursor: pointer;"></p>
        </div>
    </div>



    <div class="layui-form-item">
        <label class="layui-form-label">状态：</label>
        <div class="layui-input-block">
            {foreach $shelfRange as $k => $v}
                <input type="radio" name="state" value="{$k}" title="{$v}" {if(isset($info['state']) && $info['state'] == $k)}checked{/if}>
            {/foreach}
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">标题：</label>
        <div class="layui-input-block">
            <input type="text"  value="{$info['title'] ?? ''}" name="title" id="title" required placeholder="" autocomplete="off" class="layui-input" >
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">描述：</label>
        <div class="layui-input-block">
            <textarea name="intro" placeholder="请输入" class="layui-textarea" required lay-verify="intro">{$info['intro'] ?? ''}</textarea>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">URL：</label>
        <div class="layui-input-block">
            <input type="text"  value="{$info['url'] ?? ''}" name="url" id="url" required placeholder="" autocomplete="off" class="layui-input" >
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">序号：</label>
        <div class="layui-input-block">
            <input type="number"  value="{$info['sort'] ?? ''}" name="sort" id="sort" required placeholder="" autocomplete="off" class="layui-input" style="width: 100px;">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">标题颜色：</label>
        <div class="layui-input-block">
            <input type="text"  value="{$info['title_color'] ?? ''}" name="title_color" id="title_color" placeholder="" autocomplete="off" class="layui-input" style="width: 100px;float: left;">
            <div id="titleColorPicker" style="display: float:left; margin-left: 10px;"></div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">描述颜色：</label>
        <div class="layui-input-block">
            <input type="text"  value="{$info['intro_color'] ?? ''}" name="intro_color" id="intro_color" placeholder="" autocomplete="off" class="layui-input" style="width: 100px;float: left;">
            <div id="introColorPicker" style="display: float:left; margin-left: 10px;"></div>
        </div>
    </div>


@endsection
@section('js')
    <script>
        layui.use(['colorpicker', 'form','jquery','laypage', 'layer', 'upload', 'element', 'laydate'], function() {
            var $ = layui.jquery
                ,upload = layui.upload
                ,element = layui.element;

            var colorpicker = layui.colorpicker;
          //渲染
          colorpicker.render({
            elem: '#titleColorPicker',  //绑定元素
            color:$('#title_color').val(),
            done: function(color){
                $('#title_color').val(color);
                console.log(color); // 选择颜色后的回调
            }
          });

          colorpicker.render({
            elem: '#introColorPicker',  //绑定元素
            color:$('#intro_color').val(),
            done: function(color){
                $('#intro_color').val(color);
                console.log(color); // 选择颜色后的回调
            }
          });

            //手动上传
            upload.render({
                elem: '#btnChoose'
                ,url: '{{url('admin/uploads/upload')}}'
                ,auto: false
                ,data:{folder:'banner'}
                //,multiple: true
                ,bindAction: '#btnUpload'
                ,choose: function(obj){
                    var that = this;
                    obj.preview(function(index, file, result){
                        console.log(file.name);
                        $('#boxImgPreview').html("<img src='"+ result+"' id='imgPreview' style='margin-top: 15px; max-height: 150px; cursor: pointer;' />").show();
                        obj.resetFile(index, file, encodeURI(file.name));
                        $('#btnUpload').show();
                    });
                }
                ,before: function(){
                    //loading效果
                    layer.msg( '上传中' , { icon: 16 , time: 1000 } );
                }
                ,done: function(res,index){
                    if ( res.code === 200 ) {
                        $('#imgPreview').attr('src', res.data.src);
                        $('#cover').val(res.data.src);
                        layer.msg( '上传成功',{icon:1, time:1000} );
                        // 删除数组中上传成功的文件，防止重复上传  重点*****
                        delete this.files[index];
                    } else {
                        layer.msg( '上传失败', {icon:2, time:2000} );
                    }

                }
            });

            $('#cover').blur(function(){
                if(this.value == ""){
                   $('#imgPreview').attr('src', this.value).parent().hide();
                    return ;
                }
                $('#imgPreview').attr('src', this.value).parent().show();
            });

            var form = layui.form;
            form.render();
            var layer = layui.layer;
            form.verify({
                url: function (value){
                    if(value == '' || value <1){
                        return '请上传图片';
                    }
                },
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
