@section('title', '文章列表')
@section('header')
<div class="layui-inline">
    <button class="layui-btn layui-btn-sm layui-btn-normal add-btn-right" data-desc="添加文章" data-url="{{url('info')}}"><i class="layui-icon">&#xe654;</i></button>
    <button class="layui-btn layui-btn-sm layui-btn-warm freshBtn"><i class="layui-icon">&#x1002;</i></button>
</div>
<div class="layui-inline">
    <select id="class_id" name="class_id" class="layui-select-sm" lay-ignore>
        {$classList}
    </select>
</div>
<div class="layui-inline">
    <select id="search_type" name="search_type" class="layui-select-sm" lay-ignore>
        <option value="">搜索方式</option>
        <option value="title">标题</option>
        <option value="intro">描述</option>
    </select>
</div>
<div class="layui-inline">
    <input type="text" name="search_text" value="{$input['search_text'] ?? ''}" id="search_text" class=" layui-input layui-input-sm" style="width: 120px;" />
</div>
<div class="layui-inline">
    <button class="layui-btn layui-btn-sm layui-btn-normal" value="搜索">搜索</button>
    <button type="button"  class="layui-btn layui-btn-sm layui-btn-normal" onclick="resetForm()" value="重置">重置</button>
</div>

@endsection
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
        <col class="hidden-xs" width="50">
        <col class="hidden-xs" width="120">
        <col class="hidden-xs" width="260">
        <col class="hidden-xs" width="100">
        <col class="hidden-xs" width="60">
        <col class="hidden-xs" width="60">
        <col class="hidden-xs" width="60">
        <col class="hidden-xs" width="180">
        <col class="hidden-xs" width="170">
    </colgroup>
    <thead>
    <tr>
        <th><input type="checkbox" name="" lay-skin="primary" lay-filter="allChoose"></th>
        <th class="hidden-xs">ID</th>
        <th class="hidden-xs">分类</th>
        <th class="hidden-xs">标题</th>
        <th>状态</th>
        <th>置顶</th>
        <th>推荐</th>
        <th>幻灯片</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    {foreach $list as $v}
    <tr>
        <td><input type="checkbox" name="ids[]" value="{$v['id']}" lay-skin="primary" /></td>
        <td class="hidden-xs">{$v['id']}</td>
        <td class="hidden-xs">{$v['class_name']}</td>
        <td class="hidden-xs"><a href="{{content_url($v['id'], $v['class_id'])}}" target="_blank">{$v['title']}</a></td>
        <th class="hidden-xs"><input type="checkbox" name="state" value="{$v['id']}" {$v['state'] == 1 ?'checked':''} lay-skin="switch" lay-filter="switchState" lay-text="上架|下架" title="状态"></th>
        <th class="hidden-xs"><input type="checkbox" name="is_top" value="{$v['id']}" {$v['is_top'] == 1 ?'checked':''} lay-skin="switch" lay-filter="switchState" lay-text="是|否" title="置顶"></th>
        <th class="hidden-xs"><input type="checkbox" name="is_recommend" value="{$v['id']}" {$v['is_recommend'] == 1 ?'checked':''} lay-skin="switch" lay-filter="switchState" lay-text="是|否" title="推荐"></th>
        <th class="hidden-xs"><input type="checkbox" name="is_slide" value="{$v['id']}" {$v['is_slide'] == 1 ?'checked':''} lay-skin="switch" lay-filter="switchState" lay-text="是|否" title="轮播图"></th>

        <td class="hidden-xs">{$v['created_at']}</td>
        <td>
            <div class="layui-inline">
                {if $system['make_html']==1}
                <button class="layui-btn layui-btn-xs layui-btn-normal make-btn" data-id="{$v.id}" data-desc="更新" data-url="{{ url('admin/make_html/makeContent', ['id'=>$v['id'],'class_id'=>$v['class_id']]) }}">更新</button>
                {/if}
                <button class="layui-btn layui-btn-xs layui-btn-normal edit-btn-right" data-id="{$v['id']}" data-h="500" data-desc="修改文章" data-url="{{url('info', ['id' => $v['id']])}}">修改</button>
                <button class="layui-btn layui-btn-xs layui-btn-danger del-btn" data-id="{$v['id']}" data-url="{{url('delete', ['id' => $v['id']])}}">删除</button>
            </div>
        </td>
    </tr>
    {/foreach}
    </tbody>
</table>
{/if}
{if(! empty($list))}
<div style="margin-bottom: 15px;">
    <div class="layui-inline">
        <button class="layui-btn layui-btn-sm layui-btn-danger" onclick="batchDelete()">删除所选</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal" onclick="move()" >将所选记录移动到</button>
    </div>
    <div class="layui-inline">
        <select id="move_class_id" name="move_class_id" class="layui-select-sm" style="height: 30px; line-height: 30px;" lay-ignore>
            {$classList}
        </select>
    </div>
</div>
{/if}

{$pageBar}

@endsection
@section('js')
<script>
    layui.use(['form', 'jquery','laydate', 'layer'], function() {
        var form = layui.form,
            $ = layui.jquery,
            laydate = layui.laydate,
            layer = layui.layer
        ;
        $('form.layui-form').attr('action', '{{url('index')}}');
        $('#search_type').val("{$input['search_type'] ?? ''}");
        $('#class_id').val("{$input['class_id'] ?? 0}");


        $('button.make-btn').on('click', function() {
            var url=$(this).attr('data-url');
            $.get(url, {}, function (data) {
                if (data.code == 200) {
                    layer.msg("内容HTML更新成功");
                } else {
                    layer.msg("内容HTML更新失败");
                }
            }, 'json'
            );
        });


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
            let value = this.checked ? 1 : 2;
            let field = $(this).attr('name');
            let title = $(this).attr('title');

            $.get('{{url('setField')}}', {id: this.value, field:field, value: value}, function (data) {
                if (data.code == 200) {
                    layer.msg(title+"修改成功");
                } else {
                    layer.msg(title+"修改失败");
                }
            }, 'json');
        });

        form.render();
        form.on('submit(formDemo)', function(data) {
            console.log(data);
            return false;
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
                $.post('{{url('move')}}', {ids:chk_value, class_id:class_id, _token:token}, function (data){
                    console.log(data);
                    if(data.code == 200){
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
                $.post('{{url('batchDelete')}}', {ids:chk_value, _token:token}, function (data){
                    console.log(data);
                    if(data.code == 200){
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