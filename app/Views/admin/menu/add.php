@section('title', '菜单编辑')
@section('id', $id)
@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label">上级：</label>
        <div class="layui-input-block">
            <select name="parent_id" lay-verify="required">
                <option value=""></option>
                <option value="0" >一级菜单</option>
                {if is_array($menus) && $menus}
                {foreach $menus as $menus_child}
                <option value="{$menus_child['id']}">{$menus_child['title']}</option>
                {/foreach}
                {/if}
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">名称：</label>
        <div class="layui-input-block">
            <input type="text" value="{$menu['title'] ?? ''}" name="title" required lay-verify="title" placeholder="请输入名称" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">序号：</label>
        <div class="layui-input-block">
            <input type="number" value="{$menu['sort'] ?? ''}" name="sort" required lay-verify="order" placeholder="请输入数字" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">图标：</label>
        <div class="layui-input-block">
            <input type="hidden" name="icon" value="{$menu['icon'] ?? ''}" required lay-verify="required" placeholder="请选择图标" autocomplete="off" class="layui-input">
            <div id="icon" style="margin-top: 4px;"></div>
            <div id="icon_page"></div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">URL：</label>
        <div class="layui-input-block">
            <input type="text" value="{$menu['uri'] ?? ''}" name="uri" required lay-verify="uri" placeholder="请输入URL,样式如:/fzs" autocomplete="off" class="layui-input">
        </div>
    </div>
@endsection
@section('id',$id)
@section('js')
    <script>
        function chose_icon(obj){
            layui.use(['jquery'], function (){
                var $ = layui.jquery;
                $("input[name='icon']").val('&#xe'+$(obj).attr('data-icon'));
                if($(obj).hasClass('layui-btn-warm'))$(obj).removeClass('layui-btn-warm');
                else {
                    var icons = $('.fzs-icon');
                    icons.each(function(index, item) {
                        $(item).removeClass('layui-btn-warm');
                    });
                    $(obj).addClass('layui-btn-warm');
                }
            })

        }
        layui.use(['form','laypage', 'layer', 'jquery'], function() {
            var $ = layui.jquery;
            var form = layui.form;
            form.render();
            var laypage = layui.laypage
                ,layer = layui.layer;
            var data = [
                ['播放','&#xe652;'],['播放暂停','&#xe651;'],['音乐','&#xe6fc;'],['视频','&#xe6ed;'],['语音','&#xe688;'],['喇叭','&#xe645;'],['对话','&#xe611;'],['设置','&#xe614;'],['隐身','&#xe60f;'],['搜索','&#xe615;'],['分享','&#xe641;'],['刷新','&#x1002;'],['loading','&#xe63d;'],['loading','&#xe63e;'],['设置','&#xe620;'],['引擎','&#xe628;'],['阅卷错号','&#x1006;'],['错','&#x1007;'],['报表','&#xe629;'],['star','&#xe600;'],['圆点','&#xe617;'],['客服','&#xe606;'],['发布','&#xe609;'],['列表','&#xe60a;'],['图表','&#xe62c;'],['正确','&#x1005;'],['换肤','&#xe61b;'],['在线','&#xe610;'],['右右','&#xe602;'],['左左','&#xe603;'],['表格','&#xe62d;'],['树状','&#xe62e;'],['上传','&#xe62f;'],['添加','&#xe61f;'],['下载','&#xe601;'],['选择模版','&#xe630;'],['工具','&#xe631;'],['添加','&#xe654;'],['编辑','&#xe642;'],['删除','&#xe640;'],['向下','&#xe61a;'],['文件','&#xe621;'],['布局','&#xe632;'],['添加','&#xe608;'],['直播－翻页','&#xe633;'],['404','&#xe61c;'],['轮播组图','&#xe634;'],['帮助','&#xe607;'],['代码','&#xe635;'],['进水','&#xe636;'],['关于','&#xe60b;'],['向上','&#xe619;'],['日期','&#xe637;'],['文件','&#xe61d;'],['top','&#xe604;'],['对','&#xe605;'],['窗口','&#xe638;'],['表情','&#xe60c;'],['正确','&#xe616;'],['文件下载','&#xe61e;'],['图片','&#xe60d;'],['链接','&#xe64c;'],['记录','&#xe60e;'],['文件夹','&#xe622;'],['删除线','&#xe64f;'],['unlink','&#xe64d;'],['编辑_文字','&#xe639;'],['三角','&#xe623;'],['单选框-候选','&#xe63f;'],['单选框-选中','&#xe643;'],['居中对齐','&#xe647;'],['右对齐','&#xe648;'],['左对齐','&#xe649;'],['勾选框（未打勾）','&#xe626;'],['勾选框（已打勾）','&#xe627;'],['加粗','&#xe62b;'],['聊天','&#xe63a;'],['文件夹_反','&#xe624;'],['手机','&#xe63b;'],['表情','&#xe650;'],['html','&#xe64b;'],['表单','&#xe63c;'],['tab','&#xe62a;'],['代码','&#xe64e;'],['字体-下划线','&#xe646;'],['三角','&#xe625;'],['图片','&#xe64a;'],['斜体','&#xe644;'],['好友请求','&#xe612;']];
            var nums = 50;
            var render = function(data,curr){
                var arr = []
                    ,thisData = data.concat().splice(curr*nums-nums, nums);
                layui.each(thisData, function(index, item){
                    var iconclass = '';
                    if($("input[name='icon']").val()==item[1])iconclass = 'layui-btn-warm';
                    arr.push('<div class="layui-btn layui-btn-primary layui-btn-sm fzs-icon '+iconclass+'" data-icon="'+item[1].slice(4)+'" style="margin-bottom: 8px;margin-left:0px;margin-right:10px;" onclick="chose_icon(this)" title="'+item[0]+'"><i class="layui-icon">'+ item[1] +'</i></div>');
                });
                return arr.join("");

            };
            laypage.render({
                cont: 'icon_page'
                ,pages: Math.ceil(data.length/nums)
                ,groups:4
                ,jump: function(obj){
                    document.getElementById('icon').innerHTML = render(data, obj.curr);
                }
            });
            form.verify({
                title: function (value){
                    if(value == '' || value <1){
                        return '请输入菜单名称';
                    }
                },
                uri: [/^\/(.*)$/, 'URL格式错误'],
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
