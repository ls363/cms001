
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>自定义表单 </title>
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/layui/css/layui.css" />
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/css/admin.css" />
    <script src="{PUBLIC_URL}/static/admin/layui/layui.js" type="text/javascript" charset="utf-8"></script>
    <script src="{PUBLIC_URL}/static/admin/js/common.js?v=112" type="text/javascript" charset="utf-8"></script>
</head>
<body>
<div class="wrap-container clearfix">
    <div class="column-content-detail">
        <form class="layui-form" action="">
            <div class="layui-form-item">
                <div class="layui-inline tool-btn">
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-sm layui-btn-warm freshBtn"><i class="layui-icon">&#x1002;</i></button>
                    </div>
                </div>
                <?php echo csrf_token(); ?>
            </div>
        </form>
        <div class="layui-form" id="table-list">
            <table class="layui-table" lay-even lay-skin="nob">
                <colgroup>
                    <col class="hidden-xs" width="50">
                    <col class="hidden-xs" width="150">
                    <col>
                    <col>
                    <col>
                    <col class="hidden-xs" width="200">
                    <col width="200">
                </colgroup>
                <thead>

                <tr>
                    <th class="hidden-xs">ID</th>
                    <th class="hidden-xs">表单名称</th>
                    <th>表单描述</th>
                    <th>前台显示标题</th>
                    <th>数据存储表</th>
                    <th class="hidden-xs">操作时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                {if !empty($list)}
                <tbody>
                {foreach $list as $v}
                <tr>
                    <td class="hidden-xs">{$v.id}</td>
                    <td class="hidden-xs">{$v.title}</td>
                    <td>{$v.intro}</td>
                    <td>{$v.front_title}</td>
                    <td>{$v.form_table}</td>
                    <td class="hidden-xs">{$v.created_at}</td>
                    <td>
                        <div class="layui-inline">
                            <a href="{{ url('dataList', ['form_id'=> $v['id']]) }}" class="layui-btn layui-btn-xs layui-btn-normal" ><i class="layui-icon">表单数据</i></a>
                            <button class="layui-btn layui-btn-xs layui-btn-normal edit-btn" data-id="{$v.id}" data-desc="修改" data-url="{{ url('info', ['id'=>$v['id']]) }}"><i class="layui-icon">&#xe642;</i></button>
                            <button class="layui-btn layui-btn-xs layui-btn-danger del-btn" data-id="{$v.id}" data-url="{{ url('delete', ['id'=> $v['id']]) }}"><i class="layui-icon">&#xe640;</i></button>
                        </div>
                    </td>
                </tr>
                {/foreach}
                </tbody>
                {/if}
            </table>
        </div>
    </div>
</div>
</body>
<script>
    layui.use(['form', 'jquery','laydate', 'layer'], function() {
        var form = layui.form,
            $ = layui.jquery,
            laydate = layui.laydate,
            layer = layui.layer
        ;

        form.render();
        form.on('submit(formDemo)', function(data) {
        });
    });
</script>
</html>