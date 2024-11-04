@section('title', '编辑系统信息')
@section('id', $id)
@section('content')
<style type="text/css">
    .layui-form-label{width: 100px;}
    .layui-input-block{margin-left: 130px;}
</style>


<div class="layui-tab" lay-filter="tabDemo">

    <ul class="layui-tab-title">
        <li class="layui-this" lay-id="1">基本信息</li>
        <li lay-id="2">网站模板</li>
        <li lay-id="3">首页SEO信息</li>
        <li lay-id="4">扩展信息</li>
    </ul>

    <div class="layui-tab-content ">
        <div class="layui-tab-item layui-show">
            <div class="layui-form-item">
                <label class="layui-form-label">网站名称：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.site_name ?? ''}" name="site_name" required lay-verify="site_name" placeholder="请输入站点名称" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">网站域名：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                        <input type="text" style="width: 300px;" value="{$info.site_domain ?? ''}" name="site_domain" placeholder="请输入网站域名" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline" style="color: #f60;">
                        这里必须是完整域名
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">首页名称：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                        <input type="text" style="width: 300px;" value="{$info.site_home ?? ''}" name="site_home" placeholder="请输入首页名称" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline" style="color: #f60;">
                        你的位置：首页，首页的名称可以在这里改
                    </div>
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">网站LOGO：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                    <input  class="layui-input" type="text" value="<?php echo $info['site_logo'] ?? ''; ?>" name="site_logo" id="site_logo" style="width: 300px;"/>
                    </div>
                    <div class="layui-inline">
                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnChooseLogo">选择文件
                    </button>
                    <button type="button" class="layui-btn layui-btn-sm layui-btn-warm" id="btnUploadLogo" style="display: none;">开始上传
                    </button>
                    </div>
                    <p id="boxLogoImgPreview" <?php if ((! isset($info['site_logo']) || empty($info['site_logo']))){ ?> style="display: none;" <?php } ?>><img
                                src="<?php echo $info['site_logo'] ?? ''; ?>" onclick="window.open('<?php echo $info['site_logo'] ?? ''; ?>');"
                                id="imgLogoPreview" style="margin-top: 15px; max-height: 150px; cursor: pointer;"></p>

                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">网站ICP：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.icp ?? ''}" name="icp" placeholder="请输入联系人" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">版权信息：</label>
                <div class="layui-input-block">
                    <textarea name="copyrights" placeholder="请输入版权信息" class="layui-textarea" >{$info['copyrights'] ?? ''}</textarea>
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">统计代码：</label>
                <div class="layui-input-block">
                    <textarea name="statistical_code" placeholder="请输入统计代码" class="layui-textarea" >{$info['statistical_code'] ?? ''}</textarea>
                </div>
            </div>

        </div>

        <div class="layui-tab-item">
            <div class="layui-form-item">
                <label class="layui-form-label">网站模板：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                        <select name="skin" id="skin" lay-verify="required">
                            <option value="0" selected>请选择</option>
                            {foreach $skinList as $v}
                            <option value="{$v}" {if(isset($info['skin']) && $info['skin'] == $v)}selected{/if}>{$v}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="layui-inline" style="color: #666;">
                        templates下面的文件夹，如：default
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">首页模板：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                        <input style="width: 300px;" readonly type="text" value="{$info['index_template'] ?? ''}" name="index_template"  placeholder="请选择首页模板" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <input type="button" value="选择" class="layui-btn layui-btn-sm layui-btn-normal" onclick="openTemplateChoose('index_template')" >
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">顶部BANNER：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                        <input type="text" class="layui-input" style="width: 300px;" value="<?php echo $info['cover'] ?? ''; ?>" name="cover" id="cover"/>
                    </div>
                    <div class="layui-inline">
                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnBannerChoose">选择文件
                    </button>
                    <button type="button" class="layui-btn layui-btn-sm layui-btn-warm" id="btnBannerUpload" style="display: none;">开始上传
                    </button>
                    </div>
                    <p id="boxImgBannerPreview" <?php if ((! isset($info['cover']) || empty($info['cover']))){ ?> style="display: none;" <?php } ?>><img
                                src="<?php echo $info['cover'] ?? ''; ?>" onclick="window.open(this.src);"
                                id="imgBannerPreview" style="margin-top: 15px; max-height: 150px; cursor: pointer;"></p>
                    
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">扩展图片1：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                        <input type="text" class="layui-input" style="width: 300px;" value="<?php echo $info['pic_1'] ?? ''; ?>" name="pic_1" id="pic_1"/>
                    </div>
                    <div class="layui-inline">
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnPic1Choose">选择文件
                        </button>
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-warm" id="btnPic1Upload" style="display: none;">开始上传
                        </button>
                    </div>
                    <p id="boxImgPic1Preview" <?php if ((! isset($info['pic_1']) || empty($info['pic_1']))){ ?> style="display: none;" <?php } ?>><img
                                src="<?php echo $info['pic_1'] ?? ''; ?>" onclick="window.open(this.src);"
                                id="imgPic1Preview" style="margin-top: 15px; max-height: 150px; cursor: pointer;"></p>

                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">扩展图片2：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                        <input type="text" class="layui-input" style="width: 300px;" value="<?php echo $info['pic_2'] ?? ''; ?>" name="pic_2" id="pic_2"/>
                    </div>
                    <div class="layui-inline">
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnPic2Choose">选择文件
                        </button>
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-warm" id="btnPic2Upload" style="display: none;">开始上传
                        </button>
                    </div>
                    <p id="boxImgPic2Preview" <?php if ((! isset($info['pic_2']) || empty($info['pic_2']))){ ?> style="display: none;" <?php } ?>><img
                                src="<?php echo $info['pic_2'] ?? ''; ?>" onclick="window.open(this.src);"
                                id="imgPic2Preview" style="margin-top: 15px; max-height: 150px; cursor: pointer;"></p>

                </div>
            </div>

        </div>

        <div class="layui-tab-item">
            <div class="layui-form-item">
                <label class="layui-form-label">SEO标题：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.seo_title ?? ''}" name="seo_title" placeholder="请输入SEO标题" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">SEO关键字：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.seo_keywords ?? ''}" name="seo_keywords"  placeholder="请输入SEO关键字" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">SEO描述：</label>
                <div class="layui-input-block">
                    <textarea name="seo_description" placeholder="请输入SEO描述描述" class="layui-textarea" >{$info['seo_description'] ?? ''}</textarea>
                </div>
            </div>

        </div>

        <div class="layui-tab-item">

            <div class="layui-form-item">
                <label class="layui-form-label">扩展信息1：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.extra_1 ?? ''}" name="extra_1" required placeholder="请输入扩展信息1" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">扩展信息2：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.extra_2 ?? ''}" name="extra_2" required placeholder="请输入扩展信息2" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">扩展信息3：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.extra_3 ?? ''}" name="extra_3" required placeholder="请输入扩展信息3" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">扩展信息4：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.extra_4 ?? ''}" name="extra_4" required placeholder="请输入扩展信息4" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">扩展信息5：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.extra_5 ?? ''}" name="extra_5" required placeholder="请输入扩展信息5" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">扩展信息6：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.extra_6 ?? ''}" name="extra_6" required placeholder="请输入扩展信息6" autocomplete="off" class="layui-input">
                </div>
            </div>



        </div>

    </div>
