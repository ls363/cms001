function passwordDisplay(openImg, closeImg, input) {
//鼠标松开时密码隐藏
    $("#"+openImg).mouseup(function () {
        //修改input标签的类型
        $("#"+input).attr("type", "password");
        //显示密码的图片隐藏
        $("#"+openImg).hide();
        //隐藏密码的图片显示
        $("#"+closeImg).show();
    });
//鼠标按下时密码显示
    $("#"+closeImg).mousedown(function () {
        //修改input标签的类型
        $("#"+input).attr("type", "text");
        //隐藏密码的图片显示
        $("#"+closeImg).hide();
        //显示密码的图片隐藏
        $("#"+openImg).show();
    });
//鼠标移出时，密码隐藏
    $("#"+openImg).mousemove(function () {
        //修改input标签的类型
        $("#"+input).attr("type", "password");
        $("#"+openImg).hide();
        $("#"+closeImg).show();
    });

}

$(document).ready(function (){
    passwordDisplay('openEye', 'closeEye', 'db_password');
    passwordDisplay('openEye2', 'closeEye2', 'admin_password');
    $('#admin_dir').blur(function(){
        var url = $('#http').val() + "://"+$('#site_domain').val()+'/?'+$('#admin_dir').val();
        $('#admin_url').html(url);
    });

    $('#copyAdminUrl').click(function() {
        var content = $('#admin_url').html();
        var tempElement = $('<textarea>').val(content).appendTo('body').select();
        document.execCommand('copy');
        tempElement.remove();
        alert('内容已复制到粘贴板！');
        return false;
      });
});