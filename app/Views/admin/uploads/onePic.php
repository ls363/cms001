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
    <label class="layui-form-label">单个图片：</label>
    <div class="layui-input-block">
        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnChoose">选择文件
        </button>
        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnUpload">开始上传
        </button>
        <p id="boxPreview" style="width: 200px; height: 200px; border:solid 2px red;"></p>
        <p <img src="" onclick="window.open(this.src);" id="imgPreview" width="200" height="200" style=" display: none; width: 200px; height: 200px; border:solid 2px red;margin-top: 15px; max-height: 150px; cursor: pointer;"></p>
    </div>
</div>

@endsection
@section('js')
<script>

    function closePopup(){
        var index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
    }


    layui.use(['form', 'jquery', 'laypage', 'layer', 'upload', 'element'], function () {

        var form = layui.form,
            upload = layui.upload,
            element = layui.element,
            $ = layui.jquery;
        form.render();

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
                    console.log(result);
                    //$('#imgPreview').attr('src', result);
                    $('#boxPreview').html("<img src='"+ result+"' width='200' height='200' />");
                    console.log(file.name);
                    //obj.resetFile(index, file, encodeURI(file.name));
                });
            }
            , before: function (obj) {
                /*
                obj.preview(function (index, file, result) {
                    $('#imgPreview').attr('src', result).parent().show();
                });
                */
                layer.msg('上传中', {icon: 16, time: 0});
                console.log(345);
            }
            , done: function (res) {
                console.log(res)
                $('#imgPreview').attr('src', res.data.src).parent().show();
                $('#cover').val(res.data.id);
                parent.saveUploadPic(res.data.src);
                closePopup();
            }
        });

    });


</script>
@endsection
@extends('admin.common.edit')