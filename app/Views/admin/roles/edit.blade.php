@section('title', '管理员类型编辑')
@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label">类型标识：</label>
        <div class="layui-input-block">
            <input type="text" value="{{$info['name'] ?? ''}}" name="role_remark" required lay-verify="role_remark" placeholder="请输入管理员类型标识" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">类型名称：</label>
        <div class="layui-input-block">
            <input type="text" value="{{$info['display_name'] ?? ''}}" name="role_name" required lay-verify="role_name" placeholder="请输入管理员类型名称" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">类型描述：</label>
        <div class="layui-input-block">
            <textarea name="role_desc" placeholder="请输入管理员类型描述" class="layui-textarea" required lay-verify="role_desc">{{$info['description'] ?? ''}}</textarea>
        </div>
    </div>

    <div class="layui-form-item" style="display:none;">
        <label class="layui-form-label">数据权限：</label>
        <div class="layui-input-block">
            @foreach($dataRange as $k => $v)
                <input type="radio" name="data_permission" value="{{$k}}" title="{{$v}}" {{isset($info['data_permission']) && $info['data_permission'] == $k ?'checked':''}}>
            @endforeach
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">菜单权限：</label>
        <div class="layui-input-block">
            <input type="checkbox" name="" lay-skin="primary" lay-filter="mAllChoose" title="全选">
        </div>
        <div class="layui-input-block menu">
            @foreach($menu as $m)
                <p style="color: bold;"><input type="checkbox" name="menu_list[]"
                       value="{{$m['id']}}" lay-skin="primary" title="{{$m['title']}}"></p>
                @if(isset($m['children']))
                    @foreach($m['children'] as $mc)
                        <input type="checkbox" name="menu_list[]"
                               value="{{$mc['id']}}" lay-skin="primary" title="{{$mc['title']}}">
                    @endforeach
                @endif
            @endforeach
        </div>
    </div>
@endsection
@section('id',$id)
@section('js')
    <script>
        layui.use(['form','jquery','laypage', 'layer'], function() {
            var form = layui.form,
                $ = layui.jquery;
            /*
            form.on('checkbox(pAllChoose)', function(data) {
                var child = $(".permission").find('input[type="checkbox"]');
                child.each(function(index, item) {
                    console.log(data.elem.checked);
                    item.checked = data.elem.checked;
                });
                if(data.elem.checked)$(this).attr('title','全不选');
                else $(this).attr('title','全选');
                form.render('checkbox');
            });
*/

            var child = $(".menu").find('input[type="checkbox"]');
            var menuIds = {!! $menuIds !!};
            child.each(function(index, item) {
                //alert(item.value);
                if(menuIds.indexOf(item.value) != -1){
                    console.log(item);
                    item.checked = true;
                }
            });
            form.render('checkbox');

            form.on('checkbox(mAllChoose)', function(data) {
                var child = $(".menu").find('input[type="checkbox"]');
                child.each(function(index, item) {
                    item.checked = data.elem.checked;
                });
                if(data.elem.checked)$(this).attr('title','全不选');
                else $(this).attr('title','全选');
                form.render('checkbox');
            });

            form.render();
            var layer = layui.layer;
            form.verify({
                role_remark: function (value){
                    if(value == ''){
                        return '请输入管理员类型标识';
                    }
                },
                role_name: function (value){
                    if(value == ''){
                        return '请输入管理员类型名称';
                    }
                },
            //    role_desc: [/[*]{2,120}$/, '角色描述2到120位汉字'],
            });
            form.on('submit(formDemo)', function(data) {
                var chk_value =[];
                $('input[name="permission_list[]"]:checked').each(function(){
                    chk_value.push($(this).val());
                });
                if($("input[type='permission_list[]']").length>0&&chk_value.length==0){
                    layer.msg('至少选择一个角色权限',{shift: 6,icon:5});
                    return false;
                }
                $.ajax({
                    url:"{{url('/roles')}}",
                    data:$('form').serialize(),
                    type:'post',
                    dataType:'json',
                    success:function(res){
                        if(res.status == 1){
                            layer.msg(res.msg,{icon:6});
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
@extends('common.edit')
