<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
    <title>{$system_name}　安装</title>
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/layui/css/layui.css{$srand_time}" />
    <link rel="stylesheet" type="text/css" href="{PUBLIC_URL}/static/admin/css/admin.css{$srand_time}" />
    <script src="{PUBLIC_URL}/static/admin/layui/layui.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript">
    <?php if (PUBLIC_URL == ""){?>
    var layuiModulePath = '../../static/admin/js/module/';
    <?php }else{?>
    var layuiModulePath = '../../public/static/admin/js/module/';
    <?php }?>
    </script>
    <script src="{PUBLIC_URL}/static/admin/js/common.js{$srand_time}" type="text/javascript" charset="utf-8"></script>
    <script src="{PUBLIC_URL}/static/admin/js/jquery.min.js"></script>
    <script type="text/javascript" src="{PUBLIC_URL}/static/install/js/install.js{$srand_time}"></script>
    <link href="{PUBLIC_URL}/static/install/style/css.css{$srand_time}" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="wrapper">
    <h1>{$system_name} 系统安装</h1>
    <form class="layui-form"  action="" method="post" name="myform" id="myform">
        <input type="hidden" name="_ajax" value="1">
        <table>
            <tr><td colspan="2" class="box_label">网站信息</td></tr>
            <tr align="left" valign="middle">
                <td width="146" align="right" nowrap class="layui-required"><strong>网站名称：</strong></td>
                <td colspan="2" nowrap>
                    <input name="site_name" lay-verify="required" type="text" id="site_name" value="{$site_name}" size="35"> <span class="tip">网站名称</span>
                    <span class="required">*</span></td>
            </tr>
            <tr align="left" valign="middle">
                <td align="right" nowrap><strong>网站域名：</strong></td>
                <td colspan="2" nowrap><input type="hidden" id="http" name="http" value="{$http}" />{$http}://<input lay-verify="required" name="site_domain" required type="text" id="site_domain" value="{$site_domain}" size="35" />
                    <span class="tip">网站的域名,后面不要加“/”</span> <span class="required">*</span></td>
            </tr>
            <tr><td colspan="2" class="box_label">数据库信息</td></tr>

            <tr align="left" valign="middle" id="tr_sql1" >
                <td align="right" nowrap><strong>数据库服务器IP：</strong></td>
                <td colspan="2" nowrap><input lay-verify="required" name="db_host" type="text" id="db_host" value="{$db_host}" size="35" />
                    &nbsp; <span class="tip">数据库所在的服务器IP</span> <span class="required">*</span></td>
            </tr>
            <tr align="left" valign="middle" id="tr_sql2">
                <td align="right" nowrap><strong>数据库端口：</strong></td>
                <td colspan="2" nowrap><input lay-verify="required" name="db_port" type="number" id="db_port" value="{$db_port}" size="35" />
                    &nbsp; <span class="tip">MySQL数据库的端口，一般是3306</span> <span class="required">*</span></td>
            </tr>
            <tr align="left" valign="middle" id="tr_sql2">
                <td align="right" nowrap><strong>数据库名称：</strong></td>
                <td colspan="2" nowrap><input lay-verify="required" name="db_name" type="text" id="sql_tr2" value="{$db_name}" size="35" />
                    &nbsp; <span class="tip">MySQL数据库的名称</span> <span class="required">*</span></td>
            </tr>
            <tr align="left" valign="middle" id="tr_sql2">
                <td align="right" nowrap><strong>数据库字符集：</strong></td>
                <td colspan="2" nowrap><input lay-verify="required" name="db_charset" type="text" id="db_charset" value="{$db_charset}" size="35" />
                    &nbsp; <span class="tip">MySQL数据库的默认字符集</span> <span class="required">*</span></td>
            </tr>
            <tr align="left" valign="middle" id="tr_sql3" >
                <td align="right" nowrap><strong>数据库用户名：</strong></td>
                <td colspan="2" nowrap><input lay-verify="required" name="db_username" type="text" id="db_username" value="{$db_username}" size="35" />
                    &nbsp;
                    <span class="tip">MySQL数据库的用户名</span> <span class="required">*</span></td>
            </tr>
            <tr align="left" valign="middle">
                <td align="right" nowrap><strong>数据库密码：</strong></td>
                <td colspan="2" nowrap><input lay-verify="required" name="db_password" type="password" id="db_password" value="{$db_password}" size="35" />
                    &nbsp;<span class="see">
                                <img src="{PUBLIC_URL}/static/system/images/open_eye.png" id="openEye" class="eye" style="display:none;" />
                                <img src="{PUBLIC_URL}/static/system/images/close_eye.png" alt="隐藏密码" class="eye" id="closeEye" />
                            </span>
                    <span class="tip">MySQL数据库的密码</span> <span class="required">*</span></td>
            </tr>
            <tr align="left" valign="middle">
                <td align="right" nowrap><strong>表前辍：</strong></td>
                <td colspan="2" nowrap><input lay-verify="required" name="db_prefix" type="text" id="db_prefix" value="{$db_prefix}" size="35" />
                    &nbsp;
                    <span class="tip">数据库表的统一前辍</span> <span class="required">*</span> </td>
            </tr>
            <tr><td colspan="2" class="box_label">网站后台账号</td></tr>
            <tr align="left" valign="middle">
                <td align="right" nowrap><strong>后台目录：</strong></td>
                <td colspan="2" nowrap><input lay-verify="required" name="admin_dir" type="text" id="admin_dir" value="{$admin_dir}" size="15" />&nbsp;
                    <span class="tip">输入框为后台目录，完整后台地址 <span id="admin_url" style="font-weight: bold; color: blue;">{$http}://{$site_domain}/?{$admin_dir}</span> <button id="copyAdminUrl" class="layui-btn layui-btn-xs layui-btn-warm" style="border: none;" type="button">复制后台地址</button> <span class="required">*</span></td>
            </tr>
            <tr align="left" valign="middle">
                <td align="right" nowrap><strong>登录帐号：</strong></td>
                <td colspan="2" nowrap><input lay-verify="required" name="admin_username" type="text" id="admin_username" value="{$admin_username}" size="35" />&nbsp;
                    <span class="tip">网站后台帐号</span> <span class="required">*</span></td>
            </tr>
            <tr align="left" valign="middle">
                <td align="right" nowrap><strong>登录密码：</strong></td>
                <td colspan="2" nowrap><input lay-verify="required" name="admin_password" type="password" id="admin_password" value="{$admin_password}" size="35" />&nbsp;
                    <span class="see">
                                <img src="{PUBLIC_URL}/static/system/images/open_eye.png" id="openEye2" class="eye" style="display:none;" />
                                <img src="{PUBLIC_URL}/static/system/images/close_eye.png" alt="隐藏密码" class="eye" id="closeEye2" />
                            </span> <span class="tip">网站后台密码</span> <span class="required">*</span></td>
            </tr>

        </table>
        <div class="footer">
            <button id="btnInstall" class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo">安装</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>

    </form>

