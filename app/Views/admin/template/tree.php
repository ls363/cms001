@section('title', '模板列表')
@section('content')
<div><input id="callback" value="" class="layui-input"/></div>
<div id="test2"></div>
@endsection
@section('js')
<script>
    var treeData = {$treeData};
    var callbackInput = '';

    //设置回调参数
    function setCallbackInput(inputName){
        callbackInput = inputName;
        layui.use(['jquery'], function(){
            var $ = layui.jquery;
            $('#callback').val(inputName);
        });
    }

    layui.use(['tree'], function() {
        var tree= layui.tree;
        tree.render({
            elem: '#test2'
            , data: treeData
            //,expandClick: false
            , showLine: true //关闭连接线
            , click: function (obj, state) {
                let path = obj.data.path;
                if(path.indexOf('.shtml') != -1){
                    console.log(path);
                    parent.saveTemplate(callbackInput, path);
                    closePopup();
                }
                //console.log(obj.data);
            }
            , oncheck: function (obj, checked, child) {
                if (checked) {
                    console.log('oncheck',obj[0]);
                }
            }
            , onsearch: function (data, num) {
                console.log(num);
            }
            , dragstart: function (obj, parent) {
                console.log(obj, parent);
            }
            , dragend: function (state, obj, target) {
                console.log(state, obj, target);
            }
        });
    });


    function closePopup(){
        var index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
    }



    layui.use(['jquery'], function() {
        var $=layui.jquery;

        $('.chooseBtn').on('click', function (){
            let ids = [] ;
            let names = [];
            $('input[name="id"]:checked').each(function(){
                ids.push($(this).attr('data-id'));
                names.push($(this).attr('data-name'));
            });
            parent.saveTemplate(ids, names);
            closePopup();
        });

    });



</script>
@endsection
@extends('admin.common.blank')
