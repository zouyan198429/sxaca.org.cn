@extends('layouts.huawu')

@push('headscripts')
    {{--  本页单独使用 --}}
    <link href="{{asset('datetimepicker/bootstrap3/css/bootstrap.min.css')}}" rel="stylesheet" media="screen">
    <link href="{{asset('datetimepicker/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet" media="screen">
@endpush

@section('content')
	<input type="text" name="created_at" value="{{ $created_at ?? '' }}"  class="form-control form-date" placeholder="选择或者输入一个日期：yyyy-MM">
@endsection


@push('footscripts')
    {{--<script type="text/javascript" src="{{asset('datetimepicker/jquery/jquery-1.8.3.min.js')}}" charset="UTF-8"></script>--}}
    <script type="text/javascript" src="{{asset('datetimepicker/bootstrap3/js/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('datetimepicker/js/bootstrap-datetimepicker.js')}}" charset="UTF-8"></script>
    <script type="text/javascript" src="{{asset('datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js')}}" charset="UTF-8"></script>
	<script>
        //var SAVE_URL = "{ { url('api/handles/' . $pro_unit_id . '/ajax_save') }}";
        //var LIST_URL = "{ {url('handles/' . $pro_unit_id)}}";
        // 仅选择日期
        $(".form-date").datetimepicker(
            {
                language:  "zh-CN",
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 0,
                minuteStep:1,
                forceParse: 0,
                format: "yyyy-mm-dd hh:ii:ss"
            });


        var SUBMIT_FORM = true;//防止多次点击提交
var IS_LAYUI_PAGE = isLayuiIframePage();// 是否Layui的iframe弹出层；true: 是； false:不是
var IS_FRAME_PAGE = isIframePage();// 是否iframe弹出层；true: 是； false:不是

//获取当前窗口索引
var PARENT_LAYER_INDEX = getParentLayerIndex();

//让层自适应iframe
operateBathLayuiIframeSize(PARENT_LAYER_INDEX, [1], 500);// 最大化当前弹窗[layui弹窗时]

//关闭iframe
$(document).on("click",".closeIframe",function(){
    iframeclose(PARENT_LAYER_INDEX);
});
//刷新父窗口列表
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取
function parent_only_reset_list(reset_total){
    // window.parent.reset_list(true, true, reset_total, 2);//刷新父窗口列表
    let list_fun_name = window.parent.LIST_FUNCTION_NAME || 'reset_list';
    eval( 'window.parent.' + list_fun_name + '(' + true +', ' + true +', ' + reset_total +', 2)');
}
//关闭弹窗,并刷新父窗口列表
// reset_total 是否重新从数据库获取总页数 true:重新获取,false不重新获取
function parent_reset_list_iframe_close(reset_total){
    // window.parent.reset_list(true, true, reset_total, 2);//刷新父窗口列表
    let list_fun_name = window.parent.LIST_FUNCTION_NAME || 'reset_list';
    eval( 'window.parent.' + list_fun_name + '(' + true +', ' + true +', ' + reset_total +', 2)');
    operateLayuiIframeSize(PARENT_LAYER_INDEX, 4);// 关闭弹窗
}
//关闭弹窗
function parent_reset_list(){
    operateLayuiIframeSize(PARENT_LAYER_INDEX, 4);// 关闭弹窗
}

$(function(){
            // 九张图片上传
			@include('component.upfileone.piconejsinitincludenine')
            //提交
            $(document).on("click","#submitBtn",function(){
                //var index_query = layer.confirm('您确定提交保存吗？', {
                //    btn: ['确定','取消'] //按钮
                //}, function(){
                ajax_form();
                //    layer.close(index_query);
                // }, function(){
                //});
                return false;
            })

        });

	</script>
@endpush

@push('footlast')
@endpush