</div>
<script>

    function initDb(index){
        layui.use(['form','jquery','laypage', 'layer'], function() {
            var $ = layui.jquery;
            var layer = layui.layer;
            $.ajax({
                url:"{{url('initDb')}}",
                data:$('form').serialize(),
                type:'post',
                dataType:'json',
                success:function(res){
                    layer.close(index);
                    layer.msg(res.message,{icon:6});
                    //安装成功跳转到首页
                    window.location.href = "/";
                },
                error : function(XMLHttpRequest, textStatus, errorThrown) {
                    layer.msg('网络失败', {time: 1000});
                }
            });
        });
    }
    layui.use(['form','jquery','laypage', 'layer'], function() {
        var form = layui.form,
            $ = layui.jquery;
        var layer = layui.layer;


        form.render();
        var layer = layui.layer;
        form.verify({
            //    title: [/[\u4e00-\u9fa5]{2,12}$/, '标题必须2到12位汉字'],
            //    intro: [/[\u4e00-\u9fa5]{2,30}$/, '权限介绍必须2到30位汉字'],
        });
        var hasInstall = 0;
        form.on('submit(formDemo)', function(data) {
            if(hasInstall == 1){
                return ;
            }
            var index = layer.open({
                title: '安装提示',
                type: 1,
                skin: 'layui-layer-rim',
                area: ['300px', '180px'],
                content: '<div class="install_tip">系统正在安装中，请稍候<br >安装完成，窗口会自动关闭</div>',
                //maxmin: true,
                minStack: false, //最小化不堆叠在左下角
                id: 'popup_comment', //定义 ID，防止重复弹出
            });
            var DISABLED = 'layui-btn-disabled';
            $('#btnInstall').addClass(DISABLED); // 添加样式
            $('#btnInstall').attr('disabled', true);  // 添加属性
            hasInstall = 1;
            $.ajax({
                url:"{{url('save')}}",
                data:$('form').serialize(),
                type:'post',
                dataType:'json',
                success:function(res){
                    if(res.code == 200){
                        setTimeout("initDb("+index+")", 500);
                    }else{
                        layer.close(index);
                        var DISABLED = 'layui-btn-disabled';
                        $('#btnInstall').attr('disabled', false);  // 添加属性
                        $('#btnInstall').removeClass(DISABLED); // 添加样式
                        layer.msg(res.message,{shift: 6, time:2000, icon:5});
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
</body>
</html>