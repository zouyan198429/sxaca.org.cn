

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
    <form class="am-form am-form-horizontal" method="post"  id="addForm" onsubmit="return false;">
{{--        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />--}}
{{--        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>--}}
{{--        <input type="hidden" name="company_id" value="{{ $company_id ?? 0 }}"/>--}}
        <input type="hidden" name="sms_operate_no" value="{{ $sms_operate_no ?? 0 }}"/>
        @if(isset($sms_operate_no) && in_array($sms_operate_no, [2, 4]))
        <input type="hidden" name="ids" value="{{ $ids ?? 0 }}"/>
        @endif
        <input type="hidden" name="sms_operate_type" value="{{ $sms_operate_type ?? 0 }}"/>

        <table class="table1">
            <tr>
                <th>所属短信模板<span class="must">*</span></th>
                <td class="sel_pay_method">
                    @foreach ($sms_template_list as $k=>$info)
                        <span class="template_block">
                            <label><input type="radio"  name="sms_template_id"  value="{{ $info['id'] ?? 0 }}"  @if(isset($defaultSmsTemplateId) && $defaultSmsTemplateId == $info['id']) checked="checked"  @endif />{{ $info['template_name'] ?? 0 }} </label>
                            <span class="template_info" style="display: none;">@json($info ?? [])</span>
                        </span>
                    @endforeach
                    <br/><br/>
                    <p>注：如果没有合适的【短信模板】，请先到第三方短信平台维护模板并在【短信管理】-》【短信模板管理】维护相应的短信模板。</p>
                </td>
            </tr>
            <tr   @if(isset($sms_operate_type) && $sms_operate_type == 2)  style="display: none;" @endif>
                <th>接收短信手机号字段<span class="must">*</span></th>
                <td class="sel_pay_method">
                    @foreach ($smsMobileFieldKV as $k=>$txt)
                        <label><input type="radio"  name="sms_mobile_field"  value="{{ $k }}"  @if(isset($defaultSmsMobileField) && $defaultSmsMobileField == $k) checked="checked"  @endif />{{ $txt }}【{{ $k }}】 </label>
                    @endforeach
                </td>
            </tr>
            <tr  @if(isset($sms_operate_type) && $sms_operate_type == 1)  style="display: none;" @endif >
                <th>短信测试手机号<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="sms_mobile" value="{{ $info['mobile'] ?? '' }}" placeholder="请输入短信测试手机号"/>
                </td>
            </tr>
            <tr>
                <th>模板类型<span class="must">*</span></th>
                <td>
                    <span class="template_type_text"></span>
                </td>
            </tr>
            <tr>
                <th>模板ID<span class="must">*</span></th>
                <td>
                    <span class="template_code"></span>
                </td>
            </tr>
            <tr>
                <th>签名名称<span class="must">*</span></th>
                <td>
                    <span class="sign_name"></span>
                </td>
            </tr>
            <tr>
                <th>模板内容<span class="must">*</span></th>
                <td>
                    <span class="template_content"></span>
                </td>
            </tr>
            <tr class="staff_td">
                <th>模板参数<span class="must">*</span></th>
                <td>

                    <table class="table2">
                        <thead>
                        <tr>
                            <th>参数名称</th>
                            <th>参数代码</th>
                            <th>参数类型</th>
                            <th>手动输入</th>
{{--                            <th>字段匹配</th>--}}
                            <th>日期时间格式化</th>
                            <th>固定值</th>
                        </tr>
                        </thead>
                        <tbody class="data_list   baguetteBoxOne gallery" >

                        </tbody>


                    </table>
                </td>
            </tr>
            <tr>
                <th> </th>
                <td><button class="btn btn-l wnormal"  id="submitBtn" >发送短信</button></td>
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
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddsmsset";

    var SAVE_URL = "{{ url('api/admin/sms_template/ajax_sms_send') }}";// ajax保存记录地址-- 测试发送短信
    var LIST_URL = "{{url('admin/sms_template')}}";//保存成功后跳转到的地址

    var BAIDU_TEMPLATE_SMS_PARAMS_NAME = 'baidu_template_sms_params_list';
    var SMS_OPERATE_TYPE = {{ $sms_operate_type ?? 0 }};

</script>
<script src="{{ asset('/js/admin/QualityControl/SmsTemplate_sms_send.js') }}?25"  type="text/javascript"></script>
</body>
</html>

