

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">
    <?php
    // 按收款方式分
    $payCount = count($pay_config_format);
    ?>
    @foreach ($pay_config_format as $pay_config_id => $pay_config)

        <?php
        // 再按企业 分
        $vod_orders = $config_vod_list[$pay_config_id] ?? [];
        ?>
        @foreach ($vod_orders as $pay_show_id => $company_vod_orders)
            <?php
            $pay_show_name = $pay_show_kv[$pay_show_id] ?? '';
            ?>
            {{ $pay_show_name ?? '' }}：
        <table  lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
            <colgroup>
                {{--                            <col width="75">--}}
                <col>
                <col>
                <col width="15%">
                <col width="15%">
                <col width="110">
                <col width="120">
{{--                <col width="85">--}}
            </colgroup>
            <thead>
            <tr>
                {{--                            <th>--}}
                {{--                                <label class="pos-rel">--}}
                {{--                                    <input type="checkbox" class="ace check_all" value="" onclick="otheraction.seledAll(this)">--}}
                {{--                                    <span>全选</span>--}}
                {{--                                </label>--}}
                {{--                            </th>--}}
                <th>
                    <span>所属分类<hr/>收费名称</span>
                </th>
                <th>
                    <span>单位<hr/>收费金额</span>
                </th>
                <th>
                    <span>优惠金额<hr/>优惠说明</span>
                </th>
                <th>
                    <span>待退金额<hr/>已退金额</span>
                </th>
                <th>
                    <span>最终金额<hr/>状态</span>
                </th>
                <th>
                    <span> 缴费状态<hr/>支付单号</span>
                </th>
            </tr>
            </thead>
            <tbody  id="data_list" >
            <?php
            $pay_method = $pay_config['pay_method'] ?? 0;
            $allow_pay_method = $pay_config['allow_pay_method'] ?? 0;
            $pay_company_name = $pay_config['pay_company_name'] ?? '';
            $totalPrice = 0;
            $data_ids = [];
            ?>
            @foreach ($company_vod_orders as $k => $data_info)
                <tr>
                    {{--                                <td >--}}
                    {{--                                    <label>--}}
                    {{--                                        <input onclick="otheraction.seledSingle(this)" type="checkbox" class="ace check_item"  name="staff_id[]"   value="{{ $data_info['id'] ?? '' }}" @if(isset($data_info['is_joined']) && ($data_info['is_joined'] & 1) == 1)  disabled @endif>--}}
                    {{--                                        <span class="lbl"></span>--}}
                    {{--                                    </label>--}}

                    {{--                                </td>--}}

                    <td>
                        {{ $data_info['type_no_text'] ?? '' }}
                        <hr/>
                        {{ $data_info['title'] ?? '' }}
                    </td>

                    <td>
                        {{ $data_info['user_company_name'] ?? '' }}
                        <hr/>
                        ￥{{ $data_info['pay_amount'] ?? '' }}
                    </td>
                    <td>
                        ￥{{ $data_info['discount_amount'] ?? '' }}
                        <hr/>
                        {!!  $data_info['discount_explain'] ?? ''  !!}
                    </td>
                    <td>
                        ￥{{ $data_info['wait_refund_amount'] ?? '' }}
                        <hr/>
                        ￥{{ $data_info['refunded_amount'] ?? '' }}
                    </td>
                    <td>
                        ￥<span style="color: red;font-size:20px;">{{ $data_info['final_amount'] ?? '' }}</span>
                        <hr/>
                        {{ $data_info['record_status_text'] ?? '' }}
                    </td>
                    <td>
                        {{ $data_info['pay_status_text'] ?? '' }}
                        <hr/>
                        {{ $data_info['order_no'] ?? '' }}
                    </td>
                </tr>
                <?php
                // $totalPrice += $data_info['price'];
                $totalPrice = bcadd($totalPrice, $data_info['final_amount'], 2);
                array_push($data_ids, $data_info['id']);
                ?>
            @endforeach
            <tr>
                <td colspan="3" style="text-align: right;" valign="top">
                    共<?php echo count($company_vod_orders)?>记录；总计：<span style="color: red;font-size:20px;">￥{{ $totalPrice ?? '' }}</span>元
                </td>
                <td colspan="3" style="text-align: left;">
                    <input type="hidden" name="pay_config_id" value="{{ $pay_config_id ?? 0 }}"/>
                    <input type="hidden" name="pay_show_id" value="{{ $pay_show_id ?? 0 }}"/>
                    <input type="hidden" name="ids" value="{{ implode(',', $data_ids) }}"/>
                    {{ $pay_company_name ?? '' }}<br/>
                    @foreach ($payMethod as $k=>$txt)
                        @if(!(isset($pay_method) && ($pay_method & $k) <=0 ))
                        <label ><input type="radio"  name="pay_method"  value="{{ $k }}"  @if(isset($defaultPayMethod) && $defaultPayMethod > 0 && ($defaultPayMethod & $k) == $k) checked="checked"  @endif @if(isset($pay_method) && ($pay_method & $k) <=0 ) disabled   @endif/>{{ $txt }} </label><br/>
                        @endif
                    @endforeach
                    <button class="layui-btn layui-btn-normal"  onclick="otheraction.paySave(this)">收款</button>
                </td>
            </tr>
            </tbody>
        </table>
        @endforeach
    @endforeach

</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "userQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/user/payment_record/ajax_join_class_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('user/payment_record')}}";//保存成功后跳转到的地址

    var PAY_SAVE_URL = "{{url('user/payment_record/pay_save')}}";// 收款页面
</script>
<script src="{{ asset('/js/user/QualityControl/PaymentRecord_pay.js') }}?11"  type="text/javascript"></script>
</body>
</html>
