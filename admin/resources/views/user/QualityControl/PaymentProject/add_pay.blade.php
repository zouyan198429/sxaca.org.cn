

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
    {{--  本页单独使用 --}}
    <script src="{{asset('dist/lib/kindeditor/kindeditor.min.js')}}"></script>
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
{{--        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>--}}
        <input type="hidden" name="id" value="0"/>
        <input type="hidden" name="payment_project_id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr  @if (isset($hidden_option) && ($hidden_option & 1) == 1 ) style="display: none;"  @endif>
                <th>所属企业<span class="must"></span></th>
                <td>
                    {{ $info['user_company_name'] ?? '' }}
                </td>
            </tr>
{{--            <tr>--}}
{{--                <th>企业id<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                    <input type="text" class="inp wnormal"  name="pay_company_id" value="0" placeholder="请输入企业id"/>--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <th>用户id<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                    <input type="text" class="inp wnormal"  name="pay_user_id" value="14" placeholder="请输入用户id"/>--}}
{{--                </td>--}}
{{--            </tr>--}}
            <tr>
                <th>收费名称<span class="must"></span></th>
                <td>
                    {{ $info['title'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>收款说明图片<span class="must"></span></th>
                <td class="baguetteBoxOne gallery"  id="resource_block">
                    <span class="resource_list"  style="display: none;">@json($info['resource_list'] ?? [])</span>
                    <span  class="resource_show"></span>
                </td>
            </tr>
            <tr>
                <th>收款文字说明<span class="must"></span></th>
                <td>
                    {!! $info['pay_explain'] ?? '' !!}
                </td>
            </tr>
            <tr>
                <th>收费金额<span class="must">*</span></th>
                <td>
                    ¥<input type="text" class="inp wnormal"  name="pay_amount" value="{{ $info['pay_amount'] ?? '' }}"
                            @if ( isset( $info['specified_amount_status']) && (in_array($info['specified_amount_status'], [\App\Models\QualityControl\PaymentProject::SPECIFIED_AMOUNT_STATUS_FIXED])) )
                            readonly
                            @endif
                            placeholder="请输入收费金额"  onkeyup="numxs(this) " onafterpaste="numxs(this)" />
                </td>
            </tr>
{{--            <tr>--}}
{{--                <th>优惠金额<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                    ¥<input type="text" class="inp wnormal"  name="discount_amount" value="{{ $info['discount_amount'] ?? '' }}" placeholder="请输入优惠金额"  onkeyup="decimal_numxs(this) " onafterpaste="decimal_numxs(this)" />--}}
{{--                    <p>增加金额请在金额前输入"-"号；否则默认为优惠金额</p>--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <th>优惠说明<span class="must"></span></th>--}}
{{--                <td>--}}
{{--                    <textarea name="discount_explain" placeholder="请输入优惠说明" class="layui-textarea"></textarea>--}}
{{--                </td>--}}
{{--            </tr>--}}
            <tr>
                <th>实收金额<span class="must">*</span></th>
                <td>
                    ¥<span style="color: red;font-size:45px;" class="real_amount">{{ $info['pay_amount'] ?? '' }}</span>
                    <input type="hidden" name="real_amount" value="{{ $info['pay_amount'] ?? '' }}"/>
                </td>
            </tr>
            @foreach ($info['project_fields'] as $k => $field_info)
                <?php
                $field_id =  $field_info['id'] ?? 0;
                $field_name =  $field_info['field_name'] ?? '';
                $val_type = $field_info['val_type'] ?? 0;
                $input_status = $field_info['input_status'] ?? 0;
                $required_status = $field_info['required_status'] ?? 0;
                $show_status = $field_info['show_status'] ?? 0;
                $sel_items = $field_info['sel_items'] ?? '';
                $sel_items_arr = [];
                if(in_array($val_type, [\App\Models\QualityControl\PaymentProjectFields::VAL_TYPE_RADIO, \App\Models\QualityControl\PaymentProjectFields::VAL_TYPE_CHECKBOX])){
                    $sel_items_arr = \App\Services\Tool::arrEqualKeyVal(explode('<br/>', $sel_items));
                }

                $input_name = "field_" . $field_id;

                ?>
                @if (($input_status & (\App\Models\QualityControl\PaymentProjectFields::INPUT_STATUS_USER_INPUT  |  \App\Models\QualityControl\PaymentProjectFields::INPUT_STATUS_COMPANY_INPUT) > 0 ) )
                <tr class="field_tr" data-field_name="{{ $field_name ?? '' }}" data-val_type="{{ $val_type ?? '0' }}"
                     data-id="{{ $field_id ?? '0' }}"  data-required_status="{{ $required_status ?? '0' }}" data-sel_items="{{ $sel_items ?? '' }}" >
                    <th>
                        {{ $field_name ?? '' }}
                        @if ((in_array($required_status, [\App\Models\QualityControl\PaymentProjectFields::REQUIRED_STATUS_REQUIRED])) )
                        <span class="must">*</span>
                        @endif
                    </th>
                    <td>
                        @switch($val_type)
                            @case(\App\Models\QualityControl\PaymentProjectFields::VAL_TYPE_TEXTAREA)
                                <textarea name="{{ $input_name ?? '' }}" placeholder="请输入{{ $field_name ?? '' }}" class="layui-textarea"></textarea>
                                @break
                            @case(\App\Models\QualityControl\PaymentProjectFields::VAL_TYPE_RICHTEXT)
                                <textarea class="kindeditor" name="{{ $input_name ?? '' }}" rows="15" id="doc-ta-1" style=" width:770px;height:400px;"></textarea>
                                @break
                            @case(\App\Models\QualityControl\PaymentProjectFields::VAL_TYPE_RADIO)
                                @foreach ($sel_items_arr as $k=>$txt)
                                    <label><input type="radio"  name="{{ $input_name ?? '' }}"  value="{{ $k }}"  />{{ $txt }} </label>
                                @endforeach
                                @break
                            @case(\App\Models\QualityControl\PaymentProjectFields::VAL_TYPE_CHECKBOX)

                                <span  class="checkbox">
                                    @foreach ($sel_items_arr as $k => $txt)
                                        <label><input type="checkbox"  name="{{ $input_name ?? '' }}[]"  value="{{ $k }}"/>{{ $txt }} </label>
                                    @endforeach
                                </span>
                                @break
                            @default
                                <input type="text" class="inp wnormal"  name="{{ $input_name ?? '' }}" value="" placeholder="请输入{{ $field_name ?? '' }}"/>
                                @break
                        @endswitch
                    </td>
                </tr>
                @endif
            @endforeach
            <tr>
                <th> </th>
                <td>
                                        <button class="btn btn-l wnormal"  id="submitBtn" >提交</button>
{{--                    <button class="btn btn-l wnormal closeIframe" >关闭</button>--}}
                </td>
            </tr>
        </table>
    </form>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "userQualityControlPaymentProjectInfo";

    var SAVE_URL = "{{ url('api/user/payment_project/ajax_pay_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('user/payment_project')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('user/company/select')}}";// 选择所属企业

    var DOWN_FILE_URL = "{{ url('user/down_file') }}";// 下载
    var DEL_FILE_URL = "{{ url('api/user/upload/ajax_del') }}";// 删除文件的接口地址

    var REQUIRED_STATUS_REQUIRED = {{ \App\Models\QualityControl\PaymentProjectFields::REQUIRED_STATUS_REQUIRED ?? 0 }};// 必填

    var VAL_TYPE_INPUT = {{ \App\Models\QualityControl\PaymentProjectFields::VAL_TYPE_INPUT ?? 0 }};// 输入框
    var VAL_TYPE_TEXTAREA = {{ \App\Models\QualityControl\PaymentProjectFields::VAL_TYPE_TEXTAREA ?? 0 }};// 多行文本
    var VAL_TYPE_RICHTEXT = {{ \App\Models\QualityControl\PaymentProjectFields::VAL_TYPE_RICHTEXT ?? 0 }};// 富文本
    var FIELD_VAL_TYPE_RADIO = {{ \App\Models\QualityControl\PaymentProjectFields::VAL_TYPE_RADIO ?? 0 }};// 单选框
    var FIELD_VAL_TYPE_CHECKBOX = {{ \App\Models\QualityControl\PaymentProjectFields::VAL_TYPE_CHECKBOX ?? 0 }};// 复选框

</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/user/QualityControl/PaymentProject_add_pay.js') }}?20"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
