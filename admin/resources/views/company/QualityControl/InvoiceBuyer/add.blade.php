

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
            <tr  @if (isset($hidden_option) && ($hidden_option & 1) == 1 ) style="display: none;"  @endif>
                <th>所属企业<span class="must">*</span></th>
                <td>
                    <input type="hidden" class="select_id"  name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                    <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                    <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择所属企业</button>
                </td>
            </tr>
            <tr>
                <th>开户号<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="gmf_mc" value="{{ $info['gmf_mc'] ?? '' }}" placeholder="请输入抬头名称"/>
                    <p>[公司名称或个人]</p>
                </td>
            </tr>
            <tr>
                <th>纳税人识别号<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="gmf_nsrsbh" value="{{ $info['gmf_nsrsbh'] ?? '' }}" placeholder="请输入纳税人识别号"/>
                </td>
            </tr>
            <tr>
                <th>企业地址<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="gmf_dz" value="{{ $info['gmf_dz'] ?? '' }}" placeholder="请输入企业地址"/>
                </td>
            </tr>
            <tr>
                <th>联系电话<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="gmf_dh" value="{{ $info['gmf_dh'] ?? '' }}" placeholder="请输入企业电话"/>
                </td>
            </tr>
            <tr>
                <th>开户行<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="gmf_yh" value="{{ $info['gmf_yh'] ?? '' }}" placeholder="请输入企业银行"/>
                </td>
            </tr>
            <tr>
                <th>银行账号<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="gmf_yhzh" value="{{ $info['gmf_yhzh'] ?? '' }}" placeholder="请输入企业银行账号"/>
                </td>
            </tr>
            <tr>
                <th>手机号<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="jff_phone" value="{{ $info['jff_phone'] ?? '' }}" placeholder="请输入手机号"/>
                    <p>针对税控盒子主动交付，需要填写</p>
                </td>
            </tr>
            <tr>
                <th>电子邮件<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="jff_email" value="{{ $info['jff_email'] ?? '' }}" placeholder="请输入电子邮件"/>
                    <p>针对税控盒子主动交付，需要填写</p>
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
    var PARENT_BUSINESS_FUN_NAME = "companyQualityControlInvoiceBuyeredit";

    var SAVE_URL = "{{ url('api/company/invoice_buyer/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('company/invoice_buyer')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业
</script>
<script src="{{ asset('/js/company/QualityControl/InvoiceBuyer_edit.js') }}?2"  type="text/javascript"></script>
</body>
</html>
