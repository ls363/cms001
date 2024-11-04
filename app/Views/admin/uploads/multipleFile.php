@section('title', '文章编辑')
@section('id', $id)
@section('content')

<style type="text/css">
    .layui-form-label {
        width: 100px;
    }

    .layui-input-block {
        margin-left: 130px;
    }
</style>

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
            <div class="layui-upload-list" id="attachmentTable" {if(empty($info['attachmentList']))}style="display: none;"{/if}>
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
                    <td><a href="{$p['url']}" target="_blank">{$p['name']} [下载]</a></td>
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

@endsection
@section('js')
<script>


    layui.use(['form', 'jquery', 'laypage', 'layer', 'upload', 'element'], function () {

        var form = layui.form,
            upload = layui.upload,
            element = layui.element,
            $ = layui.jquery;
        form.render();

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
            , url: '{{url('admin/uploads/upload')}}'
            , data: {folder: 'attachment'}
            , accept: 'file'
            , multiple: true
            , number: 3
            , auto: false
            , bindAction: '#btnAttachmentAction'
            , choose: function (obj) {
                $('#attachmentTable').show();
                $('#btnAttachmentAction').show();
                var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列

                //读取本地文件
                obj.preview(function (index, file, result) {
                    var tr = $(['<tr id="attachment-upload-' + index + '">'
                        , '<td>' + file.name + '</td>'
                        , '<td>' + (file.size / 1024).toFixed(1) + 'kb</td>'
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
                if (res.code == 200) { //上传成功
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

    });


</script>
@endsection
@extends('admin.common.edit')