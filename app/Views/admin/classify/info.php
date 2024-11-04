@section('title', '编辑栏目')
@section('id', $id)
@section('content')
<style type="text/css">
    .layui-form-label{width: 100px;}
    .layui-input-block{margin-left: 130px;}
</style>

<div class="layui-tab" lay-filter="tabDemo">

    <ul class="layui-tab-title">
        <li class="layui-this" lay-id="1">基本信息</li>
        <li lay-id="2">简介图片</li>
        <li lay-id="3">SEO及HTML更新</li>
        <li lay-id="4">扩展字段</li>
    </ul>

    <div class="layui-tab-content ">
        <div class="layui-tab-item layui-show">

            <div class="layui-form-item">
                <label class="layui-form-label">上级栏目：</label>
                <div class="layui-input-block">
                    <select name="parent_id" id="parent_id" lay-verify="required">
                        {$parentOption}
                    </select>
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">栏目名称：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.title ?? ''}" name="title" required lay-verify="title" placeholder="请输入栏目名称" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">序号：</label>
                <div class="layui-input-block">
                    <input type="number" value="{$info['sort'] ?? ''}" name="sort" required  placeholder="请输入数字" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">状态：</label>
                <div class="layui-input-block">
                    <input type="radio" name="state" value="1" title="上架" {if ($info['state'] == 1)} checked{/if}>
                    <input type="radio" name="state" value="0" title="下架" {if ($info['state'] == 0)} checked{/if}>

                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">栏目URL：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.url ?? ''}" name="url" required placeholder="请输入栏目url, 如: news" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">内容模型：</label>
                <div class="layui-input-block">
                    <select id="model_id" name="model_id" lay-filter="model_id">
                        <option value="0">请选择内容模型</option>
                        {foreach $modelList as $k=>$v}
                        <option value="{$k}">{$v}</option>
                        {/foreach}
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">列表页模板：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                        <input style="width: 300px;" readonly type="text" value="{$info['list_template'] ?? ''}" name="list_template"  placeholder="请选择列表页模板" autocomplete="off" class="layui-input">
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
                        <input type="text" readonly style="width: 300px;" value="{$info['content_template'] ?? ''}" name="content_template" placeholder="请选择内容页模板" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <input type="button" value="选择" class="layui-btn layui-btn-sm layui-btn-normal" onclick="openTemplateChoose('content_template')" >
                    </div>
                </div>
            </div>
        </div>

        <div class="layui-tab-item">
            <div class="layui-form-item">
                <label class="layui-form-label">副标题：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.alias ?? ''}" name="alias"  placeholder="请输入副标题" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">栏目简介：</label>
                <div class="layui-input-block">
                    <textarea name="intro" placeholder="请输入栏目简介" class="layui-textarea">{$info['intro'] ?? ''}</textarea>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">栏目顶部图片：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                    <input type="text" value="{$info.banner ?? ''}" style="width: 300px;" name="banner" id="banner"  placeholder="请输入栏目顶部图片" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnBannerChoose">选择图片
                        </button>
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-warm " style="display: none;" id="btnBannerUpload">开始上传
                        </button>
                    </div>
                    <p id="boxBannerImgPreview" <?php if ((! isset($info['banner']) || empty($info['banner']))){ ?> style="display: none;" <?php } ?>><img
                                src="{$info.banner}" onclick="window.open(this.src);"
                                id="bannerImgPreview" style="margin-top: 15px; max-height: 150px; cursor: pointer;"></p>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">模块封面图片：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                        <input type="text" value="{$info.cover ?? ''}" style="width: 300px;" name="cover" id="cover"  placeholder="请输入模块封面图片" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnCoverChoose">选择图片
                        </button>
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-warm " style="display: none;" id="btnCoverUpload">开始上传
                        </button>
                    </div>
                    <p id="boxCoverImgPreview" <?php if ((! isset($info['cover']) || empty($info['cover']))){ ?> style="display: none;" <?php } ?>><img
                                src="{$info.cover}" onclick="window.open(this.src);"
                                id="coverImgPreview" style="margin-top: 15px; max-height: 150px; cursor: pointer;"></p>
                </div>
            </div>
        </div>

        <div class="layui-tab-item">

            <div class="layui-form-item">
                <label class="layui-form-label">HTML生成：</label>
                <div class="layui-input-block">
                    <input type="radio" name="make_html" value="1" title="列表+内容" {if ($info['state'] == 1)} checked{/if}>
                    <input type="radio" name="make_html" value="2" title="仅列表" {if ($info['state'] == 2)} checked{/if}>
                    <input type="radio" name="make_html" value="3" title="仅内容" {if ($info['state'] == 3)} checked{/if}>

                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">SEO标题：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.seo_title ?? ''}" name="seo_title" placeholder="请输入SEO标题"
                           autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">SEO关键字：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.seo_keywords ?? ''}" name="seo_keywords" placeholder="请输入SEO关键字"
                           autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">SEO描述：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.seo_description ?? ''}" name="seo_description"
                           placeholder="请输入SEO描述" autocomplete="off" class="layui-input">
                </div>
            </div>
        </div>

        <div class="layui-tab-item">
            <div class="layui-form-item">
                <label class="layui-form-label">扩展字段1：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                    <input style="width: 360px;" type="text" value="{$info.extra_1 ?? ''}" name="extra_1"
                           placeholder="请输入扩展字段1" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline" style="color: #f60;">
                        标签 <?php echo "{\$extra_1}";?>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">扩展字段2：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                    <input style="width: 360px;" type="text" value="{$info.extra_2 ?? ''}" name="extra_2"
                           placeholder="请输入扩展字段2" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline" style="color: #f60;">
                        标签 <?php echo "{\$extra_2}";?>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">扩展字段3：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                    <input style="width: 360px;" type="text" value="{$info.extra_3 ?? ''}" name="extra_3"
                           placeholder="请输入扩展字段3" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline" style="color: #f60;">
                        标签 <?php echo "{\$extra_3}";?>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">扩展字段4：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                    <input style="width: 360px;" type="text" value="{$info.extra_4 ?? ''}" name="extra_4"
                           placeholder="请输入扩展字段4" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline" style="color: #f60;">
                        标签 <?php echo "{\$extra_4}";?>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">扩展字段5：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                    <input style="width: 360px;" type="text" value="{$info.extra_5 ?? ''}" name="extra_5"
                           placeholder="请输入扩展字段5" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline" style="color: #f60;">
                        标签 <?php echo "{\$extra_2}";?>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">扩展字段6：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                    <input style="width: 360px;" type="text" value="{$info.extra_6 ?? ''}" name="extra_6"
                           placeholder="请输入扩展字段6" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline" style="color: #f60;">
                        标签 <?php echo "{\$extra_6}";?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js')
