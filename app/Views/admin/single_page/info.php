@section('title', '单页编辑')
@section('id', $id)
@section('content')

<style type="text/css">
    .layui-form-label {
        width: 100px;
    }

    .layui-input-block {
        margin-left: 130px;
    }

    .info_tip{
        border: solid 2px #f60; color: #f00; padding: 10px; margin-left: 20px;
    }

</style>

<div class="info_tip">
    单页是一类特殊的文章，每个栏目只有一篇文章，单页添加的表单与文章一样。单页的SEO信息，在单页对应的栏目中设置，这里不需要处理。
</div>

<div class="layui-tab" lay-filter="tabDemo">

    <ul class="layui-tab-title">
        <li class="layui-this" lay-id="1">基本信息</li>
        <li lay-id="2">图片上传</li>
    </ul>

    <div class="layui-tab-content ">
        <div class="layui-tab-item layui-show">
            <input type="hidden" name="class_id" value="{$info.class_id ?? ''}">

            <div class="layui-form-item">
                <label class="layui-form-label">标题：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.title ?? ''}" name="title" required lay-verify="permission_remark"
                           placeholder="请输入2-12位字母" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">副标题：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.alias ?? ''}" name="alias" placeholder="请输入副标题"
                           autocomplete="off" class="layui-input">
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">摘要：</label>
                <div class="layui-input-block">
                    <textarea name="intro" placeholder="请输入文章摘要" class="layui-textarea">{$info.intro ?? ''}</textarea>
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">内容属性：</label>
                <div class="layui-input-block">
                    <input type="checkbox" {if(isset($info['is_top']) && $info['is_top'] == 1)}checked{/if} name="is_top" value="1" title="置顶">
                    <input type="checkbox" {if(isset($info['is_recommend']) && $info['is_recommend'] == 1)}checked{/if} name="is_recommend" value="1" title="推荐">
                    <input type="checkbox" {if(isset($info['is_slide']) && $info['is_slide'] == 1)}checked{/if} name="is_slide" value="1" title="幻灯片">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">内容：</label>
                <div class="layui-input-block">
                    <textarea id="container" name="content" placeholder="请输入2-30位汉字" class="layui-textarea" required style="min-height:300px; padding: 0;border: none;">{{htmlspecialchars($info['content'] ?? '')}}</textarea>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">作者：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.author ?? ''}" name="author" placeholder="请输入作者" autocomplete="off"
                           class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">来源：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.copyfrom ?? ''}" name="copyfrom" placeholder="请输入来源"
                           autocomplete="off" class="layui-input">
                </div>
            </div>
        </div>

        <div class="layui-tab-item">
            <div class="layui-form-item">
                <label class="layui-form-label">封面图片：</label>
                <div class="layui-input-block">
                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnChoose">选择文件
                    </button>
                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnUpload">开始上传
                    </button>
                    <p {if(! isset($info['coverPic']) || empty($info['coverPic']))} style="display: none;" {/if}><img
                            src="{$info['coverPic'] ?? ''}" onclick="window.open('{$info['coverPicBig'] ?? ''}');"
                            id="imgPreview" style="margin-top: 15px; max-height: 150px; cursor: pointer;"></p>
                    <input type="hidden" value="{$info.cover ?? 0}" name="cover" id="cover"/>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">轮播图片：</label>
                <div class="layui-input-block">
                    <input type="hidden" name="sample" id="sample" value="{$info->slide ?? ''}">
                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnSample">选择多个图片
                    </button>
                    <button type="button" class="layui-btn layui-btn-sm  layui-btn-warm " style="display: none;"
                            id="btnSampleAction">开始上传
                    </button>
                    <ul id="sampleList" class="imageList">
                        {if(! empty($info['slideList']))}
                        {foreach $info['slideList'] as $p}
                        <li><a class="img_close exist_file" data-id="{$p['id']}"
                               href="javascript:;">x</a>
                            <img src="{$p['url']}"/>
                        </li>
                        {/foreach}
                        {/if}
                    </ul>
                </div>

            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">相关附件：</label>
                <div class="layui-input-block">
                    <div class="layui-upload">
                        <input type="hidden" name="attachment" id="attachment" value="">
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnAttachment">
                            选择多个文件
                        </button>
                        <button type="button" class="layui-btn layui-btn-warm layui-btn-sm" style="display: none;"
                                id="btnAttachmentAction">开始上传
                        </button>
                        <div class="layui-upload-list" id="attachmentTalbe" {if(empty($info['attachmentList']))}style="display: none;"{/if}>
                        <table class="layui-table">
                            <thead>
                            <th>文件名</th>
                            <th>大小</th>
                            <th>上传进度</th>
                            <th>操作</th>
                            </thead>
                            <tbody id="attachmentList">
                            {if(! empty($info['attachmentList']))}
                            {foreach $info['attachmentList'] as $p}
                            <tr>
                                <td>{$p['name']}</td>
                                <td>{$p['size']}</td>
                                <td>已上传文件</td>
                                <td>
                                    <button class="layui-btn layui-btn-xs layui-btn-danger exist_attach" data-id="{$p['id']}">删除
                                    </button>
                                </td>
                            </tr>
                            {/foreach}
                            {/if}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
