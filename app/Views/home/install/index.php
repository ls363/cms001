<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
    <title>{$system_name} 安装</title>
    <script type="text/javascript" src="{PUBLIC_URL}/static/admin/js/jquery.min.js{$srand_time}"></script>
    <script type="text/javascript" src="{PUBLIC_URL}/static/install/js/install.js{$srand_time}"></script>
    <link href="{PUBLIC_URL}/static/install/style/css.css{$srand_time}" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="wrapper">
    <div class="logo"><img src="{PUBLIC_URL}/static/system/images/logo.gif"></div>

    <div class="content">
        <h1>许 可 协 议</h1>
        <p>感谢您选择 <strong>CMS001</strong> 内容管理系统（以下简称CMS001）。希望我们的努力能为您提供一个高效快速和强大的网站解决方案。 <br />
            <strong>CMS001</strong>的官方网站为 <strong>http://www.cms001.top</strong>，是 <strong>CMS001</strong> 产品的开发商，依法独立拥有 <strong>CMS001</strong> 产品著作权。<br />
            <strong>CMS001</strong> 著作权受到法律和国际公约保护。使用者：无论个人或组织、盈利与否、用途如何（包括以学习和研究为目的），均需仔细阅读本协议，在理解、同意、并遵守本协议的全部条款后，方可开始使用 <strong>CMS001</strong> 软件。 <br />
            本授权协议适用于 <strong>CMS001</strong> 的所有版本，<strong>绍兴上虞千里马网络有限公司</strong>拥有对本授权协议的最终解释权。</p>
        <p><strong>协议许可的权利</strong></p>
        <p> 1、CMS001是免费可商用的建站系统，免费版本不提供实时的技术支持，官网会更新使用教程。</p>

            <p>2、CMS001系统允许个人或公司进行任意二开及商用，但是不允许任何形式的破解行为，包括但不限于通过CMS001系统建设网站、二次开发、发布衍生版本等情况，对于任何破解的行为，我们将保留依法追究法律责任的权力，对于使用破解版本的用户，也将视为非法使用。</p>

        <p> 3、CMS001除了一个内核文件代码外，其它代码全部开源，并使用Apache2开源协议。对于任何基于CMS001进行二开的系统，应该遵守Apache2开源协议的有关要求。</p>

        <p>4、CMS001官方不对使用本软件所构建网站中的文章、商品和其它任何信息承担责任，不管您通过任何渠道下载本软件，您一旦开始安装CMS001，即被视为完全理解并接受CMS001授权声明的各项条款。</p>
        </p>


        <p><strong>有限担保和免责声明</strong></p>
        <p>本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。 <br />
            用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。 <br />
            <strong>绍兴上虞千里马网络有限公司</strong>不对使用本软件构建的网站中的文章或信息承担责任。 <br />
            有关 <strong>CMS001</strong> 最终用户授权协议、商业授权与技术服务的详细内容，均由 <strong>CMS001</strong> 官方网站独家提供。<strong>绍兴上虞千里马网络有限公司</strong>拥有在不事先通知的情况下，修改授权协议和服务价目表的权力，修改后的协议或价目表对自改变之日起的新授权用户生效。</p>
        <p>电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始安装 <strong>CMS001</strong>，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。</p>
        <p>版权所有 (c) 2005-<?php echo date('Y');?>，<strong>绍兴上虞千里马网络有限公司</strong>保留所有权利。</p>
    </div>






    <div class="footer">
        <p><input name="readpact" type="checkbox" id="readpact" value="" />
            <label for="readpact"><strong>我已经阅读并同意此协议</strong></label></p>
        <span class="butbox boxcenter">
      <input name="button" type="button" class="nextbut" onclick="document.getElementById('readpact').checked ?window.location.href='{{url('home/install/config')}}' : alert('您必须同意软件许可协议才能安装！');" value="" />
    </div>


</div>
</body>
</html>