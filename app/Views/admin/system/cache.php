@section('title', '编辑系统信息')
@section('id', $id)
@section('content')
<style type="text/css">
    .layui-form-label{width: 100px;}
    .layui-input-block{margin-left: 130px;}
    .layui-table th{font-weight: bold;}
    .layui-table p{ margin-bottom: 10px;}
</style>


    <table class="layui-table" style="width: 80%; margin-top:40px; margin-left: 20px; ">
        <colgroup>
            <col width="120"/>
            <col/>
            <col width="120"/>
        </colgroup>
        <tr>
            <th>缓存类型</th>
            <th>缓存说明</th>
            <th>操作</th>
        </tr>
        <tr>
            <td>概述</td>
            <td>CMS001使用了非常多的缓存，提升系统性能，节省服务器开销。
                路由缓存的位置为 根目录下的runtimes/，在调试模式下，为了防止数据没有变化，可以使用一键清理所有缓存。</td>
            <td>                <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="btnClearAll">一键清理所有缓存</button></td>
            </td>
        </tr>

        <tr>
            <td>路由缓存</td>
            <td><p>CMS001的路由地址： /routes/web.php, 这里可以注册自定义路由，包括URL的请求方式，中间件。路由缓存的文件，/route/route.php。</p>
                </td>
            <td><button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnClearRoute">清除路由缓存</button></td>
        </tr>
        <tr>
            <td>数据缓存</td>
            <td><p>数据缓存，通常是一些公共数据的缓存，如内容模型、内容栏目这一类很少改变的数据。</p>
                </td>
            <td><button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnClearData">清除数据缓存</button></td>
        </tr>
        <tr>
            <td>模板缓存</td>
            <td><p>模板缓存的位置，/runtimes/template_cache, 存储的是前台模板编译生成的PHP代码。</p>
                </td>
            <td><button type="button" class="layui-btn layui-btn-sm  layui-btn-normal" id="btnClearTemplate">清除模板缓存</button></td>
        </tr>
        <tr>
            <td>视图缓存</td>
            <td> <p>后台缓存的位置：/runtimes/views_cache。存储的是视图文件编译后生成的PHP代码。</p>
                </td>
            <td><button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnClearView">清除视图缓存</button></td>
        </tr>
        <tr>
            <td>表结构缓存</td>
            <td><p>表结构缓存的位置，/runtimes/table。存储的是数据库表结构，用于查询校验。</p>
                </td>
            <td><button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnClearTable">清除表结构缓存</button></td>
        </tr>
    </table>



@endsection


@section('js')
<script>

    layui.use(['form','jquery', 'layer'], function() {
        var $ = layui.jquery;
        var layer = layui.layer;
        $('#btnClearRoute').on('click', function () {
            $.get('{{url('admin/system/clearRoute')}}', {}, function (data) {
                if(data.code == 200) {
                    layer.msg('路由缓存清除成功!', {icon: 6});
                }
            }, 'json');
        });

        $('#btnClearData').on('click', function () {
            $.get('{{url('admin/system/clearData')}}', {}, function (data) {
                if(data.code == 200) {
                    layer.msg('数据缓存清除成功！', {icon: 6});
                }
            }, 'json');
        });

        $('#btnClearTemplate').on('click', function () {
            $.get('{{url('admin/system/clearTemplate')}}', {}, function (data) {
                if(data.code == 200) {
                    layer.msg('前台模板缓存清除成功！', {icon: 6});
                }
            }, 'json');
        });

        $('#btnClearView').on('click', function () {
            $.get('{{url('admin/system/clearView')}}', {}, function (data) {
                if(data.code == 200) {
                    layer.msg('后台模板缓存清除成功！', {icon: 6});
                }
            }, 'json');
        });

        $('#btnClearTable').on('click', function () {
            $.get('{{url('admin/system/clearTable')}}', {}, function (data) {
                if(data.code == 200) {
                    layer.msg('表结构缓存清除成功！', {icon: 6});
                }
            }, 'json');
        });

        $('#btnClearAll').on('click', function () {
            $.get('{{url('admin/system/clearAll')}}', {}, function (data) {
                if(data.code == 200) {
                    layer.msg('全部缓存清除成功！', {icon: 6});
                }
            }, 'json');
        });
    });
</script>
@endsection
@extends('admin.common.blank')