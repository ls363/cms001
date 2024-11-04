@section('title', '编辑公司信息')
@section('id', $id)
@section('content')
<style type="text/css">
    .layui-form-label{width: 100px;}
    .layui-input-block{margin-left: 130px;}
</style>


<div class="layui-tab" lay-filter="tabDemo">

    <ul class="layui-tab-title">
        <li class="layui-this" lay-id="1">基本信息</li>
        <li lay-id="2">微信微博</li>
        <li lay-id="3">扩展信息</li>
    </ul>

    <div class="layui-tab-content ">
        <div class="layui-tab-item layui-show">

            <div class="layui-form-item">
                <label class="layui-form-label">公司名称：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.name ?? ''}" name="name" required lay-verify="company_name" placeholder="请输入公司名称" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">公司地址：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.address ?? ''}" name="address" placeholder="请输入公司地址" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">邮政编码：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.postcode ?? ''}" name="postcode" placeholder="请输入邮政编码" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">联系人：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.linkman ?? ''}" name="linkman" placeholder="请输入联系人" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">手机号码：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.mobile ?? ''}" name="mobile" placeholder="请输入手机号码" autocomplete="off" class="layui-input">

                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">电话号码：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.phone ?? ''}" name="phone" placeholder="请输入电话号码" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">传真号码：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.fax ?? ''}" name="fax" placeholder="请输入传真号码" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">电子邮箱：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.email ?? ''}" name="email" placeholder="请输入电子邮箱" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">QQ号码：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.qq ?? ''}" name="qq" placeholder="请输入QQ号码" autocomplete="off" class="layui-input">
                </div>
            </div>



            <div class="layui-form-item">
                <label class="layui-form-label">营业执照代码：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.license_code ?? ''}" name="license_code" placeholder="请输入营业执照代码" autocomplete="off" class="layui-input">
                </div>
            </div>
        </div>

        <div class="layui-tab-item">
            <div class="layui-form-item">
                <label class="layui-form-label">微信号码：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.wechat_id ?? ''}" name="wechat_id" required placeholder="请输入信息号码" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">微信图标：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                        <input type="text" placeholder="请输入微信二维码地址" class="layui-input" style="width: 300px;" value="<?php echo $info['wechat'] ?? ''; ?>" name="wechat" id="wechat"/>
                    </div>
                    <div class="layui-inline">
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnChoose">选择文件
                        </button>
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-warm" id="btnUpload" style="display: none;">开始上传
                        </button>
                    </div>
                    <p id="boxImgPreview" <?php if ((! isset($info['wechat']) || empty($info['wechat']))){ ?> style="display: none;" <?php } ?>><img
                                src="<?php echo $info['wechat'] ?? ''; ?>" onclick="window.open('<?php echo $info['wechat'] ?? ''; ?>');"
                                id="imgPreview" style="margin-top: 15px; max-height: 150px; cursor: pointer;"></p>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">微博地址：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.blog_url ?? ''}" name="blog_url" required placeholder="请输入微博地址" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">微博图标：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                        <input type="text" placeholder="请输入微博二维码地址" class="layui-input" style="width: 300px;" value="<?php echo $info['blog'] ?? ''; ?>" name="blog" id="blog"/>
                    </div>
                    <div class="layui-inline">
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnBlogChoose">选择文件
                        </button>
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-warm" id="btnBlogUpload" style="display: none;">开始上传
                        </button>
                    </div>
                    <p id="boxBlogImgPreview" <?php if ((! isset($info['blog']) || empty($info['blog']))){ ?> style="display: none;" <?php } ?>><img
                                src="<?php echo $info['blog'] ?? ''; ?>" onclick="window.open('<?php echo $info['blog'] ?? ''; ?>');"
                                id="blogImgPreview" style="margin-top: 15px; max-height: 150px; cursor: pointer;"></p>
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
    layui.use(['form','jquery','laypage', 'layer', 'upload'], function() {
        var form = layui.form,
            upload = layui.upload,
            $ = layui.jquery;

        //手动上传封面图片
        upload.render({
            elem: '#btnChoose'
            , url: '{{url('admin/uploads/upload')}}'
            , auto: false
            , data: {folder: 'banner'}
            //,multiple: true
            , bindAction: '#btnUpload'
            , choose: function (obj) {
                var that = this;
                obj.preview(function (index, file, result) {
                   $('#boxImgPreview').html("<img src='"+ result+"' style='max-width:150px;max-height:150px;' />").show();
                    obj.resetFile(index, file, encodeURI(file.name));
                    $('#btnUpload').show();
                });
            }
            , before: function () {
                layer.msg( '上传中' , { icon: 16 , time: 1000 } );
            }
            , done: function (res) {
                $('#imgPreview').attr('src', res.data.src).parent().show();
                $('#wechat').val(res.data.src);
                layer.msg( '上传成功',{icon:1, time:1000} );
            }
        });

        $('#wechat').blur(function(){
            if(this.value == ""){
               $('#imgPreview').attr('src', this.value).parent().hide();
                return ;
            }
            $('#imgPreview').attr('src', this.value).parent().show();
        });

         //手动上传封面图片
        upload.render({
            elem: '#btnBlogChoose'
            , url: '{{url('admin/uploads/upload')}}'
            , auto: false
            , data: {folder: 'banner'}
            //,multiple: true
            , bindAction: '#btnBlogUpload'
            , choose: function (obj) {
                var that = this;
                obj.preview(function (index, file, result) {
                   $('#boxBlogImgPreview').html("<img src='"+ result+"' style='max-width:150px;max-height:150px;' />").show();
                    obj.resetFile(index, file, encodeURI(file.name));
                    $('#btnBlogUpload').show();
                });
            }
            , before: function () {
                layer.msg( '上传中' , { icon: 16 , time: 1000 } );
            }
            , done: function (res) {
                $('#blogImgPreview').attr('src', res.data.src).parent().show();
                $('#blog').val(res.data.src);
                layer.msg( '上传成功',{icon:1, time:1000} );
            }
        });

        $('#blog').blur(function(){
            if(this.value == ""){
               $('#blogImgPreview').attr('src', this.value).parent().hide();
                return ;
            }
            $('#blogImgPreview').attr('src', this.value).parent().show();
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