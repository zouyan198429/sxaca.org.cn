

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
 
<div class="mm">
    <?php
    // 按收款方式分
    $payCount = count($pay_config_format);
    ?>
    @foreach ($pay_config_format as $pay_config_id => $pay_config)

        <?php
        // 再按企业 分
        $course_order_staff = $config_staff_list[$pay_config_id] ?? [];
        ?>
        @foreach ($course_order_staff as $company_id => $company_course_order_staff)
            <?php
            $company_name = $company_kv[$company_id] ?? '';
            ?>
            {{ $company_name ?? '' }}：
        <table  lay-even class="layui-table table2 tableWidthFixed"  lay-size="lg"  id="dynamic-table">
            <colgroup>
                <col>
                <col>
                <col width="120">
                <col width="200">
            </colgroup>
            <thead>
            <tr>
                {{--                            <th>--}}
                {{--                                <label class="pos-rel">--}}
                {{--                                    <input type="checkbox" class="ace check_all" value="" onclick="otheraction.seledAll(this)">--}}
                {{--                                    <span>全选</span>--}}
                {{--                                </label>--}}
                {{--                            </th>--}}
                <th >
                    <span>姓名</span>
                </th>
                <th>
                    <span>手机号</span>
                </th>
				<th>
					身份证
				</th>
                <th>
                    <span>单价</span>
					<!-- <hr/>人员状态 -->
                </th>
                <!-- <th>
                    <span> 缴费状态<hr/>支付单号</span>
                </th> -->
               <!-- <th>
                    <span> 分班状态<hr/>班级</span>
                </th> -->
            </tr>
            </thead>

            <tbody  id="data_list" >
            <?php
            $pay_method = $pay_config['pay_method'] ?? 0;
            $allow_pay_method = $pay_config['allow_pay_method'] ?? 0;
            $pay_company_name = $pay_config['pay_company_name'] ?? '';
            $totalPrice = 0;
            $staff_ids = [];
            ?>
            @foreach ($company_course_order_staff as $k => $staff_info)
                <tr>
                    {{--                                <td >--}}
                    {{--                                    <label>--}}
                    {{--                                        <input onclick="otheraction.seledSingle(this)" type="checkbox" class="ace check_item"  name="staff_id[]"   value="{{ $staff_info['id'] ?? '' }}" @if(isset($staff_info['is_joined']) && ($staff_info['is_joined'] & 1) == 1)  disabled @endif>--}}
                    {{--                                        <span class="lbl"></span>--}}
                    {{--                                    </label>--}}

                    {{--                                </td>--}}
                    <td>
                        {{ $staff_info['real_name'] ?? '' }}({{ $staff_info['sex_text'] ?? '' }})
                    </td>
                    <td>
                        {{ $staff_info['mobile'] ?? '' }}
                    </td>
                    <td>
                        {{ $staff_info['id_number'] ?? '' }}
                    </td>
                    <td>
                        ￥{{ $staff_info['price'] ?? '' }}

                       <!-- {{ $staff_info['staff_status_text'] ?? '' }} -->
                    </td>
                   <!-- <td>
                        {{ $staff_info['pay_status_text'] ?? '' }}
                        <hr/>
                        {{ $staff_info['order_no'] ?? '' }}
                    </td>
                    <td>
                        {{ $staff_info['join_class_status_text'] ?? '' }}
                        <hr/>
                        {{ $staff_info['class_name'] ?? '' }}
                    </td> -->
                </tr>
                <?php
                // $totalPrice += $staff_info['price'];
                $totalPrice = bcadd($totalPrice, $staff_info['price'], 2);
                array_push($staff_ids, $staff_info['id']);
                ?>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: right;" valign="top">
                    共<?php echo count($company_course_order_staff)?>人；总计：￥{{ $totalPrice ?? '' }}元
                </td>
			</tr>
			</tbody>

			</table>
			<div style="height: 30px;"></div>
			<div class="pay_block">
                    <input type="hidden" name="pay_config_id" value="{{ $pay_config_id ?? 0 }}"/>
                    <input type="hidden" name="company_id" value="{{ $company_id ?? 0 }}"/>
                    <input type="hidden" name="ids" value="{{ implode(',', $staff_ids) }}"/>                   
					<div style="display: none;">{{ $pay_company_name ?? '' }}</div>					
					<br/>
                    @foreach ($payMethod as $k=>$txt)
                        @if(!(isset($pay_method) && ($pay_method & $k) <=0 ))
                            <label><input type="radio"  name="pay_method"  value="{{ $k }}"  @if(isset($defaultPayMethod) && $defaultPayMethod > 0 && ($defaultPayMethod & $k) == $k) checked="checked"  @endif @if(isset($pay_method) && ($pay_method & $k) <=0 ) disabled   @endif/>{{ $txt }} </label><br/>
                        @endif
                    @endforeach
					<div style="height: 30px;"></div>
                    <button class="layui-btn layui-btn-normal"  onclick="otheraction.paySave(this)">付款</button>
            </div>
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
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/company/course_order_staff/ajax_join_class_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('company/course_order_staff')}}";//保存成功后跳转到的地址

    var PAY_SAVE_URL = "{{url('company/course_order_staff/pay_save')}}";// 收款页面
</script>
<script src="{{ asset('/js/company/QualityControl/CourseOrderStaff_pay.js') }}?14"  type="text/javascript"></script>
</body>
</html>
