//在右侧窗口关闭页面, 并激活新的url
function closeRightFrame(url){
    layui.use(['element', 'jquery'], function (){
        var $ = layui.jquery;
        var element = layui.element;
        //先刷新页面，体验更好
        var isActive = $('.main-layout-tab .layui-tab-content').find("iframe[name='iframe_" + url + "']");
        if(isActive.length > 0) {
            isActive[0].src = isActive[0].src;
        }
        //关闭编辑的窗口
        id = $('.main-layout-tab .layui-this').attr('lay-id');
        element.tabDelete('tab', id);
        //激活列表页窗口
        element.tabChange('tab', '_'+url);
    })
}

//获取活动窗体的ID
function getRightFrameId(){
	var id = 0;
	layui.use(['jquery'], function () {
		var $ = layui.jquery;
		id = $('.main-layout-tab .layui-this').attr('lay-id');
	});
	return id;
}

//根据菜单ID刷新窗体
function refreshRightFrame(id){
    layui.use(['element', 'jquery'], function () {
        var $ = layui.jquery;
        var element = layui.element;
        var isActive = $('.main-layout-tab .layui-tab-content').find("iframe[name='iframe_" + id + "']");
        if(isActive.length > 0) {
            isActive[0].src = isActive[0].src;
        }
    });
}

//在右侧窗口打开页面
function openRightFrame(id, text, url){
    layui.use(['element', 'jquery'], function (){
        var $ = layui.jquery;
        var element = layui.element;
        id = '_'+url;
        var isActive = $('.main-layout-tab .layui-tab-title').find("li[lay-id='" + id + "']");
        if(isActive.length > 0) {
            //切换到选项卡
            element.tabChange('tab', id);
        } else {
            element.tabAdd('tab', {
                title: text,
                content: '<iframe src="' + url + '" name="iframe' + id + '" class="iframe" framborder="0" data-id="' + id + '" scrolling="auto" width="100%"  height="100%"></iframe>',
                id: id
            });
            element.tabChange('tab', id);
        }
    })
}

layui.use(['layer', 'form', 'element', 'jquery', 'dialog'], function() {
	var layer = layui.layer;
	var element = layui.element;
	var form = layui.form;
	var $ = layui.jquery;
	var dialog = layui.dialog;
	var hideBtn = $('#hideBtn');
	var mainLayout = $('#main-layout');
	var mainMask = $('.main-mask');
	//监听导航点击
	element.on('nav(leftNav)', function(elem) {
		var link  = elem.context.dataset;
		var id = link.id;
		var url = link.url;
		var text = link.text;

		if(!url){
			return;
		}
		id = "_"+url;
		var isActive = $('.main-layout-tab .layui-tab-title').find("li[lay-id='" + id + "']");
		if(isActive.length > 0) {
			//切换到选项卡
			element.tabChange('tab', id);
		} else {
			element.tabAdd('tab', {
				title: text,
				content: '<iframe src="' + url + '" name="iframe' + id + '" class="iframe" framborder="0" data-id="' + id + '" scrolling="auto" width="100%"  height="100%"></iframe>',
				id: id
			});
			element.tabChange('tab', id);

		}
		mainLayout.removeClass('hide-side');
	});
	//监听导航点击
	element.on('nav(rightNav)', function(elem) {

		var link  = elem.context.dataset;
		var id = link.id;
		var url = link.url;
		var text = link.text;
	    id = "_"+url;
		if(!url){
			return;
		}
		var isActive = $('.main-layout-tab .layui-tab-title').find("li[lay-id=" + id + "]");
		if(isActive.length > 0) {
			//切换到选项卡
			element.tabChange('tab', id);
		} else {
			element.tabAdd('tab', {
				title: text,
				content: '<iframe src="' + url + '" name="iframe' + id + '" class="iframe" framborder="0" data-id="' + id + '" scrolling="auto" width="100%"  height="100%"></iframe>',
				id: id
			});
			element.tabChange('tab', id);

		}
		mainLayout.removeClass('hide-side');
	});
	//菜单隐藏显示
	hideBtn.on('click', function() {
		if(!mainLayout.hasClass('hide-side')) {
			mainLayout.addClass('hide-side');
		} else {
			mainLayout.removeClass('hide-side');
		}
	});
	//遮罩点击隐藏
	mainMask.on('click', function() {
		mainLayout.removeClass('hide-side');
	})

	//示范一个公告层
//	layer.open({
//		  type: 1
//		  ,title: false //不显示标题栏
//		  ,closeBtn: false
//		  ,area: '300px;'
//		  ,shade: 0.8
//		  ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
//		  ,resize: false
//		  ,btn: ['火速围观', '残忍拒绝']
//		  ,btnAlign: 'c'
//		  ,moveType: 1 //拖拽模式，0或者1
//		  ,content: '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;">后台模版1.1版本今日更新：<br><br><br>数据列表页...<br><br>编辑删除弹出功能<br><br>失去焦点排序功能<br>数据列表页<br>数据列表页<br>数据列表页</div>'
//		  ,success: function(layero){
//		    var btn = layero.find('.layui-layer-btn');
//		    btn.find('.layui-layer-btn0').attr({
//		      href: 'http://www.layui.com/'
//		      ,target: '_blank'
//		    });
//		  }
//		});
});
