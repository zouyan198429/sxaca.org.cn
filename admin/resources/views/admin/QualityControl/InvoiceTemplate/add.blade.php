

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
                <th>开票服务商<span class="must">*</span></th>
                <td>
                    @foreach ($invoiceService as $k=>$txt)
                        <label><input type="radio"  name="invoice_service"  value="{{ $k }}"  @if(isset($defaultInvoiceService) && $defaultInvoiceService == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>发票模板名称<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="template_name" value="{{ $info['template_name'] ?? '' }}" placeholder="请输入发票模板名称"/>
                </td>
            </tr>
            <tr>
                <th>征税方式<span class="must">*</span></th>
                <td>
                    @foreach ($zsfs as $k=>$txt)
                        <label><input type="radio"  name="zsfs"  value="{{ $k }}"  @if(isset($defaultZsfs) && $defaultZsfs == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
{{--            <tr>--}}
{{--                <th>发票类型<span class="must">*</span></th>--}}
{{--                <td>--}}
{{--                    @foreach ($itype as $k=>$txt)--}}
{{--                        <label><input type="radio"  name="itype"  value="{{ $k }}"  @if(isset($defaultItype) && $defaultItype == $k) checked="checked"  @endif />{{ $txt }} </label>--}}
{{--                    @endforeach--}}
{{--                </td>--}}
{{--            </tr>--}}
            <tr>
                <th>特殊票种标识<span class="must">*</span></th>
                <td>
                    @foreach ($tspz as $k=>$txt)
                        <label><input type="radio"  name="tspz"  value="{{ $k }}"  @if(isset($defaultTspz) && $defaultTspz == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>开票人<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="kpr" value="{{ $info['kpr'] ?? '' }}" placeholder="请输入开票人"/>
                </td>
            </tr>
            <tr>
                <th>收款人<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="skr" value="{{ $info['skr'] ?? '' }}" placeholder="请输入收款人"/>
                </td>
            </tr>
            <tr>
                <th>复核人<span class="must"></span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="fhr" value="{{ $info['fhr'] ?? '' }}" placeholder="请输入复核人"/>
                </td>
            </tr>
            <tr>
                <th>备注<span class="must"></span></th>
                <td>
                    <textarea name="bz" placeholder="请输入备注" class="layui-textarea">{{ replace_enter_char($info['bz'] ?? '',2) }}</textarea>

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
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlInvoiceTemplateedit";

    var SAVE_URL = "{{ url('api/admin/invoice_template/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/invoice_template')}}";//保存成功后跳转到的地址

</script>
<script src="{{ asset('/js/admin/QualityControl/InvoiceTemplate_edit.js') }}?3"  type="text/javascript"></script>
</body>
</html>