</div>


@endsection


@section('js')
<script>

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

    layui.use(['form','jquery','laypage', 'layer', 'upload'], function() {
        var form = layui.form,
            upload = layui.upload,
            $ = layui.jquery;

        //手动上传封面图片
        upload.render({
            elem: '#btnChooseLogo'
            , url: '{{url('admin/uploads/upload')}}'
            , auto: false
            , data: {folder: 'banner'}
            //,multiple: true
            , bindAction: '#btnUploadLogo'
            , choose: function (obj) {
                var that = this;
                obj.preview(function (index, file, result) {
                    $('#boxLogoImgPreview').html("<img src='"+ result+"' style='max-height:150px;' />").show();
                    obj.resetFile(index, file, encodeURI(file.name));
                    $('#btnUploadLogo').show();
                });
            }
            , before: function () {
                layer.msg( '上传中' , { icon: 16 , time: 1000 } );
            }
            , done: function (res, index) {
                $('#imgLogoPreview').attr('src', res.data.src).parent().show();
                $('#site_logo').val(res.data.src);
                layer.msg( '上传成功',{icon:1, time:1000} );
                 // 删除数组中上传成功的文件，防止重复上传  重点*****
                delete this.files[index];
            }
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
                obj.preview(function (index, file, result) {
                    $('#boxImgBannerPreview').html("<img src='"+ result+"' style='max-height:150px;' />").show();
                    obj.resetFile(index, file, encodeURI(file.name));
                    $('#btnBannerUpload').show();
                });
            }
        , before: function () {
               layer.msg( '上传中' , { icon: 16 , time: 1000 } );

          }
        , done: function (res,index) {
                $('#imgBannerPreview').attr('src', res.data.src).parent().show();
                $('#cover').val(res.data.src);
                layer.msg( '上传成功',{icon:1, time:1000} );
                 // 删除数组中上传成功的文件，防止重复上传  重点*****
                delete this.files[index];
            }
        });

        $('#cover').blur(function(){
            if(this.value == ""){
               $('#imgBannerPreview').attr('src', this.value).parent().hide();
                return ;
            }
            $('#imgBannerPreview').attr('src', this.value).parent().show();
        });

        $('#site_logo').blur(function(){
            if(this.value == ""){
               $('#imgLogoPreview').attr('src', this.value).parent().hide();
                return ;
            }
            $('#imgLogoPreview').attr('src', this.value).parent().show();
        });

        //手动上传封面图片
        upload.render({
        elem: '#btnPic1Choose'
        , url: '{{url('admin/uploads/upload')}}'
        , auto: false
        , data: {folder: 'Pic1'}
            //,multiple: true
        , bindAction: '#btnPic1Upload'
        , choose: function (obj) {
                var that = this;
                obj.preview(function (index, file, result) {
                    $('#boxImgPic1Preview').html("<img src='"+ result+"' style='max-height:150px;' />").show();
                    obj.resetFile(index, file, encodeURI(file.name));
                    $('#btnPic1Upload').show();
                });
            }
        , before: function () {
               layer.msg( '上传中' , { icon: 16 , time: 1000 } );

          }
        , done: function (res,index) {
                $('#imgPic1Preview').attr('src', res.data.src).parent().show();
                $('#pic_1').val(res.data.src);
                layer.msg( '上传成功',{icon:1, time:1000} );
                 // 删除数组中上传成功的文件，防止重复上传  重点*****
                delete this.files[index];
            }
        });


        $('#pic_1').blur(function(){
            if(this.value == ""){
               $('#imgPicPreview').attr('src', this.value).parent().hide();
                return ;
            }
            $('#imgPic1Preview').attr('src', this.value).parent().show();
        });

        //手动上传封面图片
        upload.render({
        elem: '#btnPic2Choose'
        , url: '{{url('admin/uploads/upload')}}'
        , auto: false
        , data: {folder: 'Pic2'}
            //,multiple: true
        , bindAction: '#btnPic2Upload'
        , choose: function (obj) {
                var that = this;
                obj.preview(function (index, file, result) {
                    $('#boxImgPic2Preview').html("<img src='"+ result+"' style='max-height:150px;' />").show();
                    obj.resetFile(index, file, encodeURI(file.name));
                    $('#btnPic2Upload').show();
                });
            }
        , before: function () {
               layer.msg( '上传中' , { icon: 16 , time: 1000 } );

          }
        , done: function (res,index) {
                $('#imgPic2Preview').attr('src', res.data.src).parent().show();
                $('#pic_2').val(res.data.src);
                layer.msg( '上传成功',{icon:1, time:1000} );
                 // 删除数组中上传成功的文件，防止重复上传  重点*****
                delete this.files[index];
            }
        });


        $('#pic_2').blur(function(){
            if(this.value == ""){
               $('#imgPicPreview').attr('src', this.value).parent().hide();
                return ;
            }
            $('#imgPic2Preview').attr('src', this.value).parent().show();
        });

        form.render();
        var layer = layui.layer;
        form.verify({
            //    title: [/[\u4e00-\u9fa5]{2,12}$/, '标题必须2到12位汉字'],
            //    intro: [/[\u4e00-\u9fa5]{2,30}$/, '权限介绍必须2到30位汉字'],
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
                        layer.msg(res.msg,{shift: 6,icon:5});
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