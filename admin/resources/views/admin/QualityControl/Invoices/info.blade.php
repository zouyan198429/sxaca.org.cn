

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
{{--    <form class="am-form am-form-horizontal" method="post"  id="addForm"  onsubmit="return false;">--}}
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr >
                <th>电子发票<span class="must"></span></th>
                <td class="baguetteBoxOne gallery"  id="resource_block">
                    <span class="resource_list"  style="display: none;">@json($info['resource_list'] ?? [])</span>
                    <span  class="resource_show"></span>
                </td>
            </tr>
            <tr >
                <th>交付二维码<span class="must"></span></th>
                <td>
                   <div id="qrcode" data-qrcodeurl="{{ $info['qrcodeurl'] ?? '' }}"></div>
                </td>
            </tr>
            <tr  @if (isset($hidden_option) && ($hidden_option & 1) == 1 ) style="display: none;"  @endif>
                <th>所属企业<span class="must"></span></th>
                <td>
                    <input type="hidden" class="select_id"  name="company_id"  value="{{ $info['company_id'] ?? '' }}" />
                    {{ $info['user_company_name'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>销售方名称<span class="must"></span></th>
                <td>
                    {{ $info['invoice_seller_history']['xsf_mc'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>销售方地址<span class="must"></span></th>
                <td>
                    {{ $info['invoice_seller_history']['xsf_dz'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>销售方电话<span class="must"></span></th>
                <td>
                    {{ $info['invoice_seller_history']['xsf_dh'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>销售方纳税人识别号<span class="must"></span></th>
                <td>
                    {{ $info['invoice_seller_history']['xsf_nsrsbh'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>销售方银行<span class="must"></span></th>
                <td>
                    {{ $info['invoice_seller_history']['xsf_yh'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>销售方银行账号<span class="must"></span></th>
                <td>
                    {{ $info['invoice_seller_history']['xsf_yhzh'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>购买方名称[公司或个人]<span class="must"></span></th>
                <td>
                    {{ $info['invoice_buyer_history']['gmf_mc'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>购买方纳税人识别号<span class="must"></span></th>
                <td>
                    {{ $info['invoice_buyer_history']['gmf_nsrsbh'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>购买方地址<span class="must"></span></th>
                <td>
                    {{ $info['invoice_buyer_history']['gmf_dz'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>购买方电话<span class="must"></span></th>
                <td>
                    {{ $info['invoice_buyer_history']['gmf_dh'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>购买方银行<span class="must"></span></th>
                <td>
                    {{ $info['invoice_buyer_history']['gmf_yh'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>购买方银行账号<span class="must"></span></th>
                <td>
                    {{ $info['invoice_buyer_history']['gmf_yhzh'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>手机号<span class="must"></span></th>
                <td>
                    {{ $info['invoice_buyer_history']['jff_phone'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>电子邮件<span class="must"></span></th>
                <td>
                    {{ $info['invoice_buyer_history']['jff_email'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>征税方式<span class="must"></span></th>
                <td>
                    {{ $info['invoice_template_history']['zsfs_text'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>发票类型<span class="must"></span></th>
                <td>
                    {{ $info['invoice_template_history']['itype'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>特殊票种标识<span class="must"></span></th>
                <td>
                    {{ $info['invoice_template_history']['tspz'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>开票人<span class="must"></span></th>
                <td>
                    {{ $info['invoice_template_history']['kpr'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>收款人<span class="must"></span></th>
                <td>
                    {{ $info['invoice_template_history']['skr'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>复核人<span class="must"></span></th>
                <td>
                    {{ $info['invoice_template_history']['fhr'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>备注<span class="must"></span></th>
                <td>
                    {!! $info['invoice_template_history']['bz'] ?? '' !!}
                </td>
            </tr>
            <tr>
                <th>业务单据号<span class="must"></span></th>
                <td>
                    {{ $info['order_num'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>开票终端标识<span class="must"></span></th>
                <td>
                    {{ $info['kpzdbs'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>税控设备机器编号<span class="must"></span></th>
                <td>
                    {{ $info['jqbh'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>开票类型<span class="must"></span></th>
                <td>
                    {{ $info['kplx_text'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>发票类型<span class="must"></span></th>
                <td>
                    {{ $info['itype_text'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>特殊票种标识<span class="must"></span></th>
                <td>
                    {{ $info['tspz_text'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>征税方式<span class="must"></span></th>
                <td>
                    {{ $info['zsfs_text'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>扣除额<span class="must"></span></th>
                <td>
                    {{ $info['kce'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>原发票号码<span class="must"></span></th>
                <td>
                    {{ $info['yfp_hm'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>发票号码<span class="must"></span></th>
                <td>
                    {{ $info['fp_hm'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>原发票代码<span class="must"></span></th>
                <td>
                    {{ $info['yfp_dm'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>合计税额【税总额】<span class="must"></span></th>
                <td>
                    {{ $info['hjse'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>合计金额(不含税)<span class="must"></span></th>
                <td>
                    {{ $info['hjje'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>价税合计(含税)<span class="must"></span></th>
                <td>
                    {{ $info['jshj'] ?? '' }}
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
                <th>校验码<span class="must"></span></th>
                <td>
                    {{ $info['jym'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>发票清单PDF文件获取key<span class="must"></span></th>
                <td>
                    {{ $info['pdf_item_key'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>发票PDF文件获取key<span class="must"></span></th>
                <td>
                    {{ $info['pdf_key'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>提取码<span class="must"></span></th>
                <td>
                    {{ $info['ext_code'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>发票请求流水号<span class="must"></span></th>
                <td>
                    {{ $info['fpqqlsh'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>发票密文<span class="must"></span></th>
                <td>
                    {{ $info['fp_mw'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>开票日期<span class="must"></span></th>
                <td>
                    {{ $info['kprq'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>生成时间<span class="must"></span></th>
                <td>
                    {{ $info['create_time'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>提交数据时间<span class="must"></span></th>
                <td>
                    {{ $info['submit_time'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>开票时间<span class="must"></span></th>
                <td>
                    {{ $info['make_time'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>作废时间<span class="must"></span></th>
                <td>
                    {{ $info['closel_time'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th>冲红时间<span class="must"></span></th>
                <td>
                    {{ $info['cancel_time'] ?? '' }}
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

<script src="{{asset('dist/lib/jquery-qrcode-master/jquery.qrcode.min.js')}}"></script>

@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/admin/invoice_buyer/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/invoice_buyer')}}";//保存成功后跳转到的地址
    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlInvoicesInfo";

    // var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业

    var DOWN_FILE_URL = "{{ url('admin/down_file') }}";// 下载
    var DEL_FILE_URL = "{{ url('api/admin/upload/ajax_del') }}";// 删除文件的接口地址
</script>
<link rel="stylesheet" href="{{asset('js/baguetteBox.js/baguetteBox.min.css')}}">
<script src="{{asset('js/baguetteBox.js/baguetteBox.min.js')}}" async></script>
{{--<script src="{{asset('js/baguetteBox.js/highlight.min.js')}}" async></script>--}}
<!-- zui js -->
<script src="{{asset('dist/js/zui.min.js') }}"></script>
<script src="{{ asset('/js/admin/QualityControl/Invoices_info.js') }}?7"  type="text/javascript"></script>
@component('component.upfileincludejsmany')
@endcomponent
</body>
</html>
