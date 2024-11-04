@section('title', '招聘编辑')
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


<div class="layui-tab" lay-filter="tabDemo">

    <ul class="layui-tab-title">
        <li class="layui-this" lay-id="1">基本信息</li>
        <li lay-id="2">图片上传</li>
        <li lay-id="3">SEO信息</li>
        <li lay-id="4">扩展信息</li>
    </ul>

    <div class="layui-tab-content ">
        <div class="layui-tab-item layui-show">
            <div class="layui-form-item">
                <label class="layui-form-label layui-required">所属栏目：</label>
                <div class="layui-input-block">
                    <select id="class_id" name="class_id" lay-verify="class_id">
                        {$classList}
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label layui-required">标题：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.title ?? ''}" name="title" required lay-verify="title" placeholder="请输入标题" autocomplete="off" class="layui-input">
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
                    <textarea name="intro" placeholder="请输入摘要" class="layui-textarea">{$info.intro ?? ''}</textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">标签：</label>
                <div class="layui-input-block">
                    <input type="text" value="{$info.tags ?? ''}" name="tags" placeholder="请输入标签,多个标签用｜隔开" autocomplete="off" class="layui-input">
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">内容属性：</label>
                <div class="layui-input-block">
                    <input type="checkbox" {if(isset($info['state']) && $info['state'] == 1)}checked{/if} name="state" value="1" title="审核">
                    <input type="checkbox" {if(isset($info['is_top']) && $info['is_top'] == 1)}checked{/if} name="is_top" value="1" title="置顶">
                    <input type="checkbox" {if(isset($info['is_recommend']) && $info['is_recommend'] == 1)}checked{/if} name="is_recommend" value="1" title="推荐">
                    <input type="checkbox" {if(isset($info['is_slide']) && $info['is_slide'] == 1)}checked{/if} name="is_slide" value="1" title="幻灯片">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label layui-required">内容：</label>
                <div class="layui-input-block">
                    <textarea id="container" name="content" placeholder="请输入内容" class="layui-textarea" required lay-verify="content" style="min-height:300px; padding: 0;border: none;">{$info['content'] ? htmlspecialchars($info['content']) : ''}</textarea>
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
                    <div class="layui-inline">
                        <input type="text" value="{$info['cover'] ?? ''}" placeholder="请输入封面图片地址" name="cover" id="cover" class="layui-input" style="width:300px;" />
                    </div>
                    <div class="layui-inline">
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnChoose">选择图片
                        </button>
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-warm " style="display: none;" id="btnUpload">开始上传
                        </button>
                    </div>
                    <p id="boxImgPreview" {if(! isset($info['cover']) || empty($info['cover']))} style="display: none;" {/if}><img src="{$info['cover'] ?? ''}" onclick="window.open('{$info['coverPicBig'] ?? ''}');" id="imgPreview" style="margin-top: 15px; max-height: 150px; cursor: pointer;"></p>

                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">轮播图片：</label>
                <div class="layui-input-block">
                    <div class="layui-inline">
                        <input type="text" placeholder="请输入轮播图片地址，多个以｜隔开" name="slide" id="slide" value="{$info['slide'] ?? ''}" class="layui-input" style="width: 300px;" >
                    </div>
                    <div class="layui-inline">
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnSlide">选择多个图片
                        </button>
                        <button type="button" class="layui-btn layui-btn-sm  layui-btn-warm " style="display: none;" id="btnSlideAction">开始上传
                        </button>
                    </div>
                    <ul id="slideList" class="imageList">
                        {if(! empty($info['slideList']))}
                        {foreach $info['slideList'] as $p}
                        <li><a class="img_close exist_file" data-url="{$p['url']}" href="javascript:;">x</a>
                            <img src="{$p['url']}" onclick="window.open('{$p['url']}')"/>
                            <input type="text" name="slide_remark[]" value="{$p['remark']}">
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
                        <div class="layui-inline">
                            <input type="hidden" name="attachment" id="attachment" value="" class="layui-input" style="width: 300px;">
                        </div>
                        <div class="layui-inline">
                            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnAttachment">
                                选择多个文件
                            </button>

                            <button type="button" class="layui-btn layui-btn-warm layui-btn-sm" style="display: none;"
                                    id="btnAttachmentAction">开始上传
                            </button>
                        </div>
                        <div class="layui-inline" style="color: #FF6600">附件目前只支持上传, 这样可以显示名称及大小</div>

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

        </div>
    </div>

    <div class="layui-tab-item">

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
    <!--扩展字段-->
    
        <div class="layui-tab-item">
        
        <div class="layui-form-item">
            <label class="layui-form-label">招聘部门</label>
            <div class="layui-input-block">
                                                
                                <input type="text" value="{$info['dept_name'] ?? ''}" name="dept_name"
                       placeholder="请输入招聘部门" autocomplete="off" class="layui-input">
                
                            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">工作地点</label>
            <div class="layui-input-block">
                                                
                                <input type="text" value="{$info['work_place'] ?? ''}" name="work_place"
                       placeholder="请输入工作地点" autocomplete="off" class="layui-input">
                
                            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">招聘人数</label>
            <div class="layui-input-block">
                                                
                                <input type="text" value="{$info['job_num'] ?? ''}" name="job_num"
                       placeholder="请输入招聘人数" autocomplete="off" class="layui-input">
                
                            </div>
        </div>
            </div>
    
