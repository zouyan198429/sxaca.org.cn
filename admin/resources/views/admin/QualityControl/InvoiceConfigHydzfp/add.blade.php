

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
            <tr  @if (isset($hidden_option) && (($hidden_option & 2) == 2) ) style="display: none;"  @endif>
                <th>收款帐号<span class="must">*</span></th>
                <td>
                    @foreach ($pay_config_kv as $k=>$txt)
                        <label><input type="radio"  name="pay_config_id"  value="{{ $k }}"  @if(isset($defaultPayConfig) && $defaultPayConfig == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>设备编号<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="tax_num" value="{{ $info['tax_num'] ?? '' }}" placeholder="请输入设备编号"/>
                </td>
            </tr>
            <tr>
                <th>应用OPENID<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal" style="width: 90%"  name="open_id" value="{{ $info['open_id'] ?? '' }}" placeholder="请输入应用OPENID"/>
                </td>
            </tr>
            <tr>
                <th>应用密匙<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal" style="width: 90%"  name="app_secret" value="{{ $info['app_secret'] ?? '' }}" placeholder="请输入应用密匙"/>
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

    var SAVE_URL = "{{ url('api/admin/invoice_config_hydzfp/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/invoice_config_hydzfp')}}";//保存成功后跳转到的地址
</script>
<script src="{{ asset('/js/admin/QualityControl/InvoiceConfigHydzfp_edit.js') }}?2"  type="text/javascript"></script>
</body>
</html>