</div>
@endsection
@section('js')
<script type="text/javascript" charset="utf-8" src="{PUBLIC_URL}/static/admin/lib/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{PUBLIC_URL}/static/admin/lib/ueditor/ueditor.all.min.js"></script>
<!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
<!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
<script type="text/javascript" charset="utf-8" src="{PUBLIC_URL}/static/admin/lib/ueditor/lang/zh-cn/zh-cn.js"></script>
<script>
    //获取来源窗口的ID
    let sourceFrameId = '_{{url('index')}}';

    layui.use(['form', 'jquery', 'laypage', 'layer', 'upload'], function () {

        var form = layui.form,
            upload = layui.upload,
            $ = layui.jquery;
        form.render();

        //手动上传封面图片
        upload.render({
            elem: '#btnChoose'
            , url: '/admin/uploads/upload'
            , auto: false
            , data: {folder: 'banner'}
            //,multiple: true
            , bindAction: '#btnUpload'
            , choose1: function (obj) {
                var that = this;
                obj.preview(function (index, file) {
                    console.log(file.name);
                    obj.resetFile(index, file, '123.jpg');
                });
            }
            , before: function () {
                console.log(345);
            }
            , done: function (res) {
                console.log(res)
                $('#imgPreview').attr('src', res.data.src).parent().show();
                $('#cover').val(res.data.id);
            }
        });

        //轮播图
        $('#sampleList .exist_file').on('click', function () {
            var id = $(this).attr('data-id');
            var sample = $('#sample').val();
            if (sample != '') {
                var proofArray = sample.split(',');
                var index = proofArray.indexOf(id);
                if (index > -1) {
                    var tmp = proofArray.splice(index, 1);
                    $('#sample').val(proofArray.join(','));
                }
            }
            $(this).parent().remove();
        });

        //附件信息
        $('#attachmentList .exist_attach').on('click', function () {
            var id = $(this).attr('data-id');
            var proof = $('#attachment').val();
            if (proof != '') {
                var proofArray = proof.split(',');
                var index = proofArray.indexOf(id);
                if (index > -1) {
                    var tmp = proofArray.splice(index, 1);
                    $('#attachment').val(proofArray.join(','));
                }
            }
            $(this).parent().parent().remove();
        });


        var attachmentListView = $('#attachmentList');
        var attachmentUploadListIns = upload.render({
            elem: '#btnAttachment'
            , url: '/uploads/upload'
            , data: {folder: 'attachment'}
            , accept: 'file'
            , multiple: true
            , number: 3
            , auto: false
            , bindAction: '#btnAttachmentAction'
            , choose: function (obj) {
                $('#attachmentTalbe').show();
                $('#btnAttachmentAction').show();
                var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列

                //读取本地文件
                obj.preview(function (index, file, result) {
                    var tr = $(['<tr id="attachment-upload-' + index + '">'
                        , '<td>' + file.name + '</td>'
                        , '<td>' + (file.size / 1014).toFixed(1) + 'kb</td>'
                        , '<td><div class="layui-progress" lay-filter="progress-' + index + '"><div class="layui-progress-bar" lay-percent=""></div></div></td>'
                        , '<td>'
                        , '<button class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>'
                        , '<button class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>'
                        , '</td>'
                        , '</tr>'].join(''));

                    //单个重传
                    tr.find('.demo-reload').on('click', function () {
                        obj.upload(index, file);
                    });

                    //删除
                    tr.find('.demo-delete').on('click', function () {
                        delete files[index]; //删除对应的文件
                        tr.remove();
                        attachmentUploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                    });

                    attachmentListView.append(tr);

                    element.render('progress');
                });
            }
            , done: function (res, index, upload) {
                if (res.code == 0) { //上传成功
                    var fileList = $('#attachment').val();
                    if (fileList != '') {
                        fileList += ',';
                    }
                    fileList += res.data.id;
                    $('#attachment').val(fileList);
                    var tr = attachmentListView.find('tr#attachment-upload-' + index)
                        , tds = tr.children();
                    tds.eq(3).html(''); //清空操作
                    delete this.files[index]; //删除文件队列已经上传成功的文件
                    return;
                }
                this.error(index, upload);
            }
            , allDone: function (obj) {
                console.log(obj)
            }
            , error: function (index, upload) {
                var tr = attachmentListView.find('tr#attachment-upload-' + index)
                    , tds = tr.children();
                tds.eq(3).find('.demo-reload').removeClass('layui-hide'); //显示重传
            }
            , progress: function (n, elem, e, index) {
                console.log(n);
                element.progress('progress-' + index, n + '%'); //进度条
            }
        });

        var sampleListView = $('#sampleList');
        var sampleUploadListIns = upload.render({
            elem: '#btnSample'
            , url: '/uploads/upload'
            , data: {folder: 'sample'}
            , accept: 'file'
            , multiple: true
            , number: 3
            , auto: false
            , bindAction: '#btnSampleAction'
            , choose: function (obj) {
                $('#sampleTable').show();
                $('#btnSampleAction').show();
                var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列

                //读取本地文件
                obj.preview(function (index, file, result) {
                    var tr = $(['<li id="sample-upload-' + index + '">'
                        , '<a class="img_close demo-delete" href="javascript:;">x</a>'
                        , '<img src="' + result + '" alt="' + file.name + '" >'
                        , '<div class="layui-progress progressPos" lay-filter="progress-' + index + '"><div class="layui-progress-bar" lay-percent=""></div></div>'
                        , '<div class="operate">'
                        , '<button class="layui-btn layui-btn-xs demo-reload layui-hide" style="background: #f60;">重传</button>'
                        , '</div>'
                        , '</li>'].join(''));

                    //单个重传
                    tr.find('.demo-reload').on('click', function () {
                        obj.upload(index, file);
                    });

                    //删除
                    tr.find('.demo-delete').on('click', function () {
                        delete files[index]; //删除对应的文件
                        tr.remove();
                        sampleUploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                    });

                    sampleListView.append(tr);

                    element.render('progress');
                });
            }
            , done: function (res, index, upload) {
                if (res.code == 0) { //上传成功
                    var fileList = $('#sample').val();
                    if (fileList != '') {
                        fileList += ',';
                    }
                    fileList += res.data.id;
                    $('#sample').val(fileList);
                    var tr = sampleListView.find('li#sample-upload-' + index)
                        , tds = tr.children();
                    tds.eq(3).html(''); //清空操作
                    delete this.files[index]; //删除文件队列已经上传成功的文件
                    return;
                }
                this.error(index, upload);
            }
            , allDone: function (obj) {
                console.log(obj)
            }
            , error: function (index, upload) {
                var tr = sampleListView.find('li#sample-upload-' + index)
                    , tds = tr.children();
                tds.eq(3).find('.demo-reload').removeClass('layui-hide'); //显示重传
            }
            , progress: function (n, elem, e, index) {
                console.log(n);
                element.progress('progress-' + index, n + '%'); //进度条
            }
        });


        // 实例化编辑器
        var ue = UE.getEditor('container');
        var layer = layui.layer;
        form.verify({
            //    title: [/[\u4e00-\u9fa5]{2,12}$/, '标题必须2到12位汉字'],
            //    intro: [/[\u4e00-\u9fa5]{2,30}$/, '权限介绍必须2到30位汉字'],
        });
        form.on('submit(formDemo)', function (data) {

            $.ajax({
                url: "<?php echo url('save'); ?>",
                data: $('form').serialize(),
                type: 'post',
                dataType: 'json',
                success: function (res) {
                    if (res.code == 200) {
                        layer.msg(res.message, {icon: 6});
                        parent.refreshRightFrame(sourceFrameId);
                        setTimeout('parent.closeRightFrame();', 1000);
                    } else {
                        layer.msg(res.message, {shift: 6, icon: 5});
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    layer.msg('网络失败', {time: 1000});
                }
            });
            return false;
        });
    });


</script>
@endsection
@extends('admin.common.edit')