

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
{{--    <form class="am-form am-form-horizontal" method="post"  id="addForm"  onsubmit="return false;">--}}
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr  @if (isset($hidden_option) && ($hidden_option & 1) == 1 ) style="display: none;"  @endif>
                <th>所属企业<span class="must"></span></th>
                <td>
                    <input type="hidden" class="select_id"  name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                    {{ $info['user_company_name'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>抬头名称<span class="must"></span></th>
                <td>
                    {{ $info['gmf_mc'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>纳税人识别号<span class="must"></span></th>
                <td>
                    {{ $info['gmf_nsrsbh'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>企业地址<span class="must"></span></th>
                <td>
                    {{ $info['gmf_dz'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>企业电话<span class="must"></span></th>
                <td>
                    {{ $info['gmf_dh'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>企业银行<span class="must"></span></th>
                <td>
                    {{ $info['gmf_yh'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>企业银行账号<span class="must"></span></th>
                <td>
                    {{ $info['gmf_yhzh'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>手机号<span class="must"></span></th>
                <td>
                    {{ $info['jff_phone'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>电子邮件<span class="must"></span></th>
                <td>
                    {{ $info['jff_email'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>开启状态<span class="must"></span></th>
                <td class="sel_pay_method">
                    {{ $info['open_status_text'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th> </th>
                <td>
{{--                    <button class="btn btn-l wnormal"  id="submitBtn" >提交</button>--}}
                    <button class="btn btn-l wnormal closeIframe" >关闭</button>
                </td>
            </tr>

        </table>
{{--    </form>--}}
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "companyQualityControlInvoiceBuyerInfo";

    var SAVE_URL = "{{ url('api/company/invoice_buyer/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('company/invoice_buyer')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('company/company/select')}}";// 选择所属企业
</script>
<script src="{{ asset('/js/company/QualityControl/InvoiceBuyer_info.js') }}?1"  type="text/javascript"></script>
</body>
</html>
