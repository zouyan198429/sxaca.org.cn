

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- zui css -->
    <link rel="stylesheet" href="{{asset('dist/css/zui.min.css') }}">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">

    @foreach ($pay_config_format as $pay_config_id => $pay_config)
        <?php
        $vod_orders = $config_vod_list[$pay_config_id] ?? [];
        $tem_pay_method = $pay_config['pay_method'] ?? 0;
        $allow_pay_method = $pay_config['allow_pay_method'] ?? 0;
        $pay_company_name = $pay_config['pay_company_name'] ?? '';
        $totalPrice = 0;
        $data_ids = [];
        foreach($vod_orders as $k => $data_info){
            $totalPrice += $data_info['price'];
            array_push($data_ids, $data_info['id']);
        }
        ?>

            <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <div  class=" baguetteBoxOne gallery">

                    <input type="hidden" name="pay_config_id" value="{{ $pay_config_id ?? 0 }}"/>
                    <input type="hidden" name="company_id" value="{{ $company_id ?? 0 }}"/>
                    <input type="hidden" name="pay_method" value="{{ $pay_method ?? 0 }}"/>
                    <input type="hidden" name="id" value="{{ implode(',', $data_ids) }}"/>
                    <input type="hidden" name="total_price" value="{{ $totalPrice ?? '' }}"/>{{--总金额--}}
                    <input type="hidden" name="change_amount" value="0"/>{{--找零金额--}}
                    共{{ count($vod_orders) }}人；
                    总计：￥{{ $totalPrice ?? '' }}元；<hr/>
                    实收<input type="text" name="payment_amount" value="{{ $totalPrice ?? '' }}" placeholder="请输入实收金额" style="width: 80px;" @if (isset($pay_method) && $pay_method != 1 )  readonly="true"   @endif  onkeyup="numxs(this) " onafterpaste="numxs(this)" >元;
                    应找零<span style="color: red;"><strong class="change_amount">¥0</strong></span>元
                    <div class="auth_code_block" style="display: none;">

                        请扫条码：
                        <input type="text" name="auth_code" value="" placeholder="" style="width: 200px; ">
                    </div>
                    <button class="layui-btn layui-btn-sm layui-btn-normal layui-btn-radius"  id="submitBtn"  style="display: none;">确认收款</button>
                    <hr/>
                    收款帐号：{{ $pay_company_name ?? '' }}<br/>
                    收款方式：{{ $method_info['pay_name'] ?? '' }}<hr/>
                    @if (isset($method_info['resource_list']) && !empty($method_info['resource_list']) && !in_array($pay_method, [2,4,16,64]) )
                        收款图片：
                        @if (false)
                            <span class="resource_list"  style="display: none;">{{ json_encode($method_info['resource_list']) }}</span>
                            <span  class="resource_show"></span>
                        @else
                            @foreach ($method_info['resource_list'] as $resource_item)
                                <a href="{{ $resource_item['resource_url_format'] ?? '' }}" target='_blank'>
                {{--                    {{ $resource_item['resource_name'] ?? '' }}--查看--}}
                                    <img  src="{{ $resource_item['resource_url_format'] ?? '' }}"  style="width:200px;">
                                </a>
                            @endforeach

                        @endif
                        <hr/>
                    @else
                        <div class="qrcode_block" style="display:none;">
                            收款码：<span style="color: red;"><strong class="count_down_num"></strong></span>秒
                            <div id="qrcode"></div>
                            <hr/>
                        </div>
                    @endif
                    收款说明：<br/>{!!   $method_info['pay_remarks'] ?? '' !!}<hr/>
                </div>
            </form>
    @endforeach
</div>
{{--<button onclick="scanPay('1111', '555', '32323');">扫码支付测试</button>--}}
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}

<script src="{{asset('dist/lib/jquery-qrcode-master/jquery.qrcode.min.js')}}"></script>

@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/web/vod_orders/ajax_create_order') }}";// ajax保存记录地址
    var LIST_URL = "{{url('web/vod_orders')}}";//保存成功后跳转到的地址

    var DOWN_FILE_URL = "{{ url('web/down_file') }}";// 下载
    var DEL_FILE_URL = "{{ url('api/web/upload/ajax_del') }}";// 删除文件的接口地址

    var AJAX_QUERY_ORDER_WX_URL = "{{ url('api/web/vod_orders/ajax_wx_query_order') }}";// ajax查询微信扫码支付是否成功地址

    var WAIT_SECOND_NUM = 90;// 扫码支付等待时间
</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>

<script src="{{ asset('/js/web/QualityControl/Site/VodOrders_pay_save.js') }}?72"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