</div>
</div>
@endsection
@section('js')
<script type="text/javascript" charset="utf-8" src="{PUBLIC_URL}/static/admin/lib/ueditor/ueditor.config.js?stime={{time()}}"></script>
<script type="text/javascript" charset="utf-8" src="{PUBLIC_URL}/static/admin/lib/ueditor/ueditor.all.min.js?stime={{time()}}"></script>
<!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
<!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
<script type="text/javascript" charset="utf-8" src="{PUBLIC_URL}/static/admin/lib/ueditor/lang/zh-cn/zh-cn.js?stime={{time()}}"></script>
<script>
    // 实例化编辑器
    var ue = UE.getEditor('container');
    //获取来源窗口的ID
    let sourceFrameId = '{{url('index')}}';

    layui.use(['form', 'jquery', 'laypage', 'layer', 'upload', 'element'], function () {

        var form = layui.form,
            upload = layui.upload,
            element = layui.element,
            $ = layui.jquery;
            $('#class_id').val("{$info.class_id}");
        form.render();

        $('#cover').blur(function(){
            if(this.value == ""){
               $('#imgPreview').attr('src', this.value).parent().hide();
                return ;
            }
            $('#imgPreview').attr('src', this.value).parent().show();
        });

        $('#slide').blur(function(){
            let slideStr = this.value;
            if(slideStr == ""){
                $('#slideList').html("").hide();
                return;
            }
            //替换中文逗号
            slideStr = slideStr.replace(/，/g,',');
            let picList = slideStr.split(',');
            let str = '';
            const itemList = {};
            $('#slideList li').each(function(index, dom){
                let img = $(dom).find("img").attr('src');
                itemList[img] = dom.outerHTML;
            });
            for(const pic of picList){
                if(itemList.hasOwnProperty(pic)){
                   str += itemList[pic];
                }else{
                    str += '<li><img src="'+ pic +'" onclick="window.open(this.src)"/><input type="text" placeholder="图片备注" name="slide_remark[]" value=""></li>';
                }
            }
            $('#slideList').html(str).show();
        });


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
                obj.preview(function (index, file,result) {
                    $('#boxImgPreview').html("<img src='"+ result+"' style='max-width:150px;max-height:150px;' />").show();
                    obj.resetFile(index, file, encodeURI(file.name));
                    $('#btnUpload').show();
                });
            }
            , before: function () {
                layer.msg( '上传中' , { icon: 16 , time: 1000 } );
            }
            , done: function (res,index) {
                $('#imgPreview').attr('src', res.data.src).parent().show();
                $('#cover').val(res.data.src);
                layer.msg( '上传成功',{icon:1, time:1000} );
                 // 删除数组中上传成功的文件，防止重复上传  重点*****
                delete this.files[index];
            }
        });

        //轮播图
        $('#slideList .exist_file').on('click', function () {
            var url = $(this).attr('data-url');
            var slide = $('#slide').val();
            console.log(slide, url);
            if (slide != '') {
                var slideArray = slide.split(',');
                var index = slideArray.indexOf(url);
                if (index > -1) {
                    var tmp = slideArray.splice(index, 1);
                    $('#slide').val(tmp.join(','));
                }
            }
            $(this).parent().remove();
        });

        //附件信息
        $('#attachmentList .exist_attach').on('click', function () {
            var id = $(this).attr('data-id');
            var attachment = $('#attachment').val();
            if (attachment != '') {
                var attachmentArray = attachment.split(',');
                var index = attachmentArray.indexOf(id);
                if (index > -1) {
                    //var tmp = attachmentArray.splice(index, 1);
                    $('#attachment').val(attachmentArray.join(','));
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

        var slideListView = $('#slideList');
        var slideUploadListIns = upload.render({
            elem: '#btnSlide'
            , url: '{{url('admin/uploads/upload')}}'
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
                    fileList += res.data.src;
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



        var layer = layui.layer;
        form.verify({
            class_id:  function (value){
                if(value == '' || value == 0){
                    return '请选择栏目';
                }
            },
            title: function (value){
                if(value == ''){
                    return '请输入标题';
                }
            },
            content: function (value){
                if(value == ''){
                    return '请输入内容';
                }
            }
            //    title: [/[\u4e00-\u9fa5]{2,12}$/, '标题必须2到12位汉字'],
            //    intro: [/[\u4e00-\u9fa5]{2,30}$/, '权限介绍必须2到30位汉字'],
        });
        form.on('submit(formDemo)', function (data) {

            $.ajax({
                url: "{{url('save')}}",
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