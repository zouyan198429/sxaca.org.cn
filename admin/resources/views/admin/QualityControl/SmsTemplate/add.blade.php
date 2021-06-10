

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
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr>
                <th>所属模块<span class="must">*</span></th>
                <td class="sel_pay_method">
                    @foreach ($sms_module_kv as $k=>$txt)
                        <label><input type="radio"  name="module_id"  value="{{ $k }}"  @if(isset($defaultSmsModule) && $defaultSmsModule == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>模板名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="template_name" value="{{ $info['template_name'] ?? '' }}" placeholder="请输入模板名称"/>
                </td>
            </tr>
            <tr>
                <th>模板关键字<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="template_key" value="{{ $info['template_key'] ?? '' }}" placeholder="请输入模板关键字"/>
                    <p>唯一</p>
                </td>
            </tr>
            <tr>
                <th>模板类型<span class="must">*</span></th>
                <td class="sel_pay_method">
                    @foreach ($templateType as $k=>$txt)
                        <label><input type="radio"  name="template_type"  value="{{ $k }}"  @if(isset($defaultTemplateType) && $defaultTemplateType == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>模板ID<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="template_code" value="{{ $info['template_code'] ?? '' }}" placeholder="请输入模板ID"/>
                    <p>第三方内容，且唯一</p>
                </td>
            </tr>
            <tr>
                <th>签名名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="sign_name" value="{{ $info['sign_name'] ?? '' }}" placeholder="请输入签名名称"/>
                    <p>第三方内容</p>
                </td>
            </tr>
            <tr>
                <th>短信常用参数<span class="must"></span></th>
                <td>
                    @foreach ($sms_params_common_list as $k => $t_v)
                        <p>{{ $t_v['param_name'] ?? '' }}【{{ $t_v['param_code'] ?? '' }}】【{{ $t_v['param_type_text'] ?? '' }}】【{{ $t_v['date_time_format'] ?? '' }}】【{{ $t_v['fixed_val'] ?? '' }}】</p>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>所属模块参数<span class="must"></span></th>
                <td id="resetModulParams">

{{--                    @foreach ($sms_params_list as $k => $t_v)--}}
{{--                        <p>{{ $t_v['param_name'] ?? '' }}【{{ $t_v['param_code'] ?? '' }}】【{{ $t_v['param_type_text'] ?? '' }}】【{{ $t_v['date_time_format'] ?? '' }}】【{{ $t_v['fixed_val'] ?? '' }}】</p>--}}
{{--                    @endforeach--}}
                </td>
            </tr>
            <tr>
                <th>模板内容<span class="must">*</span></th>
                <td>
                    <textarea name="template_content" placeholder="请输入模板内容" class="layui-textarea">{{ replace_enter_char($info['template_content'] ?? '',2) }}</textarea>
                    <p>第三方内容，内容中可以使用参数，请参考【短信常用参数】、【所属模块参数】</p>
                    <p>格式如：您在{test_input}报名{test_val}操作，成功！开学时间：{test_datetime}！如有任何问题请联系{mobile}</p>
                </td>
            </tr>
            <tr style="display: none;">
                <th>限次编号<span class="must"></span></th>
                <td class="sel_limit_code">
                    @foreach ($sms_limit_kv as $k=>$txt)
                        <label><input type="checkbox"  name="limit_code[]"  value="{{ $k }}"  @if(isset($defaultSmsLimit) && (($defaultSmsLimit & $k)  == $k)) checked="checked"  @endif/>{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>开启状态<span class="must">*</span></th>
                <td class="sel_pay_method">
                    @foreach ($openStatus as $k=>$txt)
                        <label><input type="radio"  name="open_status"  value="{{ $k }}"  @if(isset($defaultOpenStatus) && $defaultOpenStatus == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th> </th>
                <td><button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
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
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/admin/sms_template/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/sms_template')}}";//保存成功后跳转到的地址

    var RESET_MODULE_PARAMS_URL = "{{ url('api/admin/sms_module_params/ajax_alist_all') }}";// 重新显示 所属模块参数
    var RESET_MODULE_PARAMS_BAIDU_TEMPLATE = "baidu_template_reset_params_data_list";//百度模板id
    var RESET_MODULE_SHOW_ID = "resetModulParams";

    var SMS_PARAMS_LIST = @json($sms_params_list ?? []) ;
    var SMS_PARAMS_LIST_JSON =  {'data_list': SMS_PARAMS_LIST };// piclistJson 数据列表json对象格式  {‘data_list’:[{'id':1,'resource_name':'aaa.jpg','resource_url':'picurl','created_at':'2018-07-05 23:00:06'}]}
</script>
<script src="{{ asset('/js/admin/QualityControl/SmsTemplate_edit.js') }}?5"  type="text/javascript"></script>
</body>
</html>