<script>

    var modelJson = {$modelJson};

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

    //打开图片上传
    function openUploadPic() {
        var index = layer.open({
            type: 2,
            title: "选择图片",
            area: ["500px", "500px"],
            fixed: false, //不固定
            content: "{{url('admin/uploads/onePic')}}",
            success: function (layer0, index) {
                //var iframeWin = [windowlayer0.find('iframe')[0]['name']]; //得到iframe页的窗口对象
                //iframeWin.setCallbackInput(inputName);
            }
        });
    }

    function saveUploadPic(path){
        layui.use(['jquery'], function() {
            var $ = layui.jquery;
            $("#cover").val(path);
            $('#coverImgPreview').attr('src', path).parent().show();
        });
    }

    layui.use(['form','jquery','laypage', 'layer', 'upload'], function() {
        var form = layui.form,
            upload = layui.upload,
            $ = layui.jquery,
            layer = layui.layer;
        $('#model_id').val({$info['model_id'] ?? 0});
        $('#parent_id').val({$info['parent_id'] ?? 0});

        //内容模型改变触发的事件
        form.on('select(model_id)', function (data) {
            var model_id = data.value;
            var model = modelJson[model_id];
            $("input[name='list_template']").val(model.list_template);
            $("input[name='content_template']").val(model.content_template);
        });

        //手动上传封面图片
        upload.render({
            elem: '#btnCoverChoose'
            , url: '{{url('admin/uploads/upload')}}'
            , auto: false
            , data: {folder: 'banner'}
            //,multiple: true
            , bindAction: '#btnCoverUpload'
            , choose: function (obj) {
                var that = this;
                obj.preview(function (index, file,result) {
                    $('#boxCoverImgPreview').html("<img src='"+ result+"' style='max-width:150px;max-height:150px;' />").show();
                    obj.resetFile(index, file, encodeURI(file.name));
                    $('#btnUpload').show();
                });
            }
            , before: function () {
                layer.msg( '上传中' , { icon: 16 , time: 1000 } );
            }
            , done: function (res) {
                $('#coverImgPreview').attr('src', res.data.src).parent().show();
                $('#cover').val(res.data.src);
                 layer.msg( '上传成功',{icon:1, time:1000} );
            }
        });

        $('#cover').blur(function(){
            if(this.value == ""){
               $('#coverImgPreview').attr('src', this.value).parent().hide();
                return ;
            }
            $('#coverImgPreview').attr('src', this.value).parent().show();
        });

         //手动上传封面图片
        upload.render({
            elem: '#btnBannerChoose'
            , url: '{{url('admin/uploads/upload')}}'
            , auto: false
            , data: {folder: 'banner'}
            //,multiple: true
            , bindAction: '#btnBannerUpload'
            , choose: function (obj) {
                var that = this;
                obj.preview(function (index, file,result) {
                    $('#boxBannerImgPreview').html("<img src='"+ result+"' style='max-width:150px;max-height:150px;' />").show();
                    obj.resetFile(index, file, encodeURI(file.name));
                    $('#btnBannerUpload').show();
                });
            }
            , before: function () {
                layer.msg( '上传中' , { icon: 16 , time: 1000 } );
            }
            , done: function (res) {
                $('#bannerImgPreview').attr('src', res.data.src).parent().show();
                $('#banner').val(res.data.src);
                 layer.msg( '上传成功',{icon:1, time:1000} );
            }
        });

        $('#banner').blur(function(){
            if(this.value == ""){
               $('#bannerImgPreview').attr('src', this.value).parent().hide();
                return ;
            }
            $('#bannerImgPreview').attr('src', this.value).parent().show();
        });

        form.render();
        var layer = layui.layer;
        form.verify({
            file_id: function (value){
                if(value == ''){
                    return '请输入栏目名称';
                }
            }
        });
        form.on('submit(formDemo)', function(data) {
            $.ajax({
                url:"{{ url('save') }}",
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