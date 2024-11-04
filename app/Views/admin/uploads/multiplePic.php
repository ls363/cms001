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
    <label class="layui-form-label">图片列表：</label>
    <div class="layui-input-block">
        <input type="hidden" name="slide" id="slide" value="{$info['slide'] ?? ''}">
        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnSlide">选择多个图片
        </button>
        <button type="button" class="layui-btn layui-btn-sm  layui-btn-warm " style="display: none;" id="btnSlideAction">开始上传
        </button>
        <ul id="slideList" class="imageList">
            {if(! empty($info['slideList']))}
            {foreach $info['slideList'] as $p}
            <li><a class="img_close exist_file" data-id="{$p['id']}" href="javascript:;">x</a>
                <img src="{$p['url_pre']}" onclick="window.open('{$p['url']}')"/>
                <input type="text" name="slide_remark[]" value="{$p['remark']}">
            </li>
            {/foreach}
            {/if}
        </ul>
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

        //轮播图
        $('#slideList .exist_file').on('click', function () {
            var id = $(this).attr('data-id');
            var slide = $('#slide').val();
            if (slide != '') {
                var proofArray = slide.split(',');
                var index = proofArray.indexOf(id);
                if (index > -1) {
                    var tmp = proofArray.splice(index, 1);
                    $('#slide').val(proofArray.join(','));
                }
            }
            $(this).parent().remove();
        });

        var slideListView = $('#slideList');
        var slideUploadListIns = upload.render({
            elem: '#btnSlide'
            , url: '{{url('/admin/uploads/upload')}}'
            , data: {folder: 'slide'}
            , accept: 'file'
            , multiple: true
            , number: 3
            , auto: false
            , bindAction: '#btnSlideAction'
            , choose: function (obj) {
                $('#slideTable').show();
                $('#btnSlideAction').show();
                var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列

                //读取本地文件
                obj.preview(function (index, file, result) {
                    var tr = $(['<li id="slide-upload-' + index + '">'
                        , '<a class="img_close demo-delete" href="javascript:;">x</a>'
                        , '<img src="' + result + '" alt="' + file.name + '" >'
                        , '<div class="layui-progress progressPos" lay-filter="progress-' + index + '"><div class="layui-progress-bar" lay-percent=""></div></div>'
                        , '<div class="operate">'
                        , '<button class="layui-btn layui-btn-xs demo-reload layui-hide" style="background: #f60;">重传</button>'
                        , '</div>'
                        ,'<input type="text" name="slide_remark[]" value="">'
                        , '</li>'].join(''));

                    //单个重传
                    tr.find('.demo-reload').on('click', function () {
                        obj.upload(index, file);
                    });

                    //删除
                    tr.find('.demo-delete').on('click', function () {
                        delete files[index]; //删除对应的文件
                        tr.remove();
                        slideUploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                    });

                    slideListView.append(tr);

                    element.render('progress');
                });
            }
            , done: function (res, index, upload) {
                if (res.code == 200) { //上传成功
                    var fileList = $('#slide').val();
                    if (fileList != '') {
                        fileList += ',';
                    }
                    fileList += res.data.id;
                    $('#slide').val(fileList);
                    var tr = slideListView.find('li#slide-upload-' + index)
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
                var tr = slideListView.find('li#slide-upload-' + index)
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