@section('title', '文章列表')

@section('table')
<style type="text/css">
    .layui-textarea{ min-height: 60px;}
</style>
{if(empty($list))}
<div class="no_data">
    没有符合条件的记录
</div>
{else}
<table class="layui-table" lay-even lay-skin="nob" style="margin-bottom: 15px;">
    <colgroup>
        <col width="50">
        <col width="50">
        <col width="120">
        <col width="300">
        <col width="100">
        <col width="180">
        <col width="180">
        <col width="140">
    </colgroup>
    <thead>
    <tr>
        <th><input type="checkbox" name="" lay-skin="primary" lay-filter="allChoose"></th>
        <th class="hidden-xs">ID</th>
        <th class="hidden-xs">栏目</th>
        <th class="hidden-xs">标题</th>
        <th class="td_center">状态</th>
        <th class="td_center">访问量</th>
        <th class="td_center">创建时间</th>
        <th class="td_center">操作</th>
    </tr>
    </thead>
    <tbody>
    {foreach $list as $v}
    <tr>
        <td><input type="checkbox" name="ids[]" value="{$v['id']}" lay-skin="primary" /></td>
        <td class="hidden-xs">{$v['id']}</td>
        <td class="hidden-xs">{$v['title']}</td>
        <td class="hidden-xs"><a href="{$v['url']}" target="_blank">{$v['page_title']}</a></td>
        <th class="hidden-xs td_center"><input type="checkbox" name="state" value="{$v['id']}" {$v['state'] == 1 ?'checked':''} lay-skin="switch" lay-filter="switchState" lay-text="上架|下架" title="开关"></th>
        <td class="hidden-xs td_center">{$v['hits']}</td>
        <td class="hidden-xs td_center">{$v['created_at']}</td>
        <td class="td_center">
            <div class="layui-inline">
                {if $system['make_html']==1}
                <button class="layui-btn layui-btn-xs layui-btn-normal make-btn" data-id="{$v.id}" data-desc="更新" data-url="{{ url('admin/make_html/makeSingle', ['class_id'=>$v['id']]) }}">更新</button>
                {/if}
                <button class="layui-btn layui-btn-xs layui-btn-normal edit-btn-right" data-id="{$v['id']}" data-h="500" data-desc="编辑{$v.title}" data-url="{{url('info', ['class_id' => $v['id']])}}">修改</button>
                <button class="layui-btn layui-btn-xs layui-btn-danger del-btn" data-id="{$v['id']}" data-url="{{url('delete', ['id' => $v['id']])}}">删除</button>
            </div>
        </td>
    </tr>
    {/foreach}
    </tbody>
</table>
{/if}

@endsection
@section('js')
<script>
    layui.use(['form', 'jquery','laydate', 'layer'], function() {
        var form = layui.form,
            $ = layui.jquery,
            laydate = layui.laydate,
            layer = layui.layer
        ;

        $('#search_type').val("{$input['search_type'] ?? ''}");
        $('#class_id').val("{$input['class_id'] ?? 0}");

        //顶部添加
        $('.add-btn-right').click(function() {
            var url=$(this).attr('data-url');
            var desc=$(this).attr('data-desc');
            //处理来源窗口
            let frameId = parent.getRightFrameId();
            if(url.indexOf('?') == -1){
                url += '?sourceFrameId='+frameId
            }else{
                url += '&sourceFrameId='+frameId
            }
            //在保侧ifarme中打开,执行操作完成刷新
            parent.openRightFrame('content_add', desc, url);
            return false;
        });

        $('button.make-btn').on('click', function() {
            var url=$(this).attr('data-url');
            $.get(url, {}, function (data) {
                if (data.code == 200) {
                    layer.msg("单页HTML更新成功");
                } else {
                    layer.msg("单页HTML更新失败");
                }
            }, 'json'
            );
        });

        //编辑栏目
        $('#table-list').on('click', '.edit-btn-right', function() {
            var id=$(this).attr('data-id');
            var url=$(this).attr('data-url');
            var desc=$(this).attr('data-desc');
            //处理来源窗口
            let frameId = parent.getRightFrameId();
            if(url.indexOf('?') == -1){
                url += '?sourceFrameId='+frameId
            }else{
                url += '&sourceFrameId='+frameId
            }
            //在保侧ifarme中打开,执行操作完成刷新
            parent.openRightFrame('content_'+id, desc, url);
            return false;
        })



        form.on('switch(switchState)', function (obj) {
            var state = this.checked ? 1 : 2;
            $.get('{{url('setField')}}', {id: this.value, field:'state', value: state}, function (data) {
                if (data.code == 200) {
                    layer.msg("状态修改成功");
                } else {
                    layer.msg("状态修改失败");
                }
            }, 'json');
        });

        form.on('switch(switchTop)', function (obj) {
            var state = this.checked ? 1 : 2;
            $.get('{{url('setField')}}', {id: this.value, field:'is_top', value: state}, function (data) {
                if (data.code == 200) {
                    layer.msg("置顶修改成功");
                } else {
                    layer.msg("置顶修改失败");
                }
            }, 'json');
        });

        form.on('switch(switchRecommend)', function (obj) {
            var state = this.checked ? 1 : 2;
            $.get('{{url('setField')}}', {id: this.value, field:'is_recommend', value: state}, function (data) {
                if (data.code == 200) {
                    layer.msg("推荐修改成功");
                } else {
                    layer.msg("推荐修改失败");
                }
            }, 'json');
        });

        form.on('switch(switchSlide)', function (obj) {
            var state = this.checked ? 1 : 2;
            $.get('{{url('setField')}}', {id: this.value, field:'is_slide', value: state}, function (data) {
                if (data.code == 200) {
                    layer.msg("幻灯片修改成功");
                } else {
                    layer.msg("幻灯片修改失败");
                }
            }, 'json');
        });

        form.render();
        form.on('submit(formDemo)', function(data) {
        });
    });

    function move(){
        layui.use(['form', 'jquery', 'layer'], function() {
            var $ = layui.jquery;
            var layer = layui.layer;

            var token = $('input[name="_token"]').val();

            var chk_value =[];
            $('input[name="ids[]"]:checked').each(function(){
                chk_value.push($(this).val());
            });
            if(chk_value.length==0){
                layer.msg('请选择要移动的记录',{shift: 6,icon:5});
                return false;
            }
            var class_id = $('#move_class_id').val();
            if(class_id < 1){
                layer.msg('请选择链接类型',{shift: 6,icon:5});
                return false;
            }
            layer.confirm('确定要移动所选记录？', function (index){
                $.post('/link/move', {ids:chk_value, class_id:class_id, _token:token}, function (data){
                    console.log(data);
                    if(data.status == 1){
                        layer.msg('操作成功');
                        window.location.reload();
                    }
                },'json');
                layer.close(index);
            });

        });
    }

    function batchDelete(){
        layui.use(['form', 'jquery', 'layer'], function() {
            var $ = layui.jquery;
            var layer = layui.layer;

            var token = $('input[name="_token"]').val();

            var chk_value =[];
            $('input[name="ids[]"]:checked').each(function(){
                chk_value.push($(this).val());
            });
            if(chk_value.length==0){
                layer.msg('请选择要删除的记录',{shift: 6,icon:5});
                return false;
            }

            layer.confirm('确定要删除所选记录？', function (index){
                $.post('/link/batchDelete', {ids:chk_value, _token:token}, function (data){
                    console.log(data);
                    if(data.status == 1){
                        layer.msg('操作成功');
                        window.location.reload();
                    }
                },'json');
                layer.close(index);
            });

        });
    }

</script>
@endsection
@extends('admin.common.list')